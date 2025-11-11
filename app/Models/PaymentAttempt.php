<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'konnect_payment_ref',
        'order_id',
        'amount',
        'course_id',
        'email',
        'first_name',
        'last_name',
        'phone_number',
        'status',
        'konnect_pay_url',
    ];
}