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
    <button onclick="{{ Route('/Register') }}">Sign In</button>
</form>

@if(session('error'))
    <div class="error-message">
        {{ session('error') }}
    </div>
    
@endif

@endsection