# 10. HỆ THỐNG REVIEW VÀ ĐÁNH GIÁ PHIM

## 🎯 Mục tiêu bài học

Sau bài học này, bạn sẽ có:
- ✅ Chức năng viết review & rating (1-5 sao)
- ✅ Permission check (chỉ khi đã xem phim)
- ✅ CRUD reviews (Create, Read, Update, Delete)
- ✅ Auto-update movie average rating
- ✅ Review listing với pagination
- ✅ Star rating UI

**Thời gian ước tính**: 60 phút

---

## 📚 Kiến thức cần biết

- Laravel Eloquent relationships
- Model events (created, updated, deleted)
- Form validation
- Authorization gates
- Star rating UI với CSS

---

## 🛠️ BƯỚC 1: TẠO REVIEWCONTROLLER

### 1.1. Generate Controller

```bash
php artisan make:controller ReviewController
```

**File**: `app/Http/Controllers/ReviewController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Movie;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Display all reviews (paginated).
     */
    public function index(Request $request)
    {
        $query = Review::with(['user', 'movie'])
            ->latest();

        // Filter by movie if specified
        if ($request->has('movie_id')) {
            $query->where('movie_id', $request->movie_id);
        }

        // Filter by rating if specified
        if ($request->has('rating')) {
            $query->where('rating', $request->rating);
        }

        $reviews = $query->paginate(20);

        return view('reviews.index', compact('reviews'));
    }

    /**
     * Store a new review.
     */
    public function store(Request $request, $movieId)
    {
        // Check if user is logged in
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Vui lòng đăng nhập để viết đánh giá.');
        }

        $movie = Movie::findOrFail($movieId);

        // Validate input
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ], [
            'rating.required' => 'Vui lòng chọn số sao',
            'rating.min' => 'Đánh giá phải từ 1 đến 5 sao',
            'rating.max' => 'Đánh giá phải từ 1 đến 5 sao',
            'comment.max' => 'Bình luận không được quá 1000 ký tự',
        ]);

        // Check if user already reviewed this movie
        $existingReview = Review::where('user_id', Auth::id())
            ->where('movie_id', $movieId)
            ->first();

        if ($existingReview) {
            return redirect()->back()
                ->with('error', 'Bạn đã đánh giá phim này rồi.');
        }

        // Check if user has watched this movie
        if (!$this->hasWatchedMovie(Auth::id(), $movieId)) {
            return redirect()->back()
                ->with('error', 'Bạn chỉ có thể đánh giá phim sau khi đã xem.');
        }

        // Create review
        Review::create([
            'user_id' => Auth::id(),
            'movie_id' => $movieId,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        // Note: Movie rating will auto-update via Review model event

        return redirect()->back()
            ->with('success', 'Đánh giá của bạn đã được gửi!');
    }

    /**
     * Show edit form for review.
     */
    public function edit($reviewId)
    {
        $review = Review::with('movie')->findOrFail($reviewId);

        // Check ownership
        if ($review->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền chỉnh sửa đánh giá này.');
        }

        return view('reviews.edit', compact('review'));
    }

    /**
     * Update review.
     */
    public function update(Request $request, $reviewId)
    {
        $review = Review::findOrFail($reviewId);

        // Check ownership
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        // Validate input
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Update review
        $review->update([
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        // Rating will auto-update via model event

        return redirect()->route('movie.show', $review->movie_id)
            ->with('success', 'Cập nhật đánh giá thành công!');
    }

    /**
     * Delete review.
     */
    public function destroy($reviewId)
    {
        $review = Review::findOrFail($reviewId);

        // Check ownership or admin
        if ($review->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $movieId = $review->movie_id;
        $review->delete();

        // Rating will auto-update via model event

        return redirect()->route('movie.show', $movieId)
            ->with('success', 'Xóa đánh giá thành công!');
    }

    /**
     * Check if user has watched the movie.
     */
    private function hasWatchedMovie($userId, $movieId): bool
    {
        // User must have a confirmed booking for a past showtime of this movie
        $hasWatched = Booking::where('user_id', $userId)
            ->where('payment_status', 'paid')
            ->whereHas('showtime', function ($query) use ($movieId) {
                $query->where('movie_id', $movieId)
                    ->where('show_date', '<', now()->toDateString());
            })
            ->exists();

        return $hasWatched;
    }

    /**
     * Check if user can review a movie.
     */
    public function canReview($userId, $movieId): array
    {
        // Check if already reviewed
        $alreadyReviewed = Review::where('user_id', $userId)
            ->where('movie_id', $movieId)
            ->exists();

        if ($alreadyReviewed) {
            return [
                'can_review' => false,
                'reason' => 'already_reviewed',
                'message' => 'Bạn đã đánh giá phim này.',
            ];
        }

        // Check if watched
        $hasWatched = $this->hasWatchedMovie($userId, $movieId);

        if (!$hasWatched) {
            return [
                'can_review' => false,
                'reason' => 'not_watched',
                'message' => 'Bạn chỉ có thể đánh giá sau khi đã xem phim.',
            ];
        }

        return [
            'can_review' => true,
            'reason' => null,
            'message' => null,
        ];
    }
}
```

---

## 🛠️ BƯỚC 2: TẠO REVIEW VIEWS

### 2.1. Reviews Index Page

**File**: `resources/views/reviews/index.blade.php`

```blade
@extends('layouts.main')

@section('title', 'Tất cả đánh giá')

@section('content')
<div class="reviews-index-container">
    <div class="container">
        <h1>Tất cả đánh giá phim</h1>

        <!-- Filter Options -->
        <div class="filter-bar">
            <form method="GET" action="{{ route('reviews.index') }}">
                <div class="filters">
                    <select name="rating" onchange="this.form.submit()">
                        <option value="">Tất cả đánh giá</option>
                        <option value="5" {{ request('rating') == 5 ? 'selected' : '' }}>5 sao</option>
                        <option value="4" {{ request('rating') == 4 ? 'selected' : '' }}>4 sao</option>
                        <option value="3" {{ request('rating') == 3 ? 'selected' : '' }}>3 sao</option>
                        <option value="2" {{ request('rating') == 2 ? 'selected' : '' }}>2 sao</option>
                        <option value="1" {{ request('rating') == 1 ? 'selected' : '' }}>1 sao</option>
                    </select>
                </div>
            </form>
        </div>

        <!-- Reviews List -->
        <div class="reviews-list">
            @forelse($reviews as $review)
                <div class="review-card">
                    <div class="review-header">
                        <div class="user-info">
                            <div class="user-avatar">
                                @if($review->user->avatar_url)
                                    <img src="{{ $review->user->avatar_url }}" alt="{{ $review->user->name }}">
                                @else
                                    <div class="avatar-placeholder">{{ substr($review->user->name, 0, 1) }}</div>
                                @endif
                            </div>
                            <div class="user-details">
                                <h3>{{ $review->user->name }}</h3>
                                <p class="review-date">{{ $review->created_at->diffForHumans() }}</p>
                            </div>
                        </div>

                        <div class="movie-link">
                            <a href="{{ route('movie.show', $review->movie_id) }}">
                                {{ $review->movie->title }}
                            </a>
                        </div>
                    </div>

                    <div class="review-rating">
                        <div class="stars">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="star {{ $i <= $review->rating ? 'filled' : '' }}">★</span>
                            @endfor
                        </div>
                        <span class="rating-text">{{ $review->rating }}/5</span>
                    </div>

                    @if($review->comment)
                        <div class="review-comment">
                            <p>{{ $review->comment }}</p>
                        </div>
                    @endif

                    @if(Auth::check() && Auth::id() === $review->user_id)
                        <div class="review-actions">
                            <a href="{{ route('reviews.edit', $review->id) }}" class="btn btn-sm btn-secondary">
                                Chỉnh sửa
                            </a>
                            <form action="{{ route('reviews.destroy', $review->id) }}"
                                  method="POST"
                                  style="display: inline-block;"
                                  onsubmit="return confirm('Bạn có chắc muốn xóa đánh giá này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                            </form>
                        </div>
                    @endif
                </div>
            @empty
                <div class="no-reviews">
                    <p>Chưa có đánh giá nào.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="pagination-wrapper">
            {{ $reviews->links() }}
        </div>
    </div>
</div>
@endsection
```

### 2.2. Edit Review Page

**File**: `resources/views/reviews/edit.blade.php`

```blade
@extends('layouts.main')

@section('title', 'Chỉnh sửa đánh giá')

@section('content')
<div class="review-edit-container">
    <div class="container">
        <div class="review-edit-card">
            <h1>Chỉnh sửa đánh giá</h1>

            <div class="movie-info">
                <img src="{{ $review->movie->poster_url }}" alt="{{ $review->movie->title }}">
                <h2>{{ $review->movie->title }}</h2>
            </div>

            <form action="{{ route('reviews.update', $review->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Star Rating -->
                <div class="form-group">
                    <label>Đánh giá của bạn <span class="required">*</span></label>

                    <div class="star-rating-input">
                        @for($i = 5; $i >= 1; $i--)
                            <input type="radio"
                                   name="rating"
                                   id="star{{ $i }}"
                                   value="{{ $i }}"
                                   {{ old('rating', $review->rating) == $i ? 'checked' : '' }}>
                            <label for="star{{ $i }}" class="star">★</label>
                        @endfor
                    </div>

                    @error('rating')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Comment -->
                <div class="form-group">
                    <label for="comment">Nhận xét của bạn</label>
                    <textarea name="comment"
                              id="comment"
                              rows="6"
                              class="form-control"
                              placeholder="Chia sẻ cảm nhận của bạn về bộ phim...">{{ old('comment', $review->comment) }}</textarea>

                    <small class="form-text">Tối đa 1000 ký tự</small>

                    @error('comment')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="form-actions">
                    <a href="{{ route('movie.show', $review->movie_id) }}" class="btn btn-secondary">
                        Hủy
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Cập nhật đánh giá
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
```

### 2.3. Review Form Component (in Movie Details)

Thêm vào **`resources/views/movie_details.blade.php`**:

```blade
<!-- Review Section -->
<div class="reviews-section">
    <h2>Đánh giá từ khán giả</h2>

    <!-- Write Review (if eligible) -->
    @auth
        @php
            $canReviewResult = app(App\Http\Controllers\ReviewController::class)
                ->canReview(Auth::id(), $movie->id);
        @endphp

        @if($canReviewResult['can_review'])
            <div class="write-review-card">
                <h3>Viết đánh giá của bạn</h3>

                <form action="{{ route('reviews.store', $movie->id) }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label>Đánh giá <span class="required">*</span></label>
                        <div class="star-rating-input">
                            @for($i = 5; $i >= 1; $i--)
                                <input type="radio" name="rating" id="star{{ $i }}" value="{{ $i }}">
                                <label for="star{{ $i }}" class="star">★</label>
                            @endfor
                        </div>
                        @error('rating')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="comment">Nhận xét</label>
                        <textarea name="comment" id="comment" rows="4"
                                  placeholder="Chia sẻ cảm nhận của bạn..."></textarea>
                        @error('comment')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
                </form>
            </div>
        @else
            <div class="cannot-review-message">
                <p>{{ $canReviewResult['message'] }}</p>
            </div>
        @endif
    @else
        <p class="login-prompt">
            <a href="{{ route('login') }}">Đăng nhập</a> để viết đánh giá
        </p>
    @endauth

    <!-- Reviews List -->
    <div class="reviews-list">
        @forelse($movie->reviews()->latest()->take(10)->get() as $review)
            <div class="review-item">
                <div class="review-header">
                    <strong>{{ $review->user->name }}</strong>
                    <span class="review-date">{{ $review->created_at->diffForHumans() }}</span>
                </div>

                <div class="review-rating">
                    @for($i = 1; $i <= 5; $i++)
                        <span class="star {{ $i <= $review->rating ? 'filled' : '' }}">★</span>
                    @endfor
                </div>

                @if($review->comment)
                    <p class="review-comment">{{ $review->comment }}</p>
                @endif

                @if(Auth::check() && Auth::id() === $review->user_id)
                    <div class="review-actions">
                        <a href="{{ route('reviews.edit', $review->id) }}">Chỉnh sửa</a>
                        <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Xóa đánh giá?')">Xóa</button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <p class="no-reviews">Chưa có đánh giá nào.</p>
        @endforelse
    </div>

    @if($movie->reviews()->count() > 10)
        <a href="{{ route('reviews.index', ['movie_id' => $movie->id]) }}" class="btn btn-secondary">
            Xem tất cả {{ $movie->reviews()->count() }} đánh giá
        </a>
    @endif
</div>
```

---

## 🛠️ BƯỚC 3: TẠO CSS CHO REVIEWS

**File**: `resources/css/reviews.css`

```css
/* Star Rating Input */
.star-rating-input {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
    gap: 5px;
    font-size: 2rem;
}

.star-rating-input input[type="radio"] {
    display: none;
}

.star-rating-input label.star {
    cursor: pointer;
    color: #ddd;
    transition: color 0.2s;
}

.star-rating-input input:checked ~ label.star,
.star-rating-input label.star:hover,
.star-rating-input label.star:hover ~ label.star {
    color: #ffd700;
}

/* Star Display */
.stars .star {
    font-size: 1.2rem;
    color: #ddd;
}

.stars .star.filled {
    color: #ffd700;
}

/* Review Card */
.review-card {
    background: var(--bg-card);
    padding: var(--spacing-lg);
    border-radius: var(--radius-md);
    margin-bottom: var(--spacing-lg);
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--spacing-md);
}

.user-info {
    display: flex;
    gap: var(--spacing-md);
    align-items: center;
}

.user-avatar img,
.avatar-placeholder {
    width: 50px;
    height: 50px;
    border-radius: 50%;
}

.avatar-placeholder {
    background: var(--primary-color);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: var(--font-size-xl);
    font-weight: 600;
    color: white;
}

.review-comment {
    margin-top: var(--spacing-md);
    color: var(--text-secondary);
    line-height: 1.6;
}
```

---

## 🛠️ BƯỚC 4: THÊM ROUTES

**File**: `routes/web.php`

```php
// Review routes
Route::get('/reviews', [ReviewController::class, 'index'])
    ->name('reviews.index');

Route::middleware('auth')->group(function () {
    Route::post('/movies/{id}/reviews', [ReviewController::class, 'store'])
        ->name('reviews.store');

    Route::get('/reviews/{id}/edit', [ReviewController::class, 'edit'])
        ->name('reviews.edit');

    Route::put('/reviews/{id}', [ReviewController::class, 'update'])
        ->name('reviews.update');

    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy'])
        ->name('reviews.destroy');
});
```

---

## ✅ TEST & VERIFY

### Test Cases:

1. **Permission Check**:
   - User chưa xem phim → Không hiện form review
   - User đã xem phim → Hiện form review
   - User đã review → Hiện message "Đã đánh giá"

2. **Create Review**:
   - Chọn số sao, viết comment
   - Submit form
   - Review được tạo
   - Movie rating_avg tự động cập nhật

3. **Edit Review**:
   - Click "Chỉnh sửa"
   - Thay đổi rating/comment
   - Submit
   - Review updated
   - Movie rating updated

4. **Delete Review**:
   - Click "Xóa"
   - Confirm
   - Review deleted
   - Movie rating updated

5. **Auto Rating Update**:
   - Tạo/sửa/xóa review
   - Check movie.rating_avg trong database
   - Verify tính toán đúng

---

## 📝 TÓM TẮT

Đã hoàn thành:
- ✅ ReviewController với CRUD
- ✅ Permission check (watched movie)
- ✅ Star rating UI
- ✅ Auto-update movie rating
- ✅ Review listing & pagination

**Bài tiếp**: [11. Admin Panel →](11_admin_panel.md)

---

**Bài trước**: [← 09. Payment & QR](09_payment_qr.md)
**Series**: Cinebook Tutorial
