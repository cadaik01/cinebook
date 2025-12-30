@extends('layouts.main')

@section('title', 'TCA Cine - Homepage')

@section('content')
<h1>Welcome to TCA Cine</h1>
<p>Experience the magic of cinema</p>

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