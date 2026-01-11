<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingSeat extends Model
{
    use HasFactory;

    protected $fillable = ['booking_id', 'seat_id', 'price'];

    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
