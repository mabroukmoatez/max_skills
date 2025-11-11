<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $table = 'chat';
    protected $fillable = ['user_id', 'course_id', 'chapter_id', 'page_type'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'chat_id');
    }
}