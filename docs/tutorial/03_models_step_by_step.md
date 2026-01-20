# 03. TẠO MODELS TỪNG BƯỚC

## 🎯 Mục tiêu bài học

Sau bài học này, bạn sẽ có:
- ✅ 13 Laravel Models hoàn chỉnh
- ✅ Relationships được định nghĩa
- ✅ Methods tiện ích trong Models
- ✅ Hiểu về Eloquent ORM

**Thời gian ước tính**: 60-75 phút

---

## 📚 Eloquent ORM là gì?

**Eloquent** là ORM (Object-Relational Mapping) của Laravel:
- Mỗi table → 1 Model class
- Mỗi row → 1 object instance
- Query database bằng PHP methods thay vì raw SQL

**Ví dụ**:
```php
// Raw SQL
$users = DB::select('SELECT * FROM users WHERE role = ?', ['admin']);

// Eloquent
$users = User::where('role', 'admin')->get();
```

---

## 🛠️ BƯỚC 1: TẠO MODEL USER

### 1.1. Generate Model

```bash
php artisan make:model User
```

⚠️ **Lưu ý**: Laravel đã có sẵn User model, nên command trên sẽ báo lỗi. Chúng ta sẽ chỉnh sửa file hiện có.

### 1.2. Edit Model User

**File**: `app/Models/User.php`

Thay toàn bộ nội dung bằng:

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'city',
        'avatar_url',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Get all bookings for this user.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get all reviews written by this user.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is regular user.
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Get user's display name.
     */
    public function getDisplayName(): string
    {
        return $this->name ?? 'Guest';
    }

    /**
     * Get total bookings count.
     */
    public function getTotalBookings(): int
    {
        return $this->bookings()->count();
    }

    /**
     * Get upcoming bookings (future showtimes).
     */
    public function getUpcomingBookings()
    {
        return $this->bookings()
            ->where('status', 'confirmed')
            ->whereHas('showtime', function ($query) {
                $query->where('show_date', '>=', now()->toDateString());
            })
            ->with(['showtime.movie', 'showtime.room'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get past bookings.
     */
    public function getPastBookings()
    {
        return $this->bookings()
            ->whereHas('showtime', function ($query) {
                $query->where('show_date', '<', now()->toDateString());
            })
            ->with(['showtime.movie', 'showtime.room'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
```

📝 **Giải thích**:

- `protected $fillable`: Các column được phép mass assignment
- `protected $hidden`: Các column không hiện trong JSON response
- `hasMany()`: Định nghĩa quan hệ 1-n
- Helper methods: Các hàm tiện ích để dùng trong code

---

## 🛠️ BƯỚC 2: TẠO CÁC LOOKUP MODELS

### 2.1. Genre Model

```bash
php artisan make:model Genre
```

**File**: `app/Models/Genre.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    protected $table = 'genres';

    protected $fillable = ['name'];

    public $timestamps = false; // No updated_at column

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Get all movies in this genre.
     */
    public function movies()
    {
        return $this->belongsToMany(
            Movie::class,
            'movie_genres',
            'genre_id',
            'movie_id'
        );
    }
}
```

### 2.2. ScreenType Model

```bash
php artisan make:model ScreenType
```

**File**: `app/Models/ScreenType.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScreenType extends Model
{
    protected $table = 'screen_types';

    protected $fillable = ['name', 'price'];

    public $timestamps = false;

    protected $casts = [
        'price' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
```

### 2.3. SeatType Model

```bash
php artisan make:model SeatType
```

**File**: `app/Models/SeatType.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeatType extends Model
{
    protected $table = 'seat_types';

    protected $fillable = ['name', 'base_price', 'description'];

    public $timestamps = false;

    protected $casts = [
        'base_price' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    // Seat type constants
    const STANDARD = 1;
    const VIP = 2;
    const COUPLE = 3;

    // ========================================
    // RELATIONSHIPS
    // ========================================

    public function seats()
    {
        return $this->hasMany(Seat::class);
    }

    public function showtimePrices()
    {
        return $this->hasMany(ShowtimePrice::class);
    }
}
```

---

## 🛠️ BƯỚC 3: TẠO MOVIE MODEL

```bash
php artisan make:model Movie
```

**File**: `app/Models/Movie.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $table = 'movies';

    protected $fillable = [
        'title',
        'description',
        'director',
        'cast',
        'language',
        'duration',
        'release_date',
        'age_rating',
        'status',
        'poster_url',
        'trailer_url',
        'rating_avg',
    ];

    protected $casts = [
        'release_date' => 'date',
        'rating_avg' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Get all genres for this movie (many-to-many).
     */
    public function genres()
    {
        return $this->belongsToMany(
            Genre::class,
            'movie_genres',
            'movie_id',
            'genre_id'
        );
    }

    /**
     * Get all showtimes for this movie.
     */
    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }

    /**
     * Get all reviews for this movie.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // ========================================
    // ACCESSORS & MUTATORS
    // ========================================

    /**
     * Get genres as comma-separated string.
     */
    public function getGenresStringAttribute(): string
    {
        return $this->genres->pluck('name')->join(', ');
    }

    /**
     * Get human-readable duration.
     */
    public function getDurationFormattedAttribute(): string
    {
        $hours = floor($this->duration / 60);
        $minutes = $this->duration % 60;
        return "{$hours}h {$minutes}m";
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Update average rating from reviews.
     */
    public function updateAverageRating(): void
    {
        $avg = $this->reviews()->avg('rating');
        $this->update(['rating_avg' => round($avg, 2)]);
    }

    /**
     * Check if movie is now showing.
     */
    public function isNowShowing(): bool
    {
        return $this->status === 'now_showing';
    }

    /**
     * Check if movie is coming soon.
     */
    public function isComingSoon(): bool
    {
        return $this->status === 'coming_soon';
    }

    /**
     * Get showtimes for specific date.
     */
    public function getShowtimesForDate($date)
    {
        return $this->showtimes()
            ->where('show_date', $date)
            ->with('room')
            ->orderBy('show_time')
            ->get();
    }
}
```

---

## 🛠️ BƯỚC 4: TẠO ROOM & SEAT MODELS

### 4.1. Room Model

```bash
php artisan make:model Room
```

**File**: `app/Models/Room.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $table = 'rooms';

    protected $fillable = [
        'name',
        'total_rows',
        'seats_per_row',
        'screen_type_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    public function screenType()
    {
        return $this->belongsTo(ScreenType::class);
    }

    public function seats()
    {
        return $this->hasMany(Seat::class);
    }

    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Get total seat count.
     */
    public function getTotalSeats(): int
    {
        return $this->seats()->count();
    }

    /**
     * Get seat layout (grouped by row).
     */
    public function getSeatLayout()
    {
        return $this->seats()
            ->orderBy('seat_row')
            ->orderBy('seat_number')
            ->get()
            ->groupBy('seat_row');
    }
}
```

### 4.2. Seat Model

```bash
php artisan make:model Seat
```

**File**: `app/Models/Seat.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    protected $table = 'seats';

    protected $fillable = [
        'room_id',
        'seat_row',
        'seat_number',
        'seat_code',
        'seat_type_id',
    ];

    public $timestamps = false;

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function seatType()
    {
        return $this->belongsTo(SeatType::class);
    }

    public function showtimeSeats()
    {
        return $this->hasMany(ShowtimeSeat::class);
    }

    public function bookingSeats()
    {
        return $this->hasMany(BookingSeat::class);
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Check if this is a couple seat.
     */
    public function isCoupleSeat(): bool
    {
        return $this->seat_type_id === SeatType::COUPLE;
    }

    /**
     * Check if this is a VIP seat.
     */
    public function isVIPSeat(): bool
    {
        return $this->seat_type_id === SeatType::VIP;
    }

    /**
     * Get couple pair seat ID (if couple seat).
     */
    public function getCouplePairId(): ?int
    {
        if (!$this->isCoupleSeat()) {
            return null;
        }

        // Couple seats are in pairs: odd with even (1-2, 3-4, 5-6, etc.)
        $isOdd = $this->seat_number % 2 === 1;
        $pairNumber = $isOdd ? $this->seat_number + 1 : $this->seat_number - 1;

        $pair = Seat::where('room_id', $this->room_id)
            ->where('seat_row', $this->seat_row)
            ->where('seat_number', $pairNumber)
            ->first();

        return $pair ? $pair->id : null;
    }
}
```

---

## 🛠️ BƯỚC 5: TẠO SHOWTIME MODELS

### 5.1. Showtime Model

```bash
php artisan make:model Showtime
```

**File**: `app/Models/Showtime.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Showtime extends Model
{
    protected $table = 'showtimes';

    protected $fillable = [
        'movie_id',
        'room_id',
        'show_date',
        'show_time',
    ];

    protected $casts = [
        'show_date' => 'date',
        'show_time' => 'datetime:H:i',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

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

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Get formatted date & time.
     */
    public function getFormattedDateTime(): string
    {
        return $this->show_date->format('d/m/Y') . ' - ' .
               $this->show_time->format('H:i');
    }

    /**
     * Get available seats count.
     */
    public function getAvailableSeatsCount(): int
    {
        return $this->showtimeSeats()
            ->where('status', 'available')
            ->count();
    }

    /**
     * Check if showtime is in the past.
     */
    public function isPast(): bool
    {
        $showtimeDateTime = $this->show_date->setTimeFromTimeString($this->show_time->format('H:i:s'));
        return $showtimeDateTime->isPast();
    }

    /**
     * Get price for specific seat type.
     */
    public function getPriceForSeatType($seatTypeId): float
    {
        $price = $this->showtimePrices()
            ->where('seat_type_id', $seatTypeId)
            ->first();

        return $price ? (float) $price->price : 0.0;
    }
}
```

### 5.2. ShowtimePrice Model

```bash
php artisan make:model ShowtimePrice
```

**File**: `app/Models/ShowtimePrice.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShowtimePrice extends Model
{
    protected $table = 'showtime_prices';

    protected $fillable = [
        'showtime_id',
        'seat_type_id',
        'price',
    ];

    public $timestamps = false;

    protected $casts = [
        'price' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    public function showtime()
    {
        return $this->belongsTo(Showtime::class);
    }

    public function seatType()
    {
        return $this->belongsTo(SeatType::class);
    }
}
```

### 5.3. ShowtimeSeat Model

```bash
php artisan make:model ShowtimeSeat
```

**File**: `app/Models/ShowtimeSeat.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShowtimeSeat extends Model
{
    protected $table = 'showtime_seats';

    protected $fillable = [
        'showtime_id',
        'seat_id',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    public function showtime()
    {
        return $this->belongsTo(Showtime::class);
    }

    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function isBooked(): bool
    {
        return $this->status === 'booked';
    }

    public function isReserved(): bool
    {
        return $this->status === 'reserved';
    }
}
```

---

## 🛠️ BƯỚC 6: TẠO BOOKING MODELS

### 6.1. Booking Model

```bash
php artisan make:model Booking
```

**File**: `app/Models/Booking.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'bookings';

    protected $fillable = [
        'user_id',
        'showtime_id',
        'booking_date',
        'total_price',
        'status',
        'payment_method',
        'payment_status',
        'expired_at',
    ];

    protected $casts = [
        'booking_date' => 'datetime',
        'expired_at' => 'datetime',
        'total_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function showtime()
    {
        return $this->belongsTo(Showtime::class);
    }

    public function bookingSeats()
    {
        return $this->hasMany(BookingSeat::class);
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' ||
               ($this->expired_at && $this->expired_at->isPast());
    }

    /**
     * Get total tickets count.
     */
    public function getTotalTickets(): int
    {
        return $this->bookingSeats()->count();
    }
}
```

### 6.2. BookingSeat Model

```bash
php artisan make:model BookingSeat
```

**File**: `app/Models/BookingSeat.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BookingSeat extends Model
{
    protected $table = 'booking_seats';

    protected $fillable = [
        'booking_id',
        'showtime_id',
        'seat_id',
        'price',
        'qr_code',
        'qr_status',
        'checked_at',
    ];

    public $timestamps = false;

    protected $casts = [
        'price' => 'decimal:2',
        'checked_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function showtime()
    {
        return $this->belongsTo(Showtime::class);
    }

    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }

    // ========================================
    // QR CODE METHODS
    // ========================================

    /**
     * Generate unique QR code.
     */
    public static function generateQRCode($bookingId, $seatInfo): string
    {
        $data = $bookingId . '_' . $seatInfo . '_' . microtime(true);
        return hash('sha256', $data);
    }

    /**
     * Validate QR code.
     */
    public static function validateQRCode($qrCode)
    {
        return self::where('qr_code', $qrCode)->first();
    }

    /**
     * Check-in with QR code.
     */
    public function checkIn(): bool
    {
        if ($this->qr_status !== 'active') {
            return false;
        }

        $this->update([
            'qr_status' => 'checked',
            'checked_at' => now(),
        ]);

        return true;
    }
}
```

---

## 🛠️ BƯỚC 7: TẠO REVIEW MODEL

```bash
php artisan make:model Review
```

**File**: `app/Models/Review.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = 'reviews';

    protected $fillable = [
        'user_id',
        'movie_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope: Latest reviews first.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope: Highest rated first.
     */
    public function scopeHighestRated($query)
    {
        return $query->orderBy('rating', 'desc');
    }

    // ========================================
    // EVENTS
    // ========================================

    protected static function booted()
    {
        // Update movie rating when review created
        static::created(function ($review) {
            $review->movie->updateAverageRating();
        });

        // Update movie rating when review updated
        static::updated(function ($review) {
            $review->movie->updateAverageRating();
        });

        // Update movie rating when review deleted
        static::deleted(function ($review) {
            $review->movie->updateAverageRating();
        });
    }
}
```

---

## ✅ TEST MODELS

### Test trong Tinker

```bash
php artisan tinker
```

**Test queries**:
```php
// Test User model
$user = User::find(1);
echo $user->name;

// Test relationships
$user->bookings; // Get all bookings
$user->reviews;  // Get all reviews

// Test Movie model
$movie = Movie::find(1);
$movie->genres; // Get all genres (n-n relationship)

// Test helper methods
$user->isAdmin();
$movie->getGenresStringAttribute();

// Test scopes
Review::latest()->take(5)->get();

// Exit tinker
exit;
```

---

## 📝 TÓM TẮT

Đã tạo 13 Models:
1. User
2. Genre
3. ScreenType
4. SeatType
5. Movie
6. Room
7. Seat
8. Showtime
9. ShowtimePrice
10. ShowtimeSeat
11. Booking
12. BookingSeat
13. Review

**Bài tiếp**: [04. Authentication →](04_authentication.md)

---

**Bài trước**: [← 02. Database Design](02_database_design.md)
**Series**: Cinebook Tutorial
