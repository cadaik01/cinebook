<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    //Show login form
    public function showloginForm()
    {
        return view('login.login');
    }

    //Handle login request
    public function login(Request $request)
    {
        //take data from request
        $email = $request->input('email');
        $password = $request->input('password');
        //2. find user in database with email
        $user = User::where('email', $email)->first();
        //3. check if user exists and password matches
        if ($user && $password === $user->password) {
            //4. log the user in using Laravel Auth
            Auth::login($user);
            // Also keep session for backward compatibility with existing code
            Session::put('user_id', $user->id);
            Session::put('user_name', $user->name);
            //5. redirect to homepage
            return redirect()->intended('/');
        } else {
            //6. redirect back with error
            return redirect('/login')->with('error', 'Invalid credentials');
        }
    }
    //Handle logout request
    public function logout()
    {
        // Laravel Auth logout
        Auth::logout();
        
        // Clear session
        Session::forget('user_id');
        Session::forget('user_name');
        
        return redirect('/');
    }
    //Show registration form
    public function showRegisterForm()
    {
        return view('login.register');
    }
    //Handle registration request
    public function register(Request $request)
    {
        //take data from request
        $name = $request->input('name');
        $email= $request->input('email');
        $password = $request->input('password');
        $city = $request->input('city');
        $phone = $request->input('phone');
        //2. check if user with email already exists
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            return redirect('/register')->with('error', 'Email already registered');
        }
        //3. create new user
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password, // storing plain text per request
            'city' => $city,
            'phone' => $phone,
            'role' => 'user' // default role
        ]);
        //4. log the user in using Laravel Auth
        Auth::login($user);
        // Also keep session for backward compatibility
        Session::put('user_id', $user->id);
        Session::put('user_name', $user->name);
        //5. redirect to homepage
        return redirect('/');
    }
}