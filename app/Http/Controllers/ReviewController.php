<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Review;
use App\Models\Movie;

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
        ]);
        //check if user has already reviewed this movie
        $existingReview = Review::where('user_id', Auth::id())
            ->where('movie_id', $request->input('movie_id'))
            ->first();
        if ($existingReview) {
            return redirect()->back()->with('error', 'You have already reviewed this movie.');
        }
        // Create and save the review
        $review = new Review();
        $review->user_id = Auth::id();
        $review->movie_id = $request->input('movie_id');
        $review->rating = $request->input('rating');
        $review->comment = $request->input('comment');
        $review->save();

        return redirect()->back()->with('success', 'Review submitted successfully.');
    }
    // Update movie average rating
    protected function updateMovieAverageRating($movieId)
    {
        $movie = Movie::find($movieId);
        if ($movie) {
            $averageRating = Review::where('movie_id', $movieId)->avg('rating');
            $movie->rating_avg = $averageRating;
            $movie->save();
        }
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
        $this->updateMovieAverageRating($review->movie_id);
        return redirect()->route('user.reviews')->with('success', 'Review updated successfully.');
}
    /**
     * Delete a review
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
        $this->updateMovieAverageRating($movieId);
        return redirect()->route('user.reviews')->with('success', 'Review deleted successfully.');
    }
    // private function to recalculate and update movie average rating
    private function updateMovieRating($movieId)
    {
        $movie = Movie::findOrFail($movieId);
        $avgRating = Review::where('movie_id', $movieId)->avg('rating');
        $movie->update(['rating_avg' => round ($avgRating, 1)]);
    }
}