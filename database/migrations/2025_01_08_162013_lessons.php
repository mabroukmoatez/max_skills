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
         Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->text('path_icon');
            $table->text('path_video');
            $table->text('path_projet');
            $table->unsignedInteger('lessonVideoHours')->nullable();
            $table->unsignedInteger('lessonVideoMinutes')->nullable();
            $table->unsignedInteger('lessonVideoSeconds')->nullable();
            $table->enum('type', ['lesson', 'test', 'test_final','projet'])->default('lesson'); 
            $table->tinyInteger('visibility')->default(0);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('chapitre_id');
            $table->bigInteger('order_num')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('chapitre_id')->references('id')->on('chapitres');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
