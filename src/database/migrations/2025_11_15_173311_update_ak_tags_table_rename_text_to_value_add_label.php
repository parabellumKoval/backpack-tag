<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * NOTE: This migration is superseded by 2025_11_15_173845_complete_tags_table_update.php
     * It's kept here for reference but will skip if columns already exist.
     */
    public function up(): void
    {
        // Check if migration already done
        if (Schema::hasColumn('ak_tags', 'value') && Schema::hasColumn('ak_tags', 'label')) {
            return; // Already migrated
        }

        // Step 1: Add new 'label' JSON column and temporary 'value' column
        Schema::table('ak_tags', function (Blueprint $table) {
            if (!Schema::hasColumn('ak_tags', 'label')) {
                $table->json('label')->nullable()->after('text');
            }
            if (!Schema::hasColumn('ak_tags', 'value')) {
                $table->string('value', 255)->nullable()->after('label');
            }
        });

        // Step 2: Migrate existing 'text' data to both 'label' (translatable) and 'value' (slug)
        // Get configured languages from config
        $locales = array_keys(config('backpack.crud.locales', ['ru' => 'Russian', 'uk' => 'Ukrainian', 'cs' => 'Czech', 'de' => 'German', 'es' => 'Spanish']));
        
        DB::table('ak_tags')->orderBy('id')->chunk(100, function ($tags) use ($locales) {
            foreach ($tags as $tag) {
                $labelData = [];
                
                // Set the same text for all configured languages
                foreach ($locales as $locale) {
                    $labelData[$locale] = $tag->text;
                }
                
                DB::table('ak_tags')
                    ->where('id', $tag->id)
                    ->update([
                        'label' => json_encode($labelData),
                        'value' => $tag->text, // Use text as value (slug/identifier)
                    ]);
            }
        });

        // Step 3: Make 'value' and 'label' NOT NULL after data migration
        Schema::table('ak_tags', function (Blueprint $table) {
            $table->string('value', 255)->nullable(false)->change();
            $table->json('label')->nullable(false)->change();
        });

        // Step 4: Drop old 'text' column if it still exists
        if (Schema::hasColumn('ak_tags', 'text')) {
            Schema::table('ak_tags', function (Blueprint $table) {
                $table->dropColumn('text');
            });
        }

        // Step 5: Add index on new 'value' column
        $indexExists = DB::select("
            SELECT COUNT(*) as count 
            FROM information_schema.statistics 
            WHERE table_schema = DATABASE() 
            AND table_name = 'ak_tags' 
            AND index_name = 'ak_tags_value_index'
        ");
        
        if ($indexExists[0]->count == 0) {
            Schema::table('ak_tags', function (Blueprint $table) {
                $table->index('value');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Add back 'text' column
        Schema::table('ak_tags', function (Blueprint $table) {
            $table->text('text')->nullable()->after('id');
        });

        // Step 2: Migrate data back from 'value' to 'text'
        DB::table('ak_tags')->orderBy('id')->chunk(100, function ($tags) {
            foreach ($tags as $tag) {
                DB::table('ak_tags')
                    ->where('id', $tag->id)
                    ->update([
                        'text' => $tag->value ?? '',
                    ]);
            }
        });

        // Step 3: Make 'text' NOT NULL
        Schema::table('ak_tags', function (Blueprint $table) {
            $table->text('text')->nullable(false)->change();
        });

        // Step 4: Drop index if exists
        $indexExists = DB::select("
            SELECT COUNT(*) as count 
            FROM information_schema.statistics 
            WHERE table_schema = DATABASE() 
            AND table_name = 'ak_tags' 
            AND index_name = 'ak_tags_value_index'
        ");
        
        if ($indexExists[0]->count > 0) {
            Schema::table('ak_tags', function (Blueprint $table) {
                $table->dropIndex(['value']);
            });
        }

        // Step 5: Drop new columns
        Schema::table('ak_tags', function (Blueprint $table) {
            $table->dropColumn(['value', 'label']);
        });
    }
};
