<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Review;
use App\Models\Movie;

class ReviewController extends Controller
{
    /**
     * Display all public reviews
     */
    public function index()
    {
        $reviews = Review::with(['user', 'movie'])
            ->latest()
            ->paginate(20);

        return view('reviews.index', compact('reviews'));
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
}