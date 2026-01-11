<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'showtime_id', 'total_price', 'status', 'payment_status', 'booking_date'
    ];

    protected $casts = [
        'booking_date' => 'datetime',
    ];

    public function bookingSeats()
    {
        return $this->hasMany(BookingSeat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function showtime()
    {
        return $this->belongsTo(Showtime::class);
    }
}
