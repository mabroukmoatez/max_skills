<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         Schema::create('cours', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('keyword');
            $table->string('top_bar');
            $table->string('button');
            $table->decimal('price_init', 10, 3);
            $table->decimal('price_promo', 10, 3)->nullable();
            $table->text('description');
            $table->string('path_banner')->nullable();
            $table->tinyInteger('visibility')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->enum('language', ['fr', 'en', 'ar'])->default('fr'); 

            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cours');
    }
};
