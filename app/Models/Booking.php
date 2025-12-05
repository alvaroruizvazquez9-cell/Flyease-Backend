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
        'status', // Reordered 'status' and 'payment_intent_id'
        'payment_intent_id'
    ];

    public function flight()
    {
        return $this->belongsTo(Flight::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}