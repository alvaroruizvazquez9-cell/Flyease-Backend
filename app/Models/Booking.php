<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id', 
        'flight_id', 
        'booking_reference', 
        'passengers',
        'total_price', 
        'payment_intent_id', 
        'status'
    ];

    public function user() { 
        return $this->belongsTo(User::class); 
    }

    public function flight() { 
        return $this->belongsTo(Flight::class); 
    }
}