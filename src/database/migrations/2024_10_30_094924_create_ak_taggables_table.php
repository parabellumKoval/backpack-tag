<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAkTaggablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ak_taggables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tag_id')->constrained('ak_tags')->cascadeOnDelete();
            $table->unsignedBigInteger('taggable_id');
            $table->string('taggable_type');
            $table->timestamps();

            $table->index(['taggable_type', 'taggable_id'], 'ak_taggables_taggable_index');
            $table->index('tag_id');
            $table->unique(['tag_id', 'taggable_id', 'taggable_type'], 'ak_taggables_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ak_taggables');
    }
}
