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

    // ==================== SCOPES ====================

    /**
     * Scope to get reviews sorted by latest
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope to get reviews sorted by highest rating
     */
    public function scopeHighestRated($query)
    {
        return $query->orderBy('rating', 'desc')->orderBy('created_at', 'desc');
    }
}