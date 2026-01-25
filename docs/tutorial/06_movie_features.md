# 06. XEM PHIM, TÌM KIẾM, CHI TIẾT

## 🎯 Mục tiêu bài học

Sau bài học này, bạn sẽ có:
- ✅ Trang danh sách phim (Đang chiếu, Sắp chiếu)
- ✅ Trang chi tiết phim với đầy đủ thông tin
- ✅ Chức năng tìm kiếm phim
- ✅ Lọc theo thể loại
- ✅ Hiển thị suất chiếu theo ngày

**Thời gian ước tính**: 75-90 phút

---

## 🛠️ BƯỚC 1: TẠO MOVIE CONTROLLER

```bash
php artisan make:controller MovieController
```

**File**: `app/Http/Controllers/MovieController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Genre;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    /**
     * Hiển thị tất cả phim đang chiếu.
     */
    public function nowShowing(Request $request)
    {
        $query = Movie::where('status', 'now_showing')
            ->with('genres');

        // Filter by genre
        if ($request->has('genre') && $request->genre) {
            $query->whereHas('genres', function($q) use ($request) {
                $q->where('genres.id', $request->genre);
            });
        }

        // Search by title
        if ($request->has('search') && $request->search) {
            $query->where('title', 'LIKE', '%' . $request->search . '%');
        }

        // Sort
        $sortBy = $request->get('sort', 'rating_avg');
        if ($sortBy === 'rating_avg') {
            $query->orderBy('rating_avg', 'desc');
        } elseif ($sortBy === 'title') {
            $query->orderBy('title', 'asc');
        } elseif ($sortBy === 'release_date') {
            $query->orderBy('release_date', 'desc');
        }

        $movies = $query->paginate(12);
        $genres = Genre::all();

        return view('movies.now-showing', compact('movies', 'genres'));
    }

    /**
     * Hiển thị tất cả phim sắp chiếu.
     */
    public function comingSoon(Request $request)
    {
        $query = Movie::where('status', 'coming_soon')
            ->with('genres');

        // Filter by genre
        if ($request->has('genre') && $request->genre) {
            $query->whereHas('genres', function($q) use ($request) {
                $q->where('genres.id', $request->genre);
            });
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where('title', 'LIKE', '%' . $request->search . '%');
        }

        $movies = $query->orderBy('release_date', 'asc')->paginate(12);
        $genres = Genre::all();

        return view('movies.coming-soon', compact('movies', 'genres'));
    }

    /**
     * Hiển thị chi tiết phim.
     */
    public function detail($id)
    {
        $movie = Movie::with(['genres', 'showtimes.room', 'reviews.user'])
            ->findOrFail($id);

        // Lấy showtimes trong 7 ngày tới
        $showtimes = $movie->showtimes()
            ->where('show_date', '>=', now()->toDateString())
            ->where('show_date', '<=', now()->addDays(7)->toDateString())
            ->with('room.screenType')
            ->orderBy('show_date')
            ->orderBy('show_time')
            ->get()
            ->groupBy('show_date');

        // Lấy reviews
        $reviews = $movie->reviews()
            ->with('user')
            ->latest()
            ->paginate(10);

        return view('movies.detail', compact('movie', 'showtimes', 'reviews'));
    }

    /**
     * Tìm kiếm phim (AJAX).
     */
    public function search(Request $request)
    {
        $search = $request->get('q', '');

        $movies = Movie::where('title', 'LIKE', '%' . $search . '%')
            ->orWhere('director', 'LIKE', '%' . $search . '%')
            ->orWhere('cast', 'LIKE', '%' . $search . '%')
            ->limit(10)
            ->get(['id', 'title', 'poster_url', 'rating_avg', 'status']);

        return response()->json($movies);
    }
}
```

---

## 🛠️ BƯỚC 2: TẠO CSS CHO MOVIE PAGES

**File**: `resources/css/movies.css`

```css
/* Movies List Page */

.movies-page {
    padding: var(--spacing-2xl) 0;
    min-height: calc(100vh - var(--header-height) - var(--footer-height));
}

.movies-header {
    margin-bottom: var(--spacing-2xl);
}

.movies-title {
    font-size: var(--font-size-3xl);
    margin-bottom: var(--spacing-md);
}

.movies-subtitle {
    color: var(--text-secondary);
    font-size: var(--font-size-lg);
}

/* Filters */
.movies-filters {
    background-color: var(--bg-card);
    border-radius: var(--radius-lg);
    padding: var(--spacing-lg);
    margin-bottom: var(--spacing-xl);
}

.filters-row {
    display: grid;
    grid-template-columns: 1fr auto auto;
    gap: var(--spacing-md);
    align-items: end;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-sm);
}

.filter-label {
    font-weight: 500;
    font-size: var(--font-size-sm);
    color: var(--text-secondary);
}

.filter-input,
.filter-select {
    padding: var(--spacing-md);
    background-color: var(--bg-dark);
    border: 2px solid transparent;
    border-radius: var(--radius-md);
    color: var(--text-primary);
    font-size: var(--font-size-base);
}

.filter-input:focus,
.filter-select:focus {
    outline: none;
    border-color: var(--primary-color);
}

/* Pagination */
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: var(--spacing-sm);
    margin-top: var(--spacing-2xl);
}

.pagination-link {
    padding: var(--spacing-sm) var(--spacing-md);
    background-color: var(--bg-card);
    border-radius: var(--radius-md);
    color: var(--text-primary);
    text-decoration: none;
    transition: all var(--transition-fast);
}

.pagination-link:hover {
    background-color: var(--primary-color);
}

.pagination-link.active {
    background-color: var(--primary-color);
}

.pagination-link.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Movie Detail Page */

.movie-detail {
    padding: var(--spacing-2xl) 0;
}

.movie-detail-hero {
    background-color: var(--bg-dark-secondary);
    border-radius: var(--radius-lg);
    overflow: hidden;
    margin-bottom: var(--spacing-2xl);
}

.movie-detail-content {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: var(--spacing-2xl);
    padding: var(--spacing-2xl);
}

.movie-detail-poster {
    width: 100%;
    height: 450px;
    object-fit: cover;
    border-radius: var(--radius-md);
    background-color: var(--bg-dark);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 100px;
}

.movie-detail-info-title {
    font-size: var(--font-size-4xl);
    margin-bottom: var(--spacing-md);
}

.movie-detail-meta {
    display: flex;
    gap: var(--spacing-xl);
    margin-bottom: var(--spacing-lg);
    flex-wrap: wrap;
}

.movie-detail-meta-item {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    color: var(--text-secondary);
}

.movie-detail-meta-item strong {
    color: var(--text-primary);
}

.movie-detail-genres {
    display: flex;
    gap: var(--spacing-sm);
    margin-bottom: var(--spacing-lg);
    flex-wrap: wrap;
}

.genre-badge {
    padding: var(--spacing-xs) var(--spacing-md);
    background-color: rgba(229, 9, 20, 0.2);
    border: 1px solid var(--primary-color);
    border-radius: var(--radius-full);
    color: var(--primary-color);
    font-size: var(--font-size-sm);
}

.movie-detail-description {
    color: var(--text-secondary);
    line-height: 1.8;
    margin-bottom: var(--spacing-lg);
}

.movie-detail-cast {
    margin-bottom: var(--spacing-lg);
}

.movie-detail-cast h4 {
    margin-bottom: var(--spacing-sm);
    color: var(--text-primary);
}

.movie-detail-cast p {
    color: var(--text-secondary);
}

/* Showtimes */
.showtimes-section {
    margin-bottom: var(--spacing-2xl);
}

.showtimes-title {
    font-size: var(--font-size-2xl);
    margin-bottom: var(--spacing-xl);
}

.showtime-day {
    margin-bottom: var(--spacing-xl);
}

.showtime-day-title {
    font-size: var(--font-size-xl);
    margin-bottom: var(--spacing-md);
    color: var(--primary-color);
}

.showtime-slots {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: var(--spacing-md);
}

.showtime-slot {
    background-color: var(--bg-card);
    border: 2px solid transparent;
    border-radius: var(--radius-md);
    padding: var(--spacing-md);
    text-align: center;
    cursor: pointer;
    transition: all var(--transition-base);
    text-decoration: none;
}

.showtime-slot:hover {
    border-color: var(--primary-color);
    transform: translateY(-2px);
}

.showtime-time {
    font-size: var(--font-size-xl);
    font-weight: 600;
    color: var(--text-primary);
    display: block;
    margin-bottom: var(--spacing-xs);
}

.showtime-room {
    font-size: var(--font-size-sm);
    color: var(--text-secondary);
}

/* Reviews Section */
.reviews-section {
    background-color: var(--bg-dark-secondary);
    border-radius: var(--radius-lg);
    padding: var(--spacing-2xl);
}

.reviews-title {
    font-size: var(--font-size-2xl);
    margin-bottom: var(--spacing-xl);
}

.review-card {
    background-color: var(--bg-card);
    border-radius: var(--radius-md);
    padding: var(--spacing-lg);
    margin-bottom: var(--spacing-lg);
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--spacing-md);
}

.review-user {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
}

.review-avatar {
    width: 40px;
    height: 40px;
    border-radius: var(--radius-full);
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
}

.review-user-name {
    font-weight: 500;
}

.review-rating {
    color: var(--warning-color);
}

.review-comment {
    color: var(--text-secondary);
    line-height: 1.6;
}

.review-date {
    color: var(--text-muted);
    font-size: var(--font-size-sm);
    margin-top: var(--spacing-sm);
}

/* Responsive */
@media (max-width: 768px) {
    .movie-detail-content {
        grid-template-columns: 1fr;
    }

    .filters-row {
        grid-template-columns: 1fr;
    }

    .showtime-slots {
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    }
}
```

---

## 🛠️ BƯỚC 3: TẠO VIEWS

### 3.1. Now Showing View

**File**: `resources/views/movies/now-showing.blade.php`

```blade
@extends('layouts.app')

@section('title', 'Phim đang chiếu - Cinebook')

@push('styles')
    @vite(['resources/css/movies.css', 'resources/css/home.css'])
@endpush

@section('content')
<div class="movies-page">
    <div class="container">
        {{-- Header --}}
        <div class="movies-header">
            <h1 class="movies-title">🎬 Phim đang chiếu</h1>
            <p class="movies-subtitle">Khám phá các bộ phim mới nhất đang chiếu tại rạp</p>
        </div>

        {{-- Filter & Sort Form --}}
        <form method="GET" action="" class="movie-filter-form">
            <div class="filter-row">
                {{-- Genre Filter --}}
                <div class="filter-group">
                    <label for="genre">Genre</label>
                    <select name="genre" id="genre">
                        <option value="">All Genres</option>
                        @foreach($genres as $genre)
                            <option value="{{ $genre->id }}" {{ request('genre') == $genre->id ? 'selected' : '' }}>
                                {{ $genre->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Language Filter --}}
                <div class="filter-group">
                    <label for="language">Language</label>
                    <select name="language" id="language">
                        <option value="">All Languages</option>
                        @foreach($languages as $lang)
                            <option value="{{ $lang }}" {{ request('language') == $lang ? 'selected' : '' }}>
                                {{ $lang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Rating Filter --}}
                <div class="filter-group">
                    <label for="rating">Rating</label>
                    <select name="rating" id="rating">
                        <option value="">All Ratings</option>
                        <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Stars</option>
                        <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4+ Stars</option>
                        <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3+ Stars</option>
                    </select>
                </div>

                {{-- Showtime Date Filter --}}
                <div class="filter-group">
                    <label for="showtime_date">Showtime Date</label>
                    <input type="date" name="showtime_date" id="showtime_date"
                           value="{{ request('showtime_date', date('Y-m-d')) }}">
                </div>

                {{-- Sort --}}
                <div class="filter-group">
                    <label for="sort">Sort by</label>
                    <select name="sort" id="sort">
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                        <option value="rating_desc" {{ request('sort') == 'rating_desc' ? 'selected' : '' }}>Rating (High-Low)</option>
                        <option value="release_desc" {{ request('sort') == 'release_desc' ? 'selected' : '' }}>Release Date</option>
                    </select>
                </div>

                {{-- Buttons --}}
                <div class="filter-group filter-actions">
                    <button type="submit" class="movie-btn movie-btn-primary">Apply</button>
                    <a href="{{ route('now_showing') }}" class="movie-btn movie-btn-secondary">Reset</a>
                </div>
            </div>
        </form>

        {{-- Movies Grid --}}
        @if($movies->count() > 0)
            <div class="movie-grid">
                @foreach($movies as $movie)
                    <div class="movie-card" onclick="window.location='{{ route('movies.detail', $movie->id) }}'">
                        <div class="movie-card-poster">
                            @if($movie->poster_url)
                                <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                🎬
                            @endif
                        </div>
                        <div class="movie-card-content">
                            <h3 class="movie-card-title">{{ $movie->title }}</h3>
                            <div class="movie-card-info">
                                <span class="movie-card-rating">
                                    ⭐ {{ number_format($movie->rating_avg, 1) }}
                                </span>
                                <span class="movie-card-duration">
                                    🕐 {{ $movie->duration }} phút
                                </span>
                            </div>
                            <div class="movie-card-genres">
                                {{ $movie->genres->pluck('name')->join(', ') }}
                            </div>
                            <a href="{{ route('movies.detail', $movie->id) }}" class="btn btn-primary btn-block">
                                Đặt vé
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="pagination">
                {{ $movies->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">🔍</div>
                <p class="empty-state-text">Không tìm thấy phim nào phù hợp</p>
            </div>
        @endif
    </div>
</div>
@endsection
```

### 3.2. Coming Soon View

**File**: `resources/views/movies/coming-soon.blade.php`

```blade
@extends('layouts.app')

@section('title', 'Phim sắp chiếu - Cinebook')

@push('styles')
    @vite(['resources/css/movies.css', 'resources/css/home.css'])
@endpush

@section('content')
<div class="movies-page">
    <div class="container">
        <div class="movies-header">
            <h1 class="movies-title">📅 Phim sắp chiếu</h1>
            <p class="movies-subtitle">Những bộ phim bom tấn sắp ra mắt</p>
        </div>

        {{-- Filters --}}
        <div class="movies-filters">
            <form method="GET" action="{{ route('movies.coming-soon') }}">
                <div class="filters-row">
                    <div class="filter-group">
                        <label class="filter-label">Tìm kiếm</label>
                        <input
                            type="text"
                            name="search"
                            class="filter-input"
                            placeholder="Tên phim..."
                            value="{{ request('search') }}"
                        >
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">Thể loại</label>
                        <select name="genre" class="filter-select">
                            <option value="">Tất cả</option>
                            @foreach($genres as $genre)
                                <option value="{{ $genre->id }}" {{ request('genre') == $genre->id ? 'selected' : '' }}>
                                    {{ $genre->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Lọc
                    </button>
                </div>
            </form>
        </div>

        @if($movies->count() > 0)
            <div class="movie-grid">
                @foreach($movies as $movie)
                    <div class="movie-card" onclick="window.location='{{ route('movies.detail', $movie->id) }}'">
                        <div class="movie-card-poster">
                            @if($movie->poster_url)
                                <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                🎬
                            @endif
                        </div>
                        <div class="movie-card-content">
                            <h3 class="movie-card-title">{{ $movie->title }}</h3>
                            <div class="movie-card-info">
                                <span class="movie-card-duration">
                                    📅 {{ $movie->release_date->format('d/m/Y') }}
                                </span>
                                <span class="movie-card-duration">
                                    🕐 {{ $movie->duration }} phút
                                </span>
                            </div>
                            <div class="movie-card-genres">
                                {{ $movie->genres->pluck('name')->join(', ') }}
                            </div>
                            <a href="{{ route('movies.detail', $movie->id) }}" class="btn btn-secondary btn-block">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pagination">
                {{ $movies->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">📆</div>
                <p class="empty-state-text">Chưa có phim sắp chiếu</p>
            </div>
        @endif
    </div>
</div>
@endsection
```

### 3.3. Movie Detail View

**File**: `resources/views/movies/detail.blade.php`

```blade
@extends('layouts.app')

@section('title', $movie->title . ' - Cinebook')

@push('styles')
    @vite(['resources/css/movies.css'])
@endpush

@section('content')
<div class="movie-detail">
    <div class="container">
        {{-- Hero Section --}}
        <div class="movie-detail-hero">
            <div class="movie-detail-content">
                {{-- Poster --}}
                <div class="movie-detail-poster">
                    @if($movie->poster_url)
                        <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: var(--radius-md);">
                    @else
                        🎬
                    @endif
                </div>

                {{-- Info --}}
                <div class="movie-detail-info">
                    <h1 class="movie-detail-info-title">{{ $movie->title }}</h1>

                    <div class="movie-detail-meta">
                        <div class="movie-detail-meta-item">
                            ⭐ <strong>{{ number_format($movie->rating_avg, 1) }}/5.0</strong>
                        </div>
                        <div class="movie-detail-meta-item">
                            🕐 <strong>{{ $movie->duration }} phút</strong>
                        </div>
                        <div class="movie-detail-meta-item">
                            📅 <strong>{{ $movie->release_date->format('d/m/Y') }}</strong>
                        </div>
                        <div class="movie-detail-meta-item">
                            🔞 <strong>{{ $movie->age_rating }}</strong>
                        </div>
                    </div>

                    <div class="movie-detail-genres">
                        @foreach($movie->genres as $genre)
                            <span class="genre-badge">{{ $genre->name }}</span>
                        @endforeach
                    </div>

                    <p class="movie-detail-description">
                        {{ $movie->description }}
                    </p>

                    <div class="movie-detail-cast">
                        <h4>🎬 Đạo diễn</h4>
                        <p>{{ $movie->director }}</p>
                    </div>

                    <div class="movie-detail-cast">
                        <h4>🎭 Diễn viên</h4>
                        <p>{{ $movie->cast }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Showtimes --}}
        @if($movie->isNowShowing() && $showtimes->count() > 0)
            <div class="showtimes-section">
                <h2 class="showtimes-title">📅 Lịch chiếu</h2>

                @foreach($showtimes as $date => $times)
                    <div class="showtime-day">
                        <h3 class="showtime-day-title">
                            {{ \Carbon\Carbon::parse($date)->locale('vi')->isoFormat('dddd, DD/MM/YYYY') }}
                        </h3>
                        <div class="showtime-slots">
                            @foreach($times as $showtime)
                                <a href="{{ route('booking.seats', $showtime->id) }}" class="showtime-slot">
                                    <span class="showtime-time">
                                        {{ \Carbon\Carbon::parse($showtime->show_time)->format('H:i') }}
                                    </span>
                                    <span class="showtime-room">
                                        {{ $showtime->room->name }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Reviews --}}
        @if($reviews->count() > 0)
            <div class="reviews-section">
                <h2 class="reviews-title">⭐ Đánh giá từ khán giả</h2>

                @foreach($reviews as $review)
                    <div class="review-card">
                        <div class="review-header">
                            <div class="review-user">
                                <div class="review-avatar">
                                    {{ strtoupper(substr($review->user->name, 0, 1)) }}
                                </div>
                                <span class="review-user-name">{{ $review->user->name }}</span>
                            </div>
                            <div class="review-rating">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        ⭐
                                    @else
                                        ☆
                                    @endif
                                @endfor
                            </div>
                        </div>
                        <p class="review-comment">{{ $review->comment }}</p>
                        <p class="review-date">{{ $review->created_at->diffForHumans() }}</p>
                    </div>
                @endforeach

                {{ $reviews->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
```

---

## 🛠️ BƯỚC 4: CẬP NHẬT ROUTES

**File**: `routes/web.php`

```php
use App\Http\Controllers\MovieController;

// Movies
Route::get('/movies/now-showing', [MovieController::class, 'nowShowing'])
    ->name('movies.now-showing');
Route::get('/movies/coming-soon', [MovieController::class, 'comingSoon'])
    ->name('movies.coming-soon');
Route::get('/movies/{id}', [MovieController::class, 'detail'])
    ->name('movies.detail');

// Search API
Route::get('/api/movies/search', [MovieController::class, 'search'])
    ->name('api.movies.search');
```

---

## ✅ TEST & VERIFY

### Test danh sách phim
1. Truy cập: `http://localhost:8000/movies/now-showing`
2. Thử filter theo thể loại
3. Thử search phim
4. Test pagination

### Test chi tiết phim
1. Click vào một phim
2. Kiểm tra hiển thị đầy đủ thông tin
3. Kiểm tra lịch chiếu
4. Kiểm tra reviews

---

## 📝 TÓM TẮT

Files đã tạo:
- MovieController.php
- movies.css
- 3 views: now-showing, coming-soon, detail

---

## 🚀 BƯỚC TIẾP THEO

**Bài tiếp**: [07. Booking System →](07_booking_system.md)

---

**Bài trước**: [← 05. Frontend Basics](05_frontend_basics.md)
**Series**: Cinebook Tutorial
**Cập nhật**: January 2026
