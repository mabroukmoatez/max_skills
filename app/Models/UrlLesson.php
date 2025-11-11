<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UrlLesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'title',
        'url',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lessons::class);
    }
}
