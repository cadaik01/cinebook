<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'genre', 'language', 'director', 'cast', 'duration',
        'release_date', 'age_rating', 'status', 'poster_url', 'trailer_url',
        'description', 'rating_avg'
    ];
}
