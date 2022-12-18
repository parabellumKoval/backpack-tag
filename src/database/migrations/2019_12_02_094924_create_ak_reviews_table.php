<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAkReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ak_reviews', function (Blueprint $table) {
            $table->id();
            
            $table->integer('owner_id')->nullable();
            $table->boolean('is_moderated')->default(0);

            $table->text('text');
            $table->json('extras')->nullable();
            $table->integer('rating')->nullable();
            $table->integer('likes')->default(0);
            $table->integer('dislikes')->default(0);
            
            $table->integer('parent_id')->default(0)->nullable();
            $table->integer('lft')->default(0)->nullable();
            $table->integer('rgt')->default(0)->nullable();
            $table->integer('depth')->default(0)->nullable();
            $table->morphs('reviewable');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ak_reviews');
    }
}
