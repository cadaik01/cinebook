<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    /** @var array<int, string> */
    protected $fillable = [
        'user_id', 'movie_id', 'rating', 'comment'
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the user that wrote this review
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the movie that this review is for
     */
    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}