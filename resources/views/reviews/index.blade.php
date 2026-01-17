@extends('layouts.main')

@section('title', 'Movie Reviews - TCA Cine')

@push('styles')
@vite('resources/css/reviews_index.css')
@endpush

@section('content')
<div class="reviews-public-container">
    <div class="reviews-public-header">
        <h1><i class="fas fa-star"></i> Movie Reviews</h1>
        <p class="subtitle">See what others are saying about our movies</p>
    </div>

    @if($reviews->isEmpty())
    <div class="no-reviews-card">
        <i class="fas fa-star-half-alt"></i>
        <h3>No Reviews Yet</h3>
        <p>Be the first to share your thoughts! Watch a movie and leave a review.</p>
        <a href="{{ route('now_showing') }}" class="btn-browse">
            <i class="fas fa-ticket-alt"></i> Book Tickets Now
        </a>
    </div>
    @else
    <div class="reviews-grid">
        @foreach($reviews as $review)
        <div class="review-card">
            <div class="review-card-header">
                <div class="movie-info">
                    <h3 class="movie-title">
                        <i class="fas fa-film"></i> {{ $review->movie->title ?? 'N/A' }}
                    </h3>
                    <div class="rating-display">
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= $review->rating)
                                <span class="star-filled">★</span>
                            @else
                                <span class="star-empty">★</span>
                            @endif
                        @endfor
                        <span class="rating-badge">{{ $review->rating }}/5</span>
                    </div>
                </div>
            </div>
            <div class="review-card-body">
                <p class="review-comment">
                    <i class="fas fa-quote-left quote-icon"></i>
                    {{ $review->comment ?? 'No comment provided' }}
                    <i class="fas fa-quote-right quote-icon"></i>
                </p>
            </div>
            <div class="review-card-footer">
                <div class="reviewer-info">
                    <i class="fas fa-user-circle"></i>
                    <span class="reviewer-name">{{ $review->user->name ?? 'Anonymous' }}</span>
                </div>
                <div class="review-date">
                    <i class="fas fa-clock"></i>
                    <span>{{ $review->created_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if ($reviews->hasPages())
    <div class="pagination-wrapper">
        <nav aria-label="Reviews pagination">
            <ul class="pagination-list">
                {{-- Previous Button --}}
                @if ($reviews->onFirstPage())
                <li class="pagination-item disabled">
                    <span class="pagination-link">← Previous</span>
                </li>
                @else
                <li class="pagination-item">
                    <a class="pagination-link" href="{{ $reviews->previousPageUrl() }}">← Previous</a>
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
                    <a class="pagination-link" href="{{ $reviews->nextPageUrl() }}">Next →</a>
                </li>
                @else
                <li class="pagination-item disabled">
                    <span class="pagination-link">Next →</span>
                </li>
                @endif
            </ul>
        </nav>
    </div>
    @endif
    @endif
</div>
@endsection
