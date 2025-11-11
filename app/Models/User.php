<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
 
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'firstname',
        'name',
        'google_id',
        'email',
        'is_online',
        'last_seen',
        'phone',
        'location',
        'payment_getways',
        'password',
        'email_verified_at',
        'niveau',
        'role',
        'is_demo',
        'language',
        'status',
        'path_photo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    public function chats()
    {
        return $this->hasMany(Chat::class, 'user1_id')->orWhere('user2_id', $this->id);
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function Payments()
    {
        return $this->hasMany(Payments::class, 'user_id');
    }

    public function lastPayment()
    {
        return $this->hasOne(Payments::class, 'user_id')->latest();
    }
}
