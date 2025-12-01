<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Added this line

class Payment extends Model
{
    use HasFactory; // Added this line

    protected $fillable = [
        'booking_id',
        'amount',
        'payment_method',
        'status',
        'transaction_id',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
