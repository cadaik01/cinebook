@extends('profile.profilepage')

@section('title','Your Reviews')

@section('content')
<div class="admin-header">
    <h2><i class="fas fa-star"></i> Your Reviews</h2>
    <p class="text-muted">Manage and view your submitted reviews</p>
</div>
<div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
        <thead class="table-dark">
            <tr>
                <th scope="col">Movie</th>
                <th scope="col">Rating</th>
                <th scope="col">Comment</th>
                <th scope="col">Submitted At</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reviews as $review)
            <tr>
                <td>{{ $review->movie->title ?? 'N/A' }}</td>
                <td>
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($i <= $review->rating)
                            <i class="fas fa-star text-warning"></i>
                        @else
                            <i class="far fa-star text-warning"></i>
                        @endif
                    @endfor
                </td>
                <td>{{ $review->comment }}</td>
                <td>{{ $review->created_at->format('d M Y, H:i') }}</td>
                <td>
                    <div class="btn-group" role="group">
                        <a href="{{ route('profile.reviews_edit', $review->id) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('profile.reviews_destroy', $review->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this review?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
            @empty
            <tr>
                <td colspan="5" class="text-center text-muted">You have not submitted any reviews yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection