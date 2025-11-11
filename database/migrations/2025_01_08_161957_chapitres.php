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
         Schema::create('chapitres', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description')->nullable();
            $table->string('path_banner');
            $table->string('path_resume')->nullable();
            $table->unsignedInteger('timer_hours')->nullable();
            $table->unsignedInteger('timer_minutes')->nullable();
            $table->unsignedInteger('timer_seconds')->nullable();
            $table->enum('type', ['chapitre', 'certifica'])->default('chapitre'); 
            $table->tinyInteger('status')->default(1);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('cour_id');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('cour_id')->references('id')->on('cours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chapitres');
    }
};
