<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShowtimeSeat extends Model
{
    use HasFactory;

    protected $fillable = ['showtime_id', 'seat_id', 'status'];

    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }
}
