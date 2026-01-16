@extends('layouts.main')

@section('title', 'Edit Review - ' . $review->movie->title)

@push('styles')
@vite('resources/css/review_edit.css')
@endpush

@section('content')
<div class="review-edit-container">
    <div class="review-edit-header">
        <h1>Edit Your Review</h1>
        <p class="movie-title">for <strong>{{ $review->movie->title }}</strong></p>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="review-edit-card">
        <form action="{{ route('reviews.update', $review->id) }}" method="POST">
            @csrf

            <!-- Star Rating Section -->
            <div class="form-group">
                <label class="form-label">Your Rating</label>
                <div class="star-rating-input">
                    <input type="radio" name="rating" value="5" id="star5" {{ $review->rating == 5 ? 'checked' : '' }} required>
                    <label for="star5" title="5 stars">★</label>

                    <input type="radio" name="rating" value="4" id="star4" {{ $review->rating == 4 ? 'checked' : '' }}>
                    <label for="star4" title="4 stars">★</label>

                    <input type="radio" name="rating" value="3" id="star3" {{ $review->rating == 3 ? 'checked' : '' }}>
                    <label for="star3" title="3 stars">★</label>

                    <input type="radio" name="rating" value="2" id="star2" {{ $review->rating == 2 ? 'checked' : '' }}>
                    <label for="star2" title="2 stars">★</label>

                    <input type="radio" name="rating" value="1" id="star1" {{ $review->rating == 1 ? 'checked' : '' }}>
                    <label for="star1" title="1 star">★</label>
                </div>
            </div>

            <!-- Comment Section -->
            <div class="form-group">
                <label for="comment" class="form-label">Your Comment</label>
                <textarea
                    name="comment"
                    id="comment"
                    class="form-control"
                    rows="6"
                    placeholder="Share your thoughts about this movie..."
                    maxlength="1000">{{ old('comment', $review->comment) }}</textarea>
                <small class="text-muted">Maximum 1000 characters</small>
            </div>

            <!-- Action Buttons -->
            <div class="form-actions">
                <button type="submit" class="review-edit-btn review-edit-btn-primary">
                    <i class="bi bi-check-circle"></i> Update Review
                </button>
                <a href="{{ url()->previous() }}" class="review-edit-btn review-edit-btn-secondary">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- Delete Review Section -->
    <div class="review-delete-section">
        <h3>Delete Review</h3>
        <p>If you want to remove your review completely, you can delete it.</p>
        <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this review? This action cannot be undone.')">
            @csrf
            @method('POST')
            <button type="submit" class="review-edit-btn review-edit-btn-danger">
                <i class="bi bi-trash"></i> Delete Review
            </button>
        </form>
    </div>
</div>
@endsection
