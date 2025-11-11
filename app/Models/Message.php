<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_id',
        'sender_id',
        'reply_to_id',
        'is_admin',
        'message',
        'type',
        'file_name',
        'file_size',
        'file_type',
        'page_type',
        'page_id',
        'readed',
        'sent_at',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'readed' => 'boolean',
        'sent_at' => 'datetime',
        'file_size' => 'integer',
    ];

    /**
     * Get the chat that owns the message.
     */
    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    /**
     * Get the sender of the message.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the message this is replying to.
     */
    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'reply_to_id');
    } 

    /**
     * Get all replies to this message.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Message::class, 'reply_to_id');
    }

    /**
     * Get the attachments for this message.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(MessageAttachment::class);
    }
}
