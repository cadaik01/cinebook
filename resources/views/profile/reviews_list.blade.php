@extends('profile.profilepage')

@section('title','Your Reviews')

@section('content')
<div class="admin-header">
    <h2><i class="fas fa-star"></i> Your Reviews</h2>
    <p class="text-muted">Manage and view your submitted reviews</p>
</div>

@if($reviews->isEmpty())
<div class="card text-center py-5">
    <div class="card-body">
        <i class="fas fa-star" style="font-size: 4rem; color: #ddd; margin-bottom: 1rem;"></i>
        <h4 class="text-muted">No Reviews Yet</h4>
        <p class="text-muted">You haven't submitted any reviews yet. Watch a movie and share your thoughts!</p>
        <a href="{{ route('now_showing') }}" class="btn btn-primary mt-3">
            <i class="fas fa-film"></i> Browse Movies
        </a>
    </div>
</div>
@else
<div class="row">
    @foreach($reviews as $review)
    <div class="col-md-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h5 class="card-title mb-2">
                            <i class="fas fa-film text-primary"></i> {{ $review->movie->title ?? 'N/A' }}
                        </h5>
                        <div class="mb-2">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= $review->rating)
                                    <i class="fas fa-star text-warning"></i>
                                @else
                                    <i class="far fa-star text-warning"></i>
                                @endif
                            @endfor
                            <span class="badge bg-warning text-dark ms-2">{{ $review->rating }}/5</span>
                        </div>
                        <p class="card-text text-muted mb-2">
                            <i class="fas fa-quote-left"></i>
                            {{ $review->comment ?? 'No comment provided' }}
                            <i class="fas fa-quote-right"></i>
                        </p>
                        <small class="text-muted">
                            <i class="fas fa-clock"></i> Submitted on {{ $review->created_at->format('d M Y, H:i') }}
                        </small>
                    </div>
                    <div class="col-md-4 d-flex align-items-center justify-content-end">
                        <div class="btn-group-vertical" role="group">
                            <a href="{{ route('reviews.edit', $review->id) }}" class="btn btn-sm btn-outline-primary mb-2">
                                <i class="fas fa-edit"></i> Edit Review
                            </a>
                            <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this review? This action cannot be undone.');">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                    <i class="fas fa-trash"></i> Delete Review
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="mt-4">
    <p class="text-muted text-center">
        <i class="fas fa-info-circle"></i> Total Reviews: <strong>{{ $reviews->count() }}</strong>
    </p>
</div>
@endif
@endsection