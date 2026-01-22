<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Review;
use App\Models\Movie;
use App\Models\ReviewHelpful;

class ReviewController extends Controller
{
    /**
     * Display all public reviews with filtering, sorting, and stats
     */
    public function index(Request $request)
    {
        // Build query with filters
        $query = Review::with(['user', 'movie', 'helpfuls']);

        // Filter by movie
        if ($request->filled('movie_id')) {
            $query->byMovie($request->movie_id);
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $query->byRating($request->rating);
        }

        // Sorting
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'highest':
                $query->highestRated();
                break;
            case 'lowest':
                $query->lowestRated();
                break;
            case 'helpful':
                $query->mostHelpful();
                break;
            default: // newest
                $query->latest();
                break;
        }

        $reviews = $query->paginate(12)->withQueryString();

        // Get stats
        $stats = [
            'total_reviews' => Review::count(),
            'average_rating' => round(Review::avg('rating'), 1) ?: 0,
            'total_reviewers' => Review::distinct('user_id')->count('user_id'),
            'top_movie' => Movie::withCount('reviews')
                ->having('reviews_count', '>', 0)
                ->orderBy('reviews_count', 'desc')
                ->first()
        ];

        // Get rating distribution
        $ratingDistribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $count = Review::where('rating', $i)->count();
            $ratingDistribution[$i] = [
                'count' => $count,
                'percentage' => $stats['total_reviews'] > 0
                    ? round(($count / $stats['total_reviews']) * 100)
                    : 0
            ];
        }

        // Get all movies for filter dropdown
        $movies = Movie::whereHas('reviews')->orderBy('title')->get();

        return view('reviews.index', compact('reviews', 'stats', 'ratingDistribution', 'movies'));
    }

    /**
     * Store a new review
     */
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'You must be logged in to submit a review.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'movie_id' => 'required|exists:movies,id',
        ]);

        $movieId = $request->input('movie_id');
        $userId = Auth::id();

        // Check if user has already reviewed this movie
        $existingReview = Review::where('user_id', $userId)
            ->where('movie_id', $movieId)
            ->first();

        if ($existingReview) {
            return redirect()->back()->with('error', 'You have already reviewed this movie.');
        }

        // Check if user has watched this movie (showtime must be in the past)
        $hasWatched = \DB::table('booking_seats')
            ->join('showtimes', 'booking_seats.showtime_id', '=', 'showtimes.id')
            ->join('bookings', 'booking_seats.booking_id', '=', 'bookings.id')
            ->where('bookings.user_id', $userId)
            ->where('showtimes.movie_id', $movieId)
            ->where('bookings.payment_status', 'paid')
            ->where(function($query) {
                $query->where('showtimes.show_date', '<', now()->toDateString())
                    ->orWhere(function($q) {
                        $q->where('showtimes.show_date', '=', now()->toDateString())
                          ->where('showtimes.show_time', '<', now()->toTimeString());
                    });
            })
            ->exists();

        if (!$hasWatched) {
            return redirect()->back()->with('error', 'You can only review movies you have watched.');
        }

        // Create and save the review
        $review = Review::create([
            'user_id' => $userId,
            'movie_id' => $movieId,
            'rating' => $request->input('rating'),
            'comment' => $request->input('comment'),
        ]);

        // Update movie average rating
        $movie = Movie::find($movieId);
        $movie->updateAverageRating();

        return redirect()->back()->with('success', 'Review submitted successfully.');
    }

    /**
     * Show the form for editing a review
     */
    public function edit($id)
    {
        $review = Review::with('movie')->findOrFail($id);

        // Ensure the authenticated user is the owner of the review
        if (Auth::id() !== $review->user_id) {
            return redirect()->back()->with('error', 'You are not authorized to edit this review.');
        }

        return view('reviews.edit', compact('review'));
    }

    /**
     * Update an existing review
     */
    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        // Ensure the authenticated user is the owner of the review
        if (Auth::id() !== $review->user_id) {
            return redirect()->back()->with('error', 'You are not authorized to edit this review.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        // Update movie average rating
        $movie = Movie::find($review->movie_id);
        $movie->updateAverageRating();

        return redirect()->route('user.reviews.list')->with('success', 'Review updated successfully.');
    }
    /**
     * Delete a review (User can delete own review)
     */
    public function destroy($id)
    {
        $review = Review::findOrFail($id);

        // Ensure the authenticated user is the owner of the review
        if (Auth::id() !== $review->user_id) {
            return redirect()->back()->with('error', 'You are not authorized to delete this review.');
        }

        $movieId = $review->movie_id;
        $review->delete();

        // Update movie average rating
        $movie = Movie::find($movieId);
        $movie->updateAverageRating();

        return redirect()->route('user.reviews.list')->with('success', 'Review deleted successfully.');
    }

    /**
     * Check if user can review a specific movie
     */
    public function canReview($movieId)
    {
        if (!Auth::check()) {
            return false;
        }

        $userId = Auth::id();

        // Check if already reviewed
        $hasReviewed = Review::where('user_id', $userId)
            ->where('movie_id', $movieId)
            ->exists();

        if ($hasReviewed) {
            return false;
        }

        // Check if user has watched this movie
        $hasWatched = \DB::table('booking_seats')
            ->join('showtimes', 'booking_seats.showtime_id', '=', 'showtimes.id')
            ->join('bookings', 'booking_seats.booking_id', '=', 'bookings.id')
            ->where('bookings.user_id', $userId)
            ->where('showtimes.movie_id', $movieId)
            ->where('bookings.payment_status', 'paid')
            ->where(function($query) {
                $query->where('showtimes.show_date', '<', now()->toDateString())
                    ->orWhere(function($q) {
                        $q->where('showtimes.show_date', '=', now()->toDateString())
                          ->where('showtimes.show_time', '<', now()->toTimeString());
                    });
            })
            ->exists();

        return $hasWatched;
    }

    /**
     * Toggle helpful mark on a review
     */
    public function toggleHelpful(Request $request, $reviewId)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to mark reviews as helpful.'
            ], 401);
        }

        $review = Review::findOrFail($reviewId);
        $userId = Auth::id();

        // Users cannot mark their own reviews as helpful
        if ($review->user_id === $userId) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot mark your own review as helpful.'
            ], 403);
        }

        // Toggle helpful mark
        $existing = ReviewHelpful::where('review_id', $reviewId)
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            $existing->delete();
            $isHelpful = false;
        } else {
            ReviewHelpful::create([
                'review_id' => $reviewId,
                'user_id' => $userId
            ]);
            $isHelpful = true;
        }

        return response()->json([
            'success' => true,
            'is_helpful' => $isHelpful,
            'helpful_count' => $review->helpfuls()->count()
        ]);
    }
}