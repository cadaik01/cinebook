@extends('layouts.main')

@section('title', 'Movie Reviews - TCA Cine')

@push('styles')
@vite('resources/css/reviews_index.css')
@endpush

@section('content')
<div class="reviews-public-container">
    {{-- Header Section --}}
    <div class="reviews-public-header">
        <h1><i class="fas fa-star"></i> Movie Reviews</h1>
        <p class="subtitle">See what others are saying about our movies</p>
    </div>

    {{-- Most Reviewed Movie Section --}}
    @if($stats['top_movie'])
    <a href="/movies/{{ $stats['top_movie']->id }}" class="most-reviewed-section">
        <div class="most-reviewed-badge">
            <i class="fas fa-trophy"></i>
            <span>Most Reviewed</span>
        </div>
        <div class="most-reviewed-content">
            <div class="most-reviewed-poster">
                @if($stats['top_movie']->poster_url)
                <img src="{{ $stats['top_movie']->poster_url }}" alt="{{ $stats['top_movie']->title }}">
                @else
                <div class="poster-placeholder">
                    <i class="fas fa-film"></i>
                </div>
                @endif
            </div>
            <div class="most-reviewed-info">
                <h2 class="most-reviewed-title">{{ $stats['top_movie']->title }}</h2>
                <div class="most-reviewed-stats">
                    <span class="review-count-badge">
                        <i class="fas fa-comments"></i>
                        {{ $stats['top_movie']->reviews_count }} Reviews
                    </span>
                    @if($stats['top_movie']->rating_avg)
                    <span class="rating-avg-badge">
                        <i class="fas fa-star"></i>
                        {{ number_format($stats['top_movie']->rating_avg, 1) }}/5
                    </span>
                    @endif
                </div>
                <p class="most-reviewed-cta">
                    Click to view movie details <i class="fas fa-arrow-right"></i>
                </p>
            </div>
        </div>
    </a>
    @endif

    {{-- Stats Section --}}
    <div class="reviews-stats-section">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-comments"></i>
            </div>
            <div class="stat-content">
                <span class="stat-number">{{ $stats['total_reviews'] }}</span>
                <span class="stat-label">Total Reviews</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-star"></i>
            </div>
            <div class="stat-content">
                <span class="stat-number">{{ $stats['average_rating'] }}</span>
                <span class="stat-label">Average Rating</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <span class="stat-number">{{ $stats['total_reviewers'] }}</span>
                <span class="stat-label">Reviewers</span>
            </div>
        </div>
    </div>

    {{-- Rating Distribution --}}
    <div class="rating-distribution-section">
        <h3><i class="fas fa-chart-bar"></i> Rating Distribution</h3>
        <div class="rating-bars">
            @foreach($ratingDistribution as $rating => $data)
            <div class="rating-bar-row">
                <span class="rating-label">{{ $rating }} <i class="fas fa-star"></i></span>
                <div class="rating-bar-container">
                    <div class="rating-bar-fill" style="width: {{ $data['percentage'] }}%"></div>
                </div>
                <span class="rating-percentage">{{ $data['percentage'] }}%</span>
                <span class="rating-count">({{ $data['count'] }})</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Filter & Sort Section --}}
    <div class="filter-sort-section">
        <form action="{{ route('reviews.index') }}" method="GET" class="filter-form" id="filterForm">
            <div class="filter-group">
                <label for="movie_id"><i class="fas fa-film"></i> Movie</label>
                <select name="movie_id" id="movie_id" onchange="document.getElementById('filterForm').submit()">
                    <option value="">All Movies</option>
                    @foreach($movies as $movie)
                    <option value="{{ $movie->id }}" {{ request('movie_id') == $movie->id ? 'selected' : '' }}>
                        {{ Str::limit($movie->title, 30) }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label for="rating"><i class="fas fa-star"></i> Rating</label>
                <select name="rating" id="rating" onchange="document.getElementById('filterForm').submit()">
                    <option value="">All Ratings</option>
                    @for($i = 5; $i >= 1; $i--)
                    <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                        {{ $i }} Star{{ $i > 1 ? 's' : '' }}
                    </option>
                    @endfor
                </select>
            </div>
            <div class="filter-group">
                <label for="sort"><i class="fas fa-sort"></i> Sort By</label>
                <select name="sort" id="sort" onchange="document.getElementById('filterForm').submit()">
                    <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>Newest First</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    <option value="highest" {{ request('sort') == 'highest' ? 'selected' : '' }}>Highest Rating</option>
                    <option value="lowest" {{ request('sort') == 'lowest' ? 'selected' : '' }}>Lowest Rating</option>
                    <option value="helpful" {{ request('sort') == 'helpful' ? 'selected' : '' }}>Most Helpful</option>
                </select>
            </div>
            @if(request('movie_id') || request('rating') || request('sort') != 'newest')
            <a href="{{ route('reviews.index') }}" class="btn-clear-filters">
                <i class="fas fa-times"></i> Clear Filters
            </a>
            @endif
        </form>
    </div>

    {{-- Reviews Grid --}}
    @if($reviews->isEmpty())
    <div class="no-reviews-card">
        <i class="fas fa-star-half-alt"></i>
        <h3>No Reviews Found</h3>
        <p>
            @if(request('movie_id') || request('rating'))
                No reviews match your filters. Try adjusting your search criteria.
            @else
                Be the first to share your thoughts! Watch a movie and leave a review.
            @endif
        </p>
        <a href="{{ route('now_showing') }}" class="btn-browse">
            <i class="fas fa-ticket-alt"></i> Book Tickets Now
        </a>
    </div>
    @else
    <div class="reviews-grid">
        @foreach($reviews as $review)
        <div class="review-card">
            {{-- Movie Poster & Info --}}
            <a href="/movies/{{ $review->movie_id }}" class="review-card-header">
                <div class="movie-poster">
                    @if($review->movie && $review->movie->poster_url)
                    <img src="{{ $review->movie->poster_url }}" alt="{{ $review->movie->title }}">
                    @else
                    <div class="poster-placeholder">
                        <i class="fas fa-film"></i>
                    </div>
                    @endif
                </div>
                <div class="movie-info">
                    <h3 class="movie-title">{{ $review->movie->title ?? 'N/A' }}</h3>
                    <div class="rating-display">
                        <div class="stars">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= $review->rating)
                                <span class="star-filled"><i class="fas fa-star"></i></span>
                                @else
                                <span class="star-empty"><i class="far fa-star"></i></span>
                                @endif
                            @endfor
                        </div>
                        <span class="rating-badge">{{ $review->rating }}/5</span>
                    </div>
                </div>
            </a>

            {{-- Review Content --}}
            <div class="review-card-body">
                <p class="review-comment">
                    <i class="fas fa-quote-left quote-icon"></i>
                    {{ $review->comment ?? 'No comment provided' }}
                    <i class="fas fa-quote-right quote-icon"></i>
                </p>
            </div>

            {{-- Review Footer --}}
            <div class="review-card-footer">
                <div class="footer-left">
                    <div class="reviewer-info">
                        <div class="reviewer-avatar">
                            {{ strtoupper(substr($review->user->name ?? 'A', 0, 1)) }}
                        </div>
                        <span class="reviewer-name">{{ $review->user->name ?? 'Anonymous' }}</span>
                    </div>
                    <div class="review-date">
                        <i class="fas fa-clock"></i>
                        <span>{{ $review->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                <div class="footer-right">
                    @auth
                        @if(Auth::id() !== $review->user_id)
                        <button class="btn-helpful {{ $review->isHelpfulBy(Auth::id()) ? 'is-helpful' : '' }}"
                                data-review-id="{{ $review->id }}"
                                onclick="toggleHelpful({{ $review->id }})">
                            <i class="fas fa-thumbs-up"></i>
                            <span class="helpful-count">{{ $review->helpfuls->count() }}</span>
                            <span class="helpful-text">Helpful</span>
                        </button>
                        @else
                        <span class="helpful-display">
                            <i class="fas fa-thumbs-up"></i>
                            <span>{{ $review->helpfuls->count() }} Helpful</span>
                        </span>
                        @endif
                    @else
                    <span class="helpful-display">
                        <i class="fas fa-thumbs-up"></i>
                        <span>{{ $review->helpfuls->count() }} Helpful</span>
                    </span>
                    @endauth

                    @if(Auth::check() && (Auth::id() === $review->user_id || Auth::user()->role === 'admin'))
                    <div class="review-actions">
                        @if(Auth::id() === $review->user_id)
                        <a href="{{ route('reviews.edit', $review->id) }}" class="btn-action btn-edit" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        @endif
                        <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" class="inline-form"
                            onsubmit="return confirm('Are you sure you want to delete this review?');">
                            @csrf
                            <button type="submit" class="btn-action btn-delete" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if ($reviews->hasPages())
    <div class="pagination-wrapper">
        <nav aria-label="Reviews pagination">
            <ul class="pagination-list">
                {{-- Previous Button --}}
                @if ($reviews->onFirstPage())
                <li class="pagination-item disabled">
                    <span class="pagination-link"><i class="fas fa-chevron-left"></i> Previous</span>
                </li>
                @else
                <li class="pagination-item">
                    <a class="pagination-link" href="{{ $reviews->previousPageUrl() }}"><i class="fas fa-chevron-left"></i> Previous</a>
                </li>
                @endif

                {{-- Page Numbers --}}
                @foreach ($reviews->getUrlRange(1, $reviews->lastPage()) as $page => $url)
                <li class="pagination-item {{ $page == $reviews->currentPage() ? 'active' : '' }}">
                    <a class="pagination-link" href="{{ $url }}">{{ $page }}</a>
                </li>
                @endforeach

                {{-- Next Button --}}
                @if ($reviews->hasMorePages())
                <li class="pagination-item">
                    <a class="pagination-link" href="{{ $reviews->nextPageUrl() }}">Next <i class="fas fa-chevron-right"></i></a>
                </li>
                @else
                <li class="pagination-item disabled">
                    <span class="pagination-link">Next <i class="fas fa-chevron-right"></i></span>
                </li>
                @endif
            </ul>
        </nav>
    </div>
    @endif
    @endif
</div>

@push('scripts')
<script>
    function toggleHelpful(reviewId) {
        const btn = document.querySelector(`button[data-review-id="${reviewId}"]`);
        const countSpan = btn.querySelector('.helpful-count');

        fetch(`/reviews/${reviewId}/helpful`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.is_helpful) {
                    btn.classList.add('is-helpful');
                } else {
                    btn.classList.remove('is-helpful');
                }
                countSpan.textContent = data.helpful_count;
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
</script>
@endpush
@endsection
