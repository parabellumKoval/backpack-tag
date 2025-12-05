<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Migrate existing data if not done yet
        $locales = array_keys(config('backpack.crud.locales', ['ru' => 'Russian', 'uk' => 'Ukrainian', 'cs' => 'Czech', 'de' => 'German', 'es' => 'Spanish']));
        
        // Check if data needs migration (value is null)
        $needsMigration = DB::table('ak_tags')->whereNull('value')->exists();
        
        if ($needsMigration) {
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
        }

        // Step 2: Make 'value' and 'label' NOT NULL if they are nullable
        $columns = Schema::getColumnListing('ak_tags');
        
        if (in_array('value', $columns) && in_array('label', $columns)) {
            Schema::table('ak_tags', function (Blueprint $table) {
                $table->string('value', 255)->nullable(false)->change();
                $table->json('label')->nullable(false)->change();
            });
        }

        // Step 3: Drop old 'text' column if it still exists
        if (Schema::hasColumn('ak_tags', 'text')) {
            Schema::table('ak_tags', function (Blueprint $table) {
                $table->dropColumn('text');
            });
        }

        // Step 4: Add index on new 'value' column if it doesn't exist
        // Check if index exists by querying information_schema
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
        // Step 1: Add back 'text' column if it doesn't exist
        if (!Schema::hasColumn('ak_tags', 'text')) {
            Schema::table('ak_tags', function (Blueprint $table) {
                $table->text('text')->nullable()->after('id');
            });
        }

        // Step 2: Migrate data back from 'value' to 'text'
        DB::table('ak_tags')->orderBy('id')->chunk(100, function ($tags) {
            foreach ($tags as $tag) {
                DB::table('ak_tags')
                    ->where('id', $tag->id)
                    ->update([
                        'text' => $tag->value,
                    ]);
            }
        });

        // Step 3: Make 'text' NOT NULL
        Schema::table('ak_tags', function (Blueprint $table) {
            $table->text('text')->nullable(false)->change();
        });

        // Step 4: Drop value index if exists
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
