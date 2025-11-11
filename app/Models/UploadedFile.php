<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class UploadedFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_id',
        'original_name',
        'filename',
        'path',
        'mime_type',
        'size',
        'payment_method',
        'user_id',
        'course_id',
        'status',
        'notes'
    ];

    protected $casts = [
        'size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the file
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course associated with the file
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the full URL of the file
     */
    public function getUrlAttribute()
    {
        return Storage::url($this->path);
    }

    /**
     * Get human readable file size
     */
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check if file is an image
     */
    public function getIsImageAttribute()
    {
        return in_array($this->mime_type, [
            'image/jpeg',
            'image/jpg', 
            'image/png',
            'image/gif',
            'image/webp'
        ]);
    }

    /**
     * Check if file is a PDF
     */
    public function getIsPdfAttribute()
    {
        return $this->mime_type === 'application/pdf';
    }

    /**
     * Get file icon based on type
     */
    public function getIconAttribute()
    {
        if ($this->is_image) {
            return 'image';
        } elseif ($this->is_pdf) {
            return 'pdf';
        } else {
            return 'file';
        }
    }

    /**
     * Scope for filtering by payment method
     */
    public function scopeByPaymentMethod($query, $paymentMethod)
    {
        return $query->where('payment_method', $paymentMethod);
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Delete file from storage when model is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($file) {
            if (Storage::disk('public')->exists($file->path)) {
                Storage::disk('public')->delete($file->path);
            }
        });
    }
}

