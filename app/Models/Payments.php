<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payments extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [ 
        'methode', 
        'img_path', 
        'online_key', 
        'status',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getStatusLabelAttribute()
    {
        switch ($this->status) {
            case 0:
                return 'Vérification';
            case 1:
                return 'Payé';
            case 2:
                return 'Expiré';
            default:
                return 'Aucun';
        }
    }
}
