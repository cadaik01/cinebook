@extends('layouts.main')

@section('title', 'TCA Cine - Homepage')

@section('content')

<h1>Register</h1>

@if(session('error'))
    <div style="background: #ffebee; color: #c62828; padding: 10px; border-radius: 5px; margin: 10px 0;">
        {{ session('error') }}
    </div>
@endif

<form method="POST" action="/register" id="registerForm">
    @csrf
    <div>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
    </div>
    <div>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div>
        <label for="phone">Phone number:</label>
        <input type="tel" id="phone" name="phone" required>
    </div>
    <div>
        <label for="city">Your City:</label>
        <select name="city" id="city">
            <option value="City">Choose your city</option>
            <option value="TPHCM">Thành phố Hồ Chí Minh</option>
            <option value="HN">Hà Nội</option>
            <option value="DN">Đà Nẵng</option>
        </select>
    </div>
    <div>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <div>
        <label for="password_confirmation">Confirm Password:</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required>
        <span id="passwordError" style="color: red; display: none;">Passwords do not match!</span>
    </div>
    <button type="submit" id="submitBtn">Register</button>
</form>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('password_confirmation');
    const errorSpan = document.getElementById('passwordError');
    const submitBtn = document.getElementById('submitBtn');
    
    function validatePasswords() {
        if (confirmPassword.value && password.value !== confirmPassword.value) {
            errorSpan.style.display = 'inline';
            submitBtn.disabled = true;
            submitBtn.style.backgroundColor = '#ccc';
        } else {
            errorSpan.style.display = 'none';
            submitBtn.disabled = false;
            submitBtn.style.backgroundColor = '';
        }
    }
    
    password.addEventListener('input', validatePasswords);
    confirmPassword.addEventListener('input', validatePasswords);
});
</script>

@endsection