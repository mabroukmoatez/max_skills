<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids; 
use Illuminate\Support\Str;

class Cours extends Model
{
    use HasFactory, HasUuids; // Use the trait

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cours';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [ 
        'id',
        'title', 
        'keyword', 
        'top_bar', 
        'button', 
        'price_init', 
        'price_promo', 
        'description', 
        'path_banner', 
        'path_resume',
        'visibility', 
        'status', 
        'language', 
        'user_id',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
