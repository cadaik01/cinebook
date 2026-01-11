<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Showtime extends Model
{
    use HasFactory;

    protected $fillable = [
        'movie_id', 'room_id', 'show_date', 'show_time'
    ];

    // Cast date/time attributes so Blade views receive Carbon instances
    protected $casts = [
        'show_date' => 'date',
        'show_time' => 'datetime:H:i A',
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function showtimePrices()
    {
        return $this->hasMany(ShowtimePrice::class);
    }

    public function showtimeSeats()
    {
        return $this->hasMany(ShowtimeSeat::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
