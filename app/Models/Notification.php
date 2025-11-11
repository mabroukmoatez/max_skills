<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'reciver_id',
        'title',
        'message',
        'status',
    ];

    /**
     * The user who sent the notification.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * The user who received the notification.
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'reciver_id');
    }
}