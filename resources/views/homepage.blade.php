@extends('layouts.main')

@section('title', 'TCA Cine - Homepage')

@section('content')
<h1>Welcome to TCA Cine</h1>
<p>Experience the magic of cinema</p>

@if(session('user_id'))
    <!-- Login success -->
    <div style="background: #f0f8ff; padding: 10px; border-radius: 5px; margin: 20px 0;">
        <p><strong>Hi, {{ session('user_name') }}!</strong></p>
        <p>You have successfully logged in.</p>
        <a href="{{ route('logout') }}" style="color: red;">Logout</a>
    </div>
@else
    <!-- Login not success -->
    <div style="background: #fff8dc; padding: 10px; border-radius: 5px; margin: 20px 0;">
        <p>You are not logged in.</p>
        <a href="/login">Login now</a>
    </div>
@endif

<div>
    <a href="">Now Showing</a>
    <a href="">Upcoming Movies</a>
    <a href="{{ route('movies.index') }}">Movie List</a>
</div>

<h2>Ready for Cinema?</h2>
<p>Join thousands of movie lovers at TCA Cine</p>
<div>
    <a href="">Join Now</a>
    <a href="">Contact Us</a>
</div>
@endsection