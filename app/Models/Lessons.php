<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lessons extends Model
{
    use HasFactory;

    protected $table = 'lessons';

    protected $fillable = [ 
        'title', 
        'description', 
        'path_icon',
        'path_video',
        'path_projet',
        'lessonVideoHours',
        'lessonVideoMinutes',
        'lessonVideoSeconds',
        'type', 
        'status',
        'visibility',
        'user_id',
        'chapitre_id',
        'order_num',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function chapitre()
    {
        return $this->belongsTo(Chapitres::class, 'chapitre_id');
    }

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    
    public function urls()
    {
        return $this->hasMany(UrlLesson::class, 'lesson_id');
    }
}
