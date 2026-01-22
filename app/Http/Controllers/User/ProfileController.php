<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Show user profile page
     */
    public function userProfile()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();//Authenticated user who is logged in (1: admin, 2: regular user, 3: guest)
        
        // Get profile stats using User model methods
        if($user->isAdmin()){
            return redirect('/admin/dashboard');
        }
        $userInfo = $user->getUserInfo();
        
        return view('profile.userprofile', compact('user', 'userInfo'));
    }

    /**
     * Show user's booking history
     */
    public function bookingsList()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();//Authenticated user who is logged in (1: admin, 2: regular user, 3: guest)
        
        // Get bookings using User model methods
        $upcomingBookings = $user->getUpcomingBookings();//Get all upcoming bookings for the user-this is an array of Booking objects
        $pastBookings = $user->getPastBookings();//Get all past bookings for the user-this is an array of Booking objects
        
        return view('profile.bookings_list', compact('upcomingBookings', 'pastBookings'));
    }

    /**
     * Show user's reviews list
     */
    public function reviewsList()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();//Authenticated user who is logged in (1: admin, 2: regular user, 3: guest)
        $reviews = $user->reviews()->with('movie')->orderBy('created_at', 'desc')->get();

        return view('profile.reviews_list', compact('reviews'));
    }

    //CRUD user profile
    /**
     * Update user profile
     */
    // Show edit profile form
    public function editProfile()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();//Authenticated user who is logged in (1: admin, 2: regular user, 3: guest)
        return view('profile.edit', compact('user'));
    }
    // Handle profile update
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();//Authenticated user who is logged in (1: admin, 2: regular user, 3: guest)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
            'avatar' => 'nullable|image|max:2048',
        ]);
        $user->name = $validated['name'];
        $user->phone = $validated['phone'] ?? $user->phone;
        $user->city = $validated['city'] ?? $user->city;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar_url = $avatarPath;
        }
        $user->save();
                
        return redirect()->route('user.profile')->with('success', 'Profile updated successfully.');
    }
    /**
     * Change user password
     */
    // Show change password form
    public function showChangePasswordForm()
    {
        return view('profile.changepw');
    } 
    // Handle password change
    public function changePassword(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();//Authenticated user who is logged in (1: admin, 2: regular user, 3: guest)
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // Check if current password matches (plain text comparison)
        if ($user->password !== $validated['current_password']) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }
        
        //check new password is different from current password
        if ($validated['new_password'] === $validated['current_password']) {
            return redirect()->back()->withErrors(['new_password' => 'New password must be different from the current password.']);
        }
        
        // Update to new password (store as plain text)
        $user->password = $validated['new_password'];
        $user->save();

        return redirect()->route('user.profile')->with('success', 'Password changed successfully.');
    }
}