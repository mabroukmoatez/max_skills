<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chapitres extends Model
{
    use HasFactory;

    protected $table = 'chapitres';

    protected $fillable = [ 
        'title', 
        'description',
        'path_banner',
        'path_resume',   
        'timer_hours', 
        'timer_minutes', 
        'timer_seconds',
        'type',
        'order_num',
        'status',
        'user_id',
        'cour_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cours()
    {
        return $this->belongsTo(Cours::class, 'cour_id');
    }

    public function lessons()
    {
        return $this->hasMany(Lessons::class, 'chapitre_id')->orderBy('order_num', 'asc');
    }

}
