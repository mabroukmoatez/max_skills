<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'file_path',
        'file_type',
        'file_size',
        'mime_type',
        'thumbnail_path',
    ];

    /**
     * Get the message that owns the attachment.
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }
}
