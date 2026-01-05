<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id', 'seat_row', 'seat_number', 'seat_code', 'seat_type_id'
    ];

    public function seatType()
    {
        return $this->belongsTo(SeatType::class);
    }
}
