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
        Schema::table('chat', function (Blueprint $table) {
            $table->dropForeign(['cour_id']);
            $table->dropColumn('cour_id');
            $table->unsignedBigInteger('course_id')->nullable();
            $table->unsignedBigInteger('chapter_id')->nullable();
            $table->string('page_type');


            $table->foreign('course_id')->references('id')->on('cours')->onDelete('cascade');
            $table->foreign('chapter_id')->references('id')->on('chapitres')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat', function (Blueprint $table) {
            $table->dropForeign(['cour_id']);
            $table->dropColumn('cour_id');
            $table->unsignedBigInteger('lesson_id')->after('user_id');
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
        });
    }
};
