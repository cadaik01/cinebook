# 11. ADMIN PANEL - QUẢN TRỊ HỆ THỐNG

## 🎯 Mục tiêu bài học

Sau bài học này, bạn sẽ có:
- ✅ Admin panel hoàn chỉnh với dashboard thống kê
- ✅ CRUD quản lý Movies, Rooms, Showtimes
- ✅ Quản lý Users, Bookings, Reviews
- ✅ QR Check-in system cho staff
- ✅ Role-based access control (Admin vs User)
- ✅ Admin layout riêng biệt
- ✅ Real-time statistics & analytics

**Thời gian ước tính**: 120-150 phút

---

## 📚 Kiến thức cần biết

- Laravel Resource Controllers
- Middleware & Authorization
- Database Transactions
- Eloquent Relationships (eager loading)
- JavaScript Fetch API
- Admin UI/UX patterns

---

## 🗂️ KIẾN TRÚC ADMIN PANEL

### Cấu trúc thư mục

```
app/Http/Controllers/Admin/
├── AdminDashboardController.php    # Dashboard & statistics
├── AdminMovieController.php        # CRUD movies
├── AdminRoomController.php         # CRUD rooms & seats
├── AdminShowtimeController.php     # CRUD showtimes
├── AdminBookingController.php      # View & manage bookings
├── AdminUserController.php         # Manage users & roles
├── AdminReviewController.php       # Moderate reviews
└── QRCheckInController.php         # QR check-in system

resources/views/admin/
├── layouts/
│   └── admin.blade.php             # Admin layout template
├── dashboard.blade.php             # Dashboard page
├── movies/                         # Movie management views
├── rooms/                          # Room management views
├── showtimes/                      # Showtime management views
├── bookings/                       # Booking management views
├── users/                          # User management views
├── reviews/                        # Review moderation views
└── qr_checkin/                     # QR check-in views

public/js/
└── qr_checkin.js                   # QR check-in JavaScript
```

---

## 🛠️ BƯỚC 1: TẠO ADMIN LAYOUT

### 1.1. Admin Layout Template

**File**: `resources/views/admin/layouts/admin.blade.php`

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel - TCA Cine')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Admin Custom CSS -->
    @vite(['resources/css/admin_layout.css'])

    @yield('extra-css')
</head>
<body>
    <div class="admin-layout-wrapper">

        <!-- Sidebar Navigation -->
        <aside class="admin-layout-sidebar">
            <div class="admin-layout-sidebar-header">
                <h3>
                    <i class="fas fa-film"></i>
                    TCA Cine Admin
                </h3>
            </div>

            <nav class="admin-layout-sidebar-nav">
                <ul class="admin-layout-sidebar-menu">

                    <!-- Dashboard -->
                    <li class="admin-layout-sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}" class="admin-layout-sidebar-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <!-- Movies -->
                    <li class="admin-layout-sidebar-item {{ request()->routeIs('admin.movies.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.movies.index') }}" class="admin-layout-sidebar-link">
                            <i class="fas fa-film"></i>
                            <span>Movies</span>
                        </a>
                    </li>

                    <!-- Rooms -->
                    <li class="admin-layout-sidebar-item {{ request()->routeIs('admin.rooms.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.rooms.index') }}" class="admin-layout-sidebar-link">
                            <i class="fas fa-door-open"></i>
                            <span>Rooms</span>
                        </a>
                    </li>

                    <!-- Showtimes -->
                    <li class="admin-layout-sidebar-item {{ request()->routeIs('admin.showtimes.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.showtimes.index') }}" class="admin-layout-sidebar-link">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Showtimes</span>
                        </a>
                    </li>

                    <!-- Divider -->
                    <li class="sidebar-divider"></li>

                    <!-- Bookings -->
                    <li class="admin-layout-sidebar-item {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.bookings.index') }}" class="admin-layout-sidebar-link">
                            <i class="fas fa-ticket-alt"></i>
                            <span>Bookings</span>
                        </a>
                    </li>

                    <!-- Users -->
                    <li class="admin-layout-sidebar-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.users.index') }}" class="admin-layout-sidebar-link">
                            <i class="fas fa-users"></i>
                            <span>Users</span>
                        </a>
                    </li>

                    <!-- Reviews -->
                    <li class="admin-layout-sidebar-item {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.reviews.index') }}" class="admin-layout-sidebar-link">
                            <i class="fas fa-star"></i>
                            <span>Reviews</span>
                        </a>
                    </li>

                    <!-- Divider -->
                    <li class="sidebar-divider"></li>

                    <!-- QR Check-in -->
                    <li class="admin-layout-sidebar-item {{ request()->routeIs('admin.qr.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.qr.index') }}" class="admin-layout-sidebar-link">
                            <i class="fas fa-qrcode"></i>
                            <span>QR Check-in</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <div class="admin-layout-main">

            <!-- Top Navbar -->
            <nav class="navbar navbar-expand-lg admin-layout-navbar">
                <div class="container-fluid">

                    <button class="btn btn-link sidebar-toggle" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>

                    <span class="navbar-text navbar-title">
                        @yield('page-title', 'Dashboard')
                    </span>

                    <div class="navbar-nav ms-auto">
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown"
                               role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle"></i>
                                <span class="ms-2">{{ Auth::user()->name ?? 'Admin' }}</span>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('homepage') }}">
                                        <i class="fas fa-home me-2"></i>
                                        Back to Site
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i>
                                            Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="admin-layout-content">
                <div class="container-fluid">

                    <!-- Alert Messages -->
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    <!-- Page Content -->
                    @yield('content')
                </div>
            </main>

            <!-- Footer -->
            <footer class="admin-footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="text-muted mb-0">© 2026 TCA Cine Admin Panel</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <p class="text-muted mb-0">Version 1.0.0</p>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Sidebar Toggle Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const wrapper = document.querySelector('.admin-layout-wrapper');

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                wrapper.classList.toggle('sidebar-collapsed');
            });
        }
    });
    </script>

    @yield('extra-js')
</body>
</html>
```

**Giải thích**:
- **Sidebar navigation**: Menu dọc với tất cả admin sections
- **Top navbar**: User dropdown, breadcrumbs
- **Alert messages**: Success/error notifications
- **Responsive**: Sidebar toggle cho mobile
- **@yield sections**: title, content, extra-css, extra-js

---

## 🛠️ BƯỚC 2: ADMIN DASHBOARD CONTROLLER

### 2.1. Dashboard với Statistics

**File**: `app/Http/Controllers/Admin/AdminDashboardController.php`

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Booking;
use App\Models\User;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Basic stats
        $totalUsers = User::where('role', 'user')->count();
        $totalMovies = Movie::count();

        // Today's stats
        $today = Carbon::today();
        $ticketsSoldToday = DB::table('booking_seats')
            ->join('bookings', 'booking_seats.booking_id', '=', 'bookings.id')
            ->whereDate('bookings.created_at', $today)
            ->where('bookings.payment_status', 'paid')
            ->count();

        $revenueToday = Booking::whereDate('created_at', $today)
            ->where('payment_status', 'paid')
            ->sum('total_price');

        // This month's revenue
        $revenueThisMonth = Booking::whereYear('created_at', $today->year)
            ->whereMonth('created_at', $today->month)
            ->where('payment_status', 'paid')
            ->sum('total_price');

        // Active showtimes (future showtimes)
        $activeShowtimes = Showtime::where('show_date', '>=', $today)->count();

        // Movie revenue analytics
        $movieRevenue = Movie::leftJoin('showtimes', 'movies.id', '=', 'showtimes.movie_id')
            ->leftJoin('bookings', function ($join) {
                $join->on('showtimes.id', '=', 'bookings.showtime_id')
                    ->where('bookings.payment_status', 'paid');
            })
            ->select('movies.id', 'movies.title',
                     DB::raw('COALESCE(SUM(bookings.total_price), 0) as revenue'))
            ->groupBy('movies.id', 'movies.title')
            ->orderBy('revenue', 'desc')
            ->get();

        $highestRevenueMovie = $movieRevenue->first();
        $lowestRevenueMovie = $movieRevenue->last();

        // Recent bookings
        $recentBookings = Booking::with(['user', 'showtime.movie', 'bookingSeats'])
            ->latest()
            ->take(10)
            ->get();

        // Movie stats by status
        $nowShowingMovies = Movie::where('status', 'now_showing')->count();
        $comingSoonMovies = Movie::where('status', 'coming_soon')->count();
        $totalRevenue = Booking::where('payment_status', 'paid')->sum('total_price');

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalMovies',
            'ticketsSoldToday',
            'revenueToday',
            'revenueThisMonth',
            'activeShowtimes',
            'highestRevenueMovie',
            'lowestRevenueMovie',
            'recentBookings',
            'nowShowingMovies',
            'comingSoonMovies',
            'totalRevenue'
        ));
    }
}
```

**Giải thích**:
- **Statistics**: Users, movies, tickets, revenue
- **Time-based queries**: Today, this month, total
- **JOIN queries**: Movie revenue với bookings
- **Eager loading**: Tránh N+1 query problem
- **COALESCE**: Xử lý NULL values trong SUM

---

## 🛠️ BƯỚC 3: ADMIN MOVIE CONTROLLER

### 3.1. Full CRUD cho Movies

**File**: `app/Http/Controllers/Admin/AdminMovieController.php`

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminMovieController extends Controller
{
    /**
     * Display all movies.
     */
    public function index()
    {
        $movies = Movie::with('genres')
            ->latest()
            ->paginate(20);

        return view('admin.movies.index', compact('movies'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $genres = Genre::orderBy('name')->get();
        return view('admin.movies.create', compact('genres'));
    }

    /**
     * Store new movie.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'director' => 'nullable|string|max:255',
            'cast' => 'nullable|string',
            'genres' => 'nullable|array',
            'genres.*' => 'exists:genres,id',
            'duration' => 'required|integer|min:1',
            'release_date' => 'required|date',
            'language' => 'nullable|string|max:100',
            'rating' => 'nullable|numeric|min:0|max:10',
            'poster_url' => 'nullable|url',
            'trailer_url' => 'nullable|url',
            'status' => 'required|in:now_showing,coming_soon,ended',
            'description' => 'nullable|string',
        ]);

        // Extract genres for many-to-many relationship
        $genres = $validated['genres'] ?? [];
        unset($validated['genres']);

        $movie = Movie::create($validated);

        // Sync genres (many-to-many)
        if (!empty($genres)) {
            $movie->genres()->sync($genres);
        }

        return redirect()->route('admin.movies.index')
            ->with('success', 'Movie created successfully!');
    }

    /**
     * Show edit form.
     */
    public function edit(Movie $movie)
    {
        $genres = Genre::orderBy('name')->get();
        $movie->load('genres');
        return view('admin.movies.edit', compact('movie', 'genres'));
    }

    /**
     * Update movie.
     */
    public function update(Request $request, Movie $movie)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'director' => 'nullable|string|max:255',
            'cast' => 'nullable|string',
            'genres' => 'nullable|array',
            'genres.*' => 'exists:genres,id',
            'duration' => 'required|integer|min:1',
            'release_date' => 'required|date',
            'language' => 'nullable|string|max:100',
            'rating' => 'nullable|numeric|min:0|max:10',
            'poster_url' => 'nullable|url',
            'trailer_url' => 'nullable|url',
            'status' => 'required|in:now_showing,coming_soon,ended',
            'description' => 'nullable|string',
        ]);

        $genres = $validated['genres'] ?? [];
        unset($validated['genres']);

        $movie->update($validated);
        $movie->genres()->sync($genres);

        return redirect()->route('admin.movies.index')
            ->with('success', 'Movie updated successfully!');
    }

    /**
     * Delete movie.
     */
    public function destroy(Movie $movie)
    {
        // Detach all genres first
        $movie->genres()->detach();

        $movie->delete();

        return redirect()->route('admin.movies.index')
            ->with('success', 'Movie deleted successfully!');
    }
}
```

**Giải thích**:
- **Resource Controller pattern**: index, create, store, edit, update, destroy
- **Many-to-many sync**: genres()->sync() cho movie_genre pivot table
- **Validation**: Comprehensive rules cho tất cả fields
- **Flash messages**: Success feedback

---

## 🛠️ BƯỚC 4: ADMIN ROOM CONTROLLER

### 4.1. Room Management với Auto Seat Generation

**File**: `app/Http/Controllers/Admin/AdminRoomController.php`

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\ScreenType;
use App\Models\SeatType;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminRoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with('screenType')->get();
        return view('admin.rooms.index', compact('rooms'));
    }

    public function create()
    {
        $screenTypes = ScreenType::all();
        $seatTypes = SeatType::all();
        return view('admin.rooms.create', compact('screenTypes', 'seatTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'total_rows' => 'required|integer|min:1|max:20',
            'seats_per_row' => 'required|integer|min:1|max:30',
            'screen_type_id' => 'required|exists:screen_types,id',
        ]);

        DB::beginTransaction();
        try {
            $room = Room::create($validated);

            // Get default seat type (Standard)
            $defaultSeatType = SeatType::where('name', 'Standard')->first();
            if (!$defaultSeatType) {
                $defaultSeatType = SeatType::first();
            }

            // Generate seats automatically
            $rowLabels = range('A', 'Z');
            for ($row = 0; $row < $validated['total_rows']; $row++) {
                for ($seat = 1; $seat <= $validated['seats_per_row']; $seat++) {
                    Seat::create([
                        'room_id' => $room->id,
                        'seat_row' => $rowLabels[$row],
                        'seat_number' => $seat,
                        'seat_code' => $rowLabels[$row] . $seat,
                        'seat_type_id' => $defaultSeatType->id,
                    ]);
                }
            }

            DB::commit();
            $totalSeats = $validated['total_rows'] * $validated['seats_per_row'];
            return redirect()->route('admin.rooms.index')
                ->with('success', "Room created successfully with {$totalSeats} seats!");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create room: ' . $e->getMessage());
        }
    }

    public function edit(Room $room)
    {
        $room->load('seats.seatType', 'screenType');
        $screenTypes = ScreenType::all();
        $seatTypes = SeatType::all();

        // Group seats by row for easier display
        $seatsByRow = $room->seats->groupBy('seat_row');

        return view('admin.rooms.edit', compact('room', 'screenTypes', 'seatTypes', 'seatsByRow'));
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'screen_type_id' => 'required|exists:screen_types,id',
        ]);

        $room->update($validated);

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Room updated successfully!');
    }

    /**
     * Update individual seat types.
     */
    public function updateSeats(Request $request, Room $room)
    {
        $validated = $request->validate([
            'seats' => 'required|array',
            'seats.*.seat_id' => 'required|exists:seats,id',
            'seats.*.seat_type_id' => 'required|exists:seat_types,id',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['seats'] as $seatData) {
                Seat::where('id', $seatData['seat_id'])
                    ->update(['seat_type_id' => $seatData['seat_type_id']]);
            }

            DB::commit();
            return back()->with('success', 'Seat types updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update seats: ' . $e->getMessage());
        }
    }

    public function destroy(Room $room)
    {
        try {
            $room->delete();
            return redirect()->route('admin.rooms.index')
                ->with('success', 'Room deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete room: ' . $e->getMessage());
        }
    }
}
```

**Giải thích**:
- **Auto seat generation**: Tạo tất cả seats khi tạo room
- **Database transactions**: Đảm bảo data consistency
- **Row labels**: A-Z cho seat rows
- **Seat type management**: Admin có thể change seat type (VIP, Standard)

---

## 🛠️ BƯỚC 5: ADMIN SHOWTIME CONTROLLER

### 5.1. Showtime Management

**File**: `app/Http/Controllers/Admin/AdminShowtimeController.php`

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Showtime;
use App\Models\ShowtimePrice;
use App\Models\ShowtimeSeat;
use App\Models\Movie;
use App\Models\Room;
use App\Models\SeatType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminShowtimeController extends Controller
{
    public function index(Request $request)
    {
        $query = Showtime::with(['movie', 'room.screenType', 'showtimeSeats']);

        // Filters
        if ($request->movie_id) {
            $query->where('movie_id', $request->movie_id);
        }

        if ($request->room_id) {
            $query->where('room_id', $request->room_id);
        }

        if ($request->date) {
            $query->whereDate('show_date', $request->date);
        }

        $showtimes = $query->orderBy('show_date', 'desc')
            ->orderBy('show_time', 'desc')
            ->paginate(20);

        $movies = Movie::where('status', '!=', 'ended')->orderBy('title')->get();
        $rooms = Room::with('screenType')->orderBy('name')->get();

        return view('admin.showtimes.index', compact('showtimes', 'movies', 'rooms'));
    }

    public function create()
    {
        $movies = Movie::where('status', 'now_showing')->orderBy('title')->get();
        $rooms = Room::with('screenType')->orderBy('name')->get();
        $seatTypes = SeatType::all();

        return view('admin.showtimes.create', compact('movies', 'rooms', 'seatTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'room_id' => 'required|exists:rooms,id',
            'show_date' => 'required|date',
            'show_time' => 'required',
            'seat_type_prices' => 'required|array',
            'seat_type_prices.*' => 'required|numeric|min:0'
        ]);

        DB::beginTransaction();
        try {
            // Check conflict
            $exists = Showtime::where('room_id', $request->room_id)
                ->where('show_date', $request->show_date)
                ->where('show_time', $request->show_time)
                ->exists();

            if ($exists) {
                return back()->with('error', 'This showtime slot is already taken!');
            }

            // Create showtime
            $showtime = Showtime::create([
                'movie_id' => $request->movie_id,
                'room_id' => $request->room_id,
                'show_date' => $request->show_date,
                'show_time' => $request->show_time,
            ]);

            // Create prices for each seat type
            foreach ($request->seat_type_prices as $seatTypeId => $price) {
                ShowtimePrice::create([
                    'showtime_id' => $showtime->id,
                    'seat_type_id' => $seatTypeId,
                    'peak_hour_price' => $price,
                ]);
            }

            // Create showtime_seats (copy from room seats)
            $room = Room::with('seats')->find($request->room_id);
            foreach ($room->seats as $seat) {
                ShowtimeSeat::create([
                    'showtime_id' => $showtime->id,
                    'seat_id' => $seat->id,
                    'status' => 'available',
                ]);
            }

            DB::commit();
            return redirect()->route('admin.showtimes.index')
                ->with('success', 'Showtime created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create showtime: ' . $e->getMessage());
        }
    }

    public function edit(Showtime $showtime)
    {
        $showtime->load(['movie', 'room.screenType', 'showtimePrices.seatType', 'showtimeSeats']);
        $movies = Movie::where('status', 'now_showing')->orderBy('title')->get();
        $rooms = Room::with('screenType')->orderBy('name')->get();
        $seatTypes = SeatType::all();

        return view('admin.showtimes.edit', compact('showtime', 'movies', 'rooms', 'seatTypes'));
    }

    public function update(Request $request, Showtime $showtime)
    {
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'room_id' => 'required|exists:rooms,id',
            'show_date' => 'required|date',
            'show_time' => 'required',
            'seat_type_prices' => 'required|array',
            'seat_type_prices.*' => 'required|numeric|min:0'
        ]);

        DB::beginTransaction();
        try {
            // Check conflict (exclude current showtime)
            $exists = Showtime::where('room_id', $request->room_id)
                ->where('show_date', $request->show_date)
                ->where('show_time', $request->show_time)
                ->where('id', '!=', $showtime->id)
                ->exists();

            if ($exists) {
                return back()->with('error', 'This showtime slot is already taken!');
            }

            // Update showtime
            $showtime->update([
                'movie_id' => $request->movie_id,
                'room_id' => $request->room_id,
                'show_date' => $request->show_date,
                'show_time' => $request->show_time,
            ]);

            // Update prices
            foreach ($request->seat_type_prices as $seatTypeId => $price) {
                ShowtimePrice::updateOrCreate(
                    [
                        'showtime_id' => $showtime->id,
                        'seat_type_id' => $seatTypeId,
                    ],
                    ['peak_hour_price' => $price]
                );
            }

            DB::commit();
            return redirect()->route('admin.showtimes.index')
                ->with('success', 'Showtime updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update showtime: ' . $e->getMessage());
        }
    }

    public function destroy(Showtime $showtime)
    {
        DB::beginTransaction();
        try {
            // Check if there are bookings
            if ($showtime->bookings()->exists()) {
                return back()->with('error', 'Cannot delete showtime with existing bookings!');
            }

            // Delete related data
            $showtime->showtimeSeats()->delete();
            $showtime->showtimePrices()->delete();
            $showtime->delete();

            DB::commit();
            return back()->with('success', 'Showtime deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete showtime: ' . $e->getMessage());
        }
    }
}
```

**Giải thích**:
- **Conflict detection**: Ngăn tạo showtime trùng room/time
- **Dynamic pricing**: Giá theo seat type
- **Auto seat creation**: Copy seats từ room
- **Cascade delete**: Xóa prices & seats khi xóa showtime

---

## 🛠️ BƯỚC 6: USER MANAGEMENT CONTROLLER

### 6.1. User & Role Management

**File**: `app/Http/Controllers/Admin/AdminUserController.php`

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount('bookings');

        // Filter by role
        if ($request->role) {
            $query->where('role', $request->role);
        }

        // Search by name or email
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistics
        $stats = [
            'total' => User::count(),
            'admins' => User::where('role', 'admin')->count(),
            'users' => User::where('role', 'user')->count(),
            'users_with_bookings' => User::has('bookings')->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    public function show(User $user)
    {
        $user->load(['bookings.showtime.movie', 'bookings.showtime.room']);

        $stats = [
            'total_bookings' => $user->bookings()->count(),
            'confirmed_bookings' => $user->bookings()->where('status', 'confirmed')->count(),
            'cancelled_bookings' => $user->bookings()->where('status', 'cancelled')->count(),
            'total_spent' => $user->bookings()->where('payment_status', 'paid')->sum('total_price'),
        ];

        return view('admin.users.show', compact('user', 'stats'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
            'role' => 'required|in:user,admin',
        ]);

        $user->update($request->only(['name', 'email', 'phone', 'city', 'role']));

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        // Prevent deleting users with bookings
        if ($user->bookings()->exists()) {
            return back()->with('error', 'Cannot delete user with existing bookings.');
        }

        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully!');
    }

    public function toggleRole(User $user)
    {
        // Prevent changing your own role
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot change your own role.');
        }

        $newRole = $user->role === 'admin' ? 'user' : 'admin';
        $user->update(['role' => $newRole]);

        return back()->with('success', "User role changed to {$newRole} successfully!");
    }
}
```

**Giải thích**:
- **Search & filter**: By role, name, email
- **User statistics**: Bookings count, total spent
- **Role toggle**: Quick admin/user switch
- **Safety checks**: Prevent self-delete, delete users with bookings

---

## 🛠️ BƯỚC 7: BOOKING MANAGEMENT

### 7.1. View & Manage Bookings

**File**: `app/Http/Controllers/Admin/AdminBookingController.php`

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ShowtimeSeat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminBookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'showtime.movie', 'showtime.room', 'bookingSeats.seat']);

        // Filters
        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->date) {
            $query->whereDate('booking_date', $request->date);
        }

        // Search by user email or booking ID
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', $search)
                  ->orWhereHas('user', function($q2) use ($search) {
                      $q2->where('email', 'like', "%{$search}%")
                         ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistics
        $stats = [
            'total' => Booking::count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'pending' => Booking::where('status', 'pending')->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
            'expired' => Booking::where('status', 'expired')->count(),
            'total_revenue' => Booking::where('payment_status', 'paid')->sum('total_price'),
            'today_bookings' => Booking::whereDate('created_at', Carbon::today())->count(),
        ];

        return view('admin.bookings.index', compact('bookings', 'stats'));
    }

    public function show(Booking $booking)
    {
        $booking->load([
            'user',
            'showtime.movie',
            'showtime.room.screenType',
            'bookingSeats.seat.seatType'
        ]);

        return view('admin.bookings.show', compact('booking'));
    }

    public function cancel(Booking $booking)
    {
        if ($booking->status === 'cancelled' || $booking->status === 'expired') {
            return back()->with('error', 'Booking is already cancelled or expired!');
        }

        DB::beginTransaction();
        try {
            // Update booking status
            $booking->update(['status' => 'cancelled']);

            // Release seats
            foreach ($booking->bookingSeats as $bookingSeat) {
                DB::table('showtime_seats')
                    ->where('showtime_id', $booking->showtime_id)
                    ->where('seat_id', $bookingSeat->seat_id)
                    ->update(['status' => 'available']);
            }

            DB::commit();
            return back()->with('success', 'Booking cancelled successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel booking: ' . $e->getMessage());
        }
    }
}
```

**Giải thích**:
- **Advanced filtering**: Status, payment, date, search
- **Statistics dashboard**: Total, today, revenue
- **Cancel booking**: Release seats back to available

---

## 🛠️ BƯỚC 8: REVIEW MODERATION

### 8.1. Review Management

**File**: `app/Http/Controllers/Admin/AdminReviewController.php`

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Movie;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['user', 'movie']);

        // Filter by movie
        if ($request->filled('movie_id')) {
            $query->where('movie_id', $request->movie_id);
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        if ($sort === 'highest_rated') {
            $query->orderBy('rating', 'desc');
        } else {
            $query->latest();
        }

        $reviews = $query->paginate(20);
        $movies = Movie::orderBy('title')->get();

        return view('admin.reviews.index', compact('reviews', 'movies'));
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $movieId = $review->movie_id;

        $review->delete();

        // Update movie average rating
        $movie = Movie::find($movieId);
        $movie->updateAverageRating();

        return redirect()->back()->with('success', 'Review deleted successfully.');
    }
}
```

**Giải thích**:
- **Filter reviews**: By movie, rating
- **Sort options**: Latest, highest rated
- **Delete with side effects**: Update movie avg rating

---

## 🛠️ BƯỚC 9: QR CHECK-IN SYSTEM

### 9.1. QR Check-in Controller

**File**: `app/Http/Controllers/Admin/QRCheckInController.php`

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingSeat;
use App\Models\Booking;
use Illuminate\Http\Request;

class QRCheckInController extends Controller
{
    /**
     * Show QR scanner page.
     */
    public function index()
    {
        return view('admin.qr_checkin.index');
    }

    /**
     * Process QR code scan and check-in.
     */
    public function checkIn(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string'
        ]);

        $qrCode = $request->input('qr_code');

        // Check-in using BookingSeat model
        $result = BookingSeat::checkInWithQR($qrCode);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }

        // Get booking details
        $bookingSeats = BookingSeat::with(['seat', 'booking.showtime.movie', 'booking.user'])
            ->where('qr_code', $qrCode)
            ->get();

        $booking = $bookingSeats->first()->booking;

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data' => [
                'booking_id' => $booking->id,
                'customer_name' => $booking->user->name,
                'movie_title' => $booking->showtime->movie->title,
                'show_date' => $booking->showtime->show_date->format('d/m/Y'),
                'show_time' => $booking->showtime->show_time,
                'seats' => $bookingSeats->pluck('seat.seat_code')->toArray(),
                'total_seats' => $bookingSeats->count(),
                'checked_at' => $bookingSeats->first()->checked_at->format('d/m/Y H:i:s')
            ]
        ]);
    }

    /**
     * Get booking info by QR code (preview before check-in).
     */
    public function preview(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string'
        ]);

        $qrCode = $request->input('qr_code');

        $bookingSeats = BookingSeat::with(['seat', 'booking.showtime.movie', 'booking.user'])
            ->where('qr_code', $qrCode)
            ->get();

        if ($bookingSeats->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'QR code không tồn tại'
            ], 404);
        }

        $booking = $bookingSeats->first()->booking;
        $firstSeat = $bookingSeats->first();

        return response()->json([
            'success' => true,
            'data' => [
                'booking_id' => $booking->id,
                'customer_name' => $booking->user->name,
                'movie_title' => $booking->showtime->movie->title,
                'show_date' => $booking->showtime->show_date->format('d/m/Y'),
                'show_time' => $booking->showtime->show_time,
                'seats' => $bookingSeats->pluck('seat.seat_code')->toArray(),
                'qr_status' => $firstSeat->qr_status,
                'checked_at' => $firstSeat->checked_at ? $firstSeat->checked_at->format('d/m/Y H:i:s') : null
            ]
        ]);
    }
}
```

### 9.2. QR Check-in JavaScript

**File**: `public/js/qr_checkin.js`

```javascript
// QR Check-in System
(function() {
    'use strict';

    const qrInput = document.getElementById('qrInput');
    const previewBtn = document.getElementById('previewBtn');
    const checkInBtn = document.getElementById('checkInBtn');
    const statusMessage = document.getElementById('statusMessage');
    const resultSection = document.getElementById('resultSection');
    const recentCheckIns = document.getElementById('recentCheckIns');

    let recentList = [];

    // Auto focus on input when page loads
    if (qrInput) {
        qrInput.focus();
    }

    // Preview QR Code
    if (previewBtn) {
        previewBtn.addEventListener('click', async () => {
            const qrCode = qrInput.value.trim();
            if (!qrCode) {
                showMessage('Vui lòng nhập mã QR', 'error');
                return;
            }

            try {
                const response = await fetch(window.qrRoutes.preview, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ qr_code: qrCode })
                });

                const data = await response.json();

                if (data.success) {
                    displayBookingInfo(data.data, 'preview');
                    showMessage('', 'clear');
                } else {
                    showMessage(data.message, 'error');
                    clearResult();
                }
            } catch (error) {
                showMessage('Lỗi kết nối: ' + error.message, 'error');
            }
        });
    }

    // Check-in
    if (checkInBtn) {
        checkInBtn.addEventListener('click', async () => {
            const qrCode = qrInput.value.trim();
            if (!qrCode) {
                showMessage('Vui lòng nhập mã QR', 'error');
                return;
            }

            if (!confirm('Xác nhận check-in?')) {
                return;
            }

            try {
                const response = await fetch(window.qrRoutes.checkIn, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ qr_code: qrCode })
                });

                const data = await response.json();

                if (data.success) {
                    displayBookingInfo(data.data, 'checked');
                    showMessage(data.message, 'success');
                    addToRecentList(data.data);
                    qrInput.value = '';
                    qrInput.focus();
                } else {
                    showMessage(data.message, 'error');
                }
            } catch (error) {
                showMessage('Lỗi kết nối: ' + error.message, 'error');
            }
        });
    }

    // Enter key to check-in
    if (qrInput) {
        qrInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                checkInBtn.click();
            }
        });
    }

    function displayBookingInfo(data, status) {
        const statusBadge = status === 'checked'
            ? '<span class="badge bg-success">✓ Đã check-in</span>'
            : status === 'preview' && data.qr_status === 'checked'
            ? '<span class="badge bg-secondary">Đã check-in trước đó</span>'
            : '<span class="badge bg-warning text-dark">Chưa check-in</span>';

        resultSection.innerHTML = `
            <div class="booking-info">
                <p><strong>Booking ID:</strong> #${data.booking_id} ${statusBadge}</p>
                <p><strong>Khách hàng:</strong> ${data.customer_name}</p>
                <p><strong>Phim:</strong> ${data.movie_title}</p>
                <p><strong>Suất chiếu:</strong> ${data.show_date} - ${data.show_time}</p>
                <p><strong>Ghế:</strong><br>
                    ${data.seats.map(seat => `<span class="seat-badge">${seat}</span>`).join('')}
                </p>
                ${data.checked_at ? `<p><strong>Thời gian check-in:</strong> ${data.checked_at}</p>` : ''}
            </div>
        `;
    }

    function showMessage(message, type) {
        if (type === 'clear') {
            statusMessage.innerHTML = '';
            return;
        }
        const className = type === 'success' ? 'status-success' : type === 'error' ? 'status-error' : 'status-info';
        statusMessage.innerHTML = `<div class="${className}">${message}</div>`;
    }

    function clearResult() {
        resultSection.innerHTML = '<p class="text-muted text-center">Quét mã QR để xem thông tin</p>';
    }

    function addToRecentList(data) {
        recentList.unshift(data);
        if (recentList.length > 10) recentList.pop();

        recentCheckIns.innerHTML = recentList.map(item => `
            <div class="recent-checkin-item px-3">
                <small>
                    <strong>#${item.booking_id}</strong> - ${item.customer_name} -
                    ${item.movie_title} -
                    Ghế: ${item.seats.join(', ')} -
                    <span class="text-success">${item.checked_at}</span>
                </small>
            </div>
        `).join('');
    }
})();
```

**Giải thích**:
- **Preview**: Xem thông tin QR trước khi check-in
- **Check-in**: Mark QR as checked, record time
- **Recent list**: Show 10 gần nhất
- **Keyboard support**: Enter to check-in

---

## 🛠️ BƯỚC 10: ROUTES CONFIGURATION

### 10.1. Admin Routes

**File**: `routes/web.php` (thêm vào cuối file)

```php
// Admin Routes - Grouped with 'admin' prefix
Route::prefix('admin')->group(function () {

    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // Movies Management (Resource Controller)
    Route::resource('movies', AdminMovieController::class, ['as' => 'admin']);

    // Rooms Management
    Route::resource('rooms', AdminRoomController::class, ['as' => 'admin']);
    Route::post('rooms/{room}/update-seats', [AdminRoomController::class, 'updateSeats'])
        ->name('admin.rooms.update-seats');

    // Showtimes Management
    Route::resource('showtimes', AdminShowtimeController::class, ['as' => 'admin']);

    // Users Management
    Route::get('users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('users/{user}', [AdminUserController::class, 'show'])->name('admin.users.show');
    Route::get('users/{user}/edit', [AdminUserController::class, 'edit'])->name('admin.users.edit');
    Route::put('users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::delete('users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
    Route::post('users/{user}/toggle-role', [AdminUserController::class, 'toggleRole'])
        ->name('admin.users.toggle-role');

    // Bookings Management
    Route::get('bookings', [AdminBookingController::class, 'index'])->name('admin.bookings.index');
    Route::get('bookings/{booking}', [AdminBookingController::class, 'show'])->name('admin.bookings.show');
    Route::post('bookings/{booking}/cancel', [AdminBookingController::class, 'cancel'])
        ->name('admin.bookings.cancel');

    // Reviews Management
    Route::get('reviews', [AdminReviewController::class, 'index'])->name('admin.reviews.index');
    Route::delete('reviews/{id}', [AdminReviewController::class, 'destroy'])->name('admin.reviews.destroy');

    // QR Check-in Management
    Route::get('qr-checkin', [QRCheckInController::class, 'index'])->name('admin.qr.index');
    Route::post('qr-checkin/check', [QRCheckInController::class, 'checkIn'])->name('admin.qr.checkin');
    Route::post('qr-checkin/preview', [QRCheckInController::class, 'preview'])->name('admin.qr.preview');
});
```

**Giải thích**:
- **Prefix /admin**: Tất cả routes bắt đầu với /admin
- **Resource routes**: Automatic CRUD routes cho movies, rooms, showtimes
- **Named routes**: Dễ reference trong views
- **RESTful**: GET, POST, PUT, DELETE methods

---

## 🧪 TEST & VERIFY

### Kiểm tra từng chức năng

**1. Test Dashboard**
```
URL: http://localhost/cinebook/public/admin
```
- ✅ Statistics hiển thị đúng
- ✅ Recent bookings table
- ✅ Movie revenue charts

**2. Test Movies CRUD**
```
URL: http://localhost/cinebook/public/admin/movies
```
- ✅ List movies với pagination
- ✅ Create new movie
- ✅ Edit movie
- ✅ Delete movie
- ✅ Genres sync correctly

**3. Test Rooms CRUD**
```
URL: http://localhost/cinebook/public/admin/rooms
```
- ✅ Create room auto-generates seats
- ✅ Edit room details
- ✅ Update individual seat types

**4. Test Showtimes**
```
URL: http://localhost/cinebook/public/admin/showtimes
```
- ✅ Create showtime với prices
- ✅ Conflict detection works
- ✅ Filter by movie/room/date

**5. Test User Management**
```
URL: http://localhost/cinebook/public/admin/users
```
- ✅ List users với search
- ✅ View user details & bookings
- ✅ Toggle admin role
- ✅ Cannot delete self

**6. Test Bookings**
```
URL: http://localhost/cinebook/public/admin/bookings
```
- ✅ Filter by status/payment
- ✅ View booking details
- ✅ Cancel booking releases seats

**7. Test Reviews**
```
URL: http://localhost/cinebook/public/admin/reviews
```
- ✅ List all reviews
- ✅ Filter by movie/rating
- ✅ Delete review updates movie rating

**8. Test QR Check-in**
```
URL: http://localhost/cinebook/public/admin/qr-checkin
```
- ✅ Preview QR shows booking info
- ✅ Check-in marks QR as used
- ✅ Cannot check-in twice
- ✅ Recent check-ins list updates

---

## 🔒 MIDDLEWARE & AUTHORIZATION

### Role-based Access Control

**Note**: Hiện tại routes chưa có middleware protection. Để bảo vệ admin routes:

**Option 1: Manual Check trong Controllers**
```php
public function __construct()
{
    $this->middleware(function ($request, $next) {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }
        return $next($request);
    });
}
```

**Option 2: Tạo Middleware riêng**
```bash
php artisan make:middleware CheckRole
```

```php
// app/Http/Middleware/CheckRole.php
public function handle($request, Closure $next, $role)
{
    if (!auth()->check() || auth()->user()->role !== $role) {
        return redirect('/')->with('error', 'Unauthorized access');
    }
    return $next($request);
}
```

**Register trong routes**:
```php
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    // ... admin routes
});
```

---

## 🎨 STYLING

Admin panel sử dụng:
- **Bootstrap 5**: Main UI framework
- **Font Awesome**: Icons
- **Custom CSS**: `resources/css/admin_layout.css`

**Key CSS Variables**:
```css
:root {
    --deep-teal: #1b4f72;
    --prussian-blue: #154360;
    --burnt-peach: #e67e22;
    --deep-space-blue: #1c2833;
}
```

---

## 🐛 TROUBLESHOOTING

### Issue 1: 404 Not Found trên admin routes
**Solution**:
```bash
php artisan route:cache
php artisan route:clear
```

### Issue 2: CSRF token mismatch
**Solution**: Thêm meta tag trong layout
```html
<meta name="csrf-token" content="{{ csrf_token() }}">
```

### Issue 3: Eager loading N+1 queries
**Solution**: Luôn dùng `with()` để load relationships
```php
$bookings = Booking::with(['user', 'showtime.movie'])->get();
```

### Issue 4: QR Check-in không hoạt động
**Solution**:
1. Check routes config trong view
2. Verify CSRF token
3. Check console for JS errors

---

## 📝 BEST PRACTICES

### 1. Database Transactions
```php
DB::beginTransaction();
try {
    // Multiple operations
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    return back()->with('error', $e->getMessage());
}
```

### 2. Validation
```php
$validated = $request->validate([
    'field' => 'required|string|max:255',
]);
```

### 3. Flash Messages
```php
return redirect()->back()->with('success', 'Action completed!');
```

### 4. Eager Loading
```php
$movies = Movie::with('genres')->paginate(20);
```

---

## 🎯 SUMMARY

Bạn đã hoàn thành:

✅ **Admin Layout** - Responsive sidebar navigation
✅ **Dashboard** - Real-time statistics & analytics
✅ **CRUD Movies** - Full management với genres
✅ **CRUD Rooms** - Auto seat generation
✅ **CRUD Showtimes** - Conflict detection, dynamic pricing
✅ **User Management** - Role toggle, search, stats
✅ **Booking Management** - Advanced filters, cancel function
✅ **Review Moderation** - Filter, delete với rating update
✅ **QR Check-in** - Preview & check-in system

**Admin Panel URL**: `http://localhost/cinebook/public/admin`

---

## 🔗 NAVIGATION

- **Previous**: [10. Review System](10_review_system.md)
- **Next**: [12. Final Touches & Deployment](12_final_touches.md)
- **Back to**: [Tutorial Index](README.md)

---

**🎉 Congratulations!** Admin panel của bạn đã hoàn chỉnh với đầy đủ tính năng quản trị hệ thống cinema booking!
