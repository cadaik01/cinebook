<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('login.login');
    }

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

    public function logout()
    {
        Session::forget('user_id');
        Session::forget('user_name');
        return redirect('/');
    }
}
