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

        // Nếu là admin thì bỏ qua kiểm tra đã xem phim
        $isAdmin = Auth::user() && Auth::user()->role === 'admin';
        if (!$isAdmin) {
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

        return redirect()->back()->with('success', 'Review deleted successfully.');
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