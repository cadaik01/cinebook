<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShowtimePrice extends Model
{
    use HasFactory;

    protected $fillable = ['showtime_id', 'seat_type_id', 'peak_hour_price'];
}
