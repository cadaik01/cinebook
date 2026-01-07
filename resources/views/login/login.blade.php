@extends('layouts.main')

@section('title', 'TCA Cine - Homepage')

@section('content')

<h1>Login</h1>
<form method="POST" action="/login">
    @csrf
    <div>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <button type="submit">Login</button>
    <a href="{{ route('register') }}" style="background: none; border: none; color: blue; text-decoration: underline;">Sign Up</a>
</form>

@if(session('error'))
    <div class="error-message">
        {{ session('error') }}
    </div>
@endif

@if (session('success'))
    <div class="success-message">
        {{ session('success') }}
    </div>
@endif

@endsection