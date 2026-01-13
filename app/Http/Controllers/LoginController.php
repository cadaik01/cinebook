<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

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
        //find user in database with email
        $user = DB::table('users')->where('email', $email)->first();
        //take is user exists and password matches (plain text for simplicity)
        if($user && $password === $user->password){
            //log the user in
            Session::put('user_id', $user->id);
            Session::put('user_name', $user->name);
            //Redirect to homepage
            return redirect('/');
        }else{
            //redirect back with error message
            return redirect('/login')->with('error', 'Invalid email or password');
        }
    }
    //Handle logout request
    public function logout()
    {
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
        //check if user with email already exists
        $existingUser = DB::table('users')->where('email',$email)->first();
        if($existingUser){
            return redirect('/register')->with('error', 'Email already registered');
        }
        //create new user
        $userId = DB::table('users')->insertGetId([
            'name'=>$name,
            'email'=>$email,
            'password'=>$password,
            'city'=>$city,
            'phone'=>$phone
        ]);
        //log the user in
        Session::put('user_id', $userId);
        Session::put('user_name', $name);
        //redirect to homepage
        return redirect('/');
    }
}