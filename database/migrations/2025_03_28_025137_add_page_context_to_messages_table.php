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
        Schema::table('messages', function (Blueprint $table) {
            $table->string('page_type')->after('message'); // 'course', 'chapter', 'lesson'
            $table->unsignedBigInteger('page_id')->after('page_type'); // ID of the course, chapter, or lesson
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['page_type', 'page_id']);
        });
    }
};
