<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Booking;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'city',
        'phone',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ==================== RELATIONSHIPS ====================
    
    /**
     * Get all bookings for this user
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // ==================== ROLE CHECKERS ====================
    
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
    
    public function isUser()
    {
        return $this->role === 'user';
    }
    
    public function isGuest()
    {
        return $this->role === 'guest';
    }

    // ==================== PROFILE METHODS ====================
    /**
     * Get User Information
     */
    public function getUserInfo()
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'phone' => $this->phone,
            'city' => $this->city,
            'created_at' => $this->created_at,
        ];
    }

    /**
     * Get user's booking history with movie details
     * Sorted by newest first
     */
    public function getBookingHistory()
    {
        return $this->bookings()
            ->with(['showtime.movie', 'showtime.room', 'bookingSeats.seat'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get user's recent bookings (last 5)
     */
    public function getRecentBookings($limit = 5)
    {
        return $this->bookings()
            ->with(['showtime.movie'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get total amount user has spent on bookings
     */
    public function getTotalSpent()
    {
        return $this->bookings()
            ->where('payment_status', 'completed')
            ->sum('total_price');
    }

    /**
     * Count total bookings
     */
    public function getTotalBookings()
    {
        return $this->bookings()->count();
    }

    /**
     * Get user's profile statistics
     */
    public function getProfileStats()
    {
        return [
            'total_bookings' => $this->getTotalBookings(),
            'total_spent' => $this->getTotalSpent(),
            'member_since' => $this->created_at->format('M Y'),
            'last_booking' => $this->bookings()->latest()->first(),
        ];
    }

    /**
     * Get user's upcoming bookings (showtimes in the future)
     */
    public function getUpcomingBookings()
    {
        return $this->bookings()
            ->whereHas('showtime', function($query) {
                $query->where('show_date', '>=', now()->toDateString())
                      ->orWhere(function($q) {
                          $q->where('show_date', '=', now()->toDateString())
                            ->where('show_time', '>', now()->toTimeString());
                      });
            })
            ->with(['showtime.movie', 'showtime.room'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get user's past bookings
     */
    public function getPastBookings()
    {
        return $this->bookings()
            ->whereHas('showtime', function($query) {
                $query->where('show_date', '<', now()->toDateString())
                      ->orWhere(function($q) {
                          $q->where('show_date', '=', now()->toDateString())
                            ->where('show_time', '<=', now()->toTimeString());
                      });
            })
            ->with(['showtime.movie', 'showtime.room'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
