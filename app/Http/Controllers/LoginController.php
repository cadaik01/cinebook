<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{   // Show login form
    public function showLoginForm()
    {
        return view('login.login');
    }
    // Handle login request
    public function login(Request $request)
    {
        //1. take data from request
        $email = $request->input('email');
        $password = $request->input('password');
        //2. find user in database with email
        $user = DB::table('users')->where('email', $email)->first();
        //3. check if user exists and password matches
        if ($user && $user->password === $password) {
            //4. log the user in
            Session::put('user_id', $user->id);
            Session::put('user_name', $user->name);
            //5. redirect to homepage
            return redirect('/');
        } else {
            //6. redirect back with error
            return redirect('/login')->with('error', 'Invalid credentials');
        }
    }
    // Handle logout request
    public function logout()
    {
        Session::forget('user_id');
        Session::forget('user_name');
        return redirect('/');
    }
    // Show registration form
    public function showRegisterForm()
    {
        return view('login.register');
    }
    // Handle registration request
    public function register(Request $request)
    {
        //1. take data from request
        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');
        $city = $request->input('city');
        $phone = $request->input('phone');
        //2. check if user with email already exists
        $existingUser = DB::table('users')->where('email', $email)->first();
        if ($existingUser) {
            return redirect('/register')->with('error', 'Email already registered');
        }
        //3. create new user
        $userId = DB::table('users')->insertGetId([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'city' => $city,
            'phone' => $phone
        ]);
        //4. log the user in
        Session::put('user_id', $userId);
        Session::put('user_name', $name);
        //5. redirect to homepage
        return redirect('/');
    }
}
