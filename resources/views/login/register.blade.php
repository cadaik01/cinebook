@extends('layouts.main')

@section('title', 'TCA Cine - Register')

@section('content')
<div class="auth-container">
    <div class="auth-wrapper">
        <div class="auth-form">
            @if(session('error'))
            <div class="error-alert">
                {{ session('error') }}
            </div>
            @endif

            <h1>Register</h1>
            <p class="subtitle">Create your account to get started</p>

            <form method="POST" action="/register" id="registerForm">
                @csrf
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" required placeholder="Enter your full name">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email">
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" required placeholder="Enter your phone number">
                </div>

                <div class="form-group">
                    <label for="city">City</label>
                    <select name="city" id="city" required>
                        <option value="">Choose your city</option>
                        <option value="TPHCM">Thành phố Hồ Chí Minh</option>
                        <option value="HN">Hà Nội</option>
                        <option value="DN">Đà Nẵng</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Enter password">
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Confirm password">
                    <span id="passwordError" class="validation-error" style="display: none;">Passwords do not match!</span>
                </div>

                <div class="form-actions">
                    <button type="submit" id="submitBtn" class="btn btn-primary">Create Account</button>
                </div>
            </form>

            <div class="auth-link">
                Already have an account? <a href="/login">Login here</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('password_confirmation');
    const errorSpan = document.getElementById('passwordError');
    const submitBtn = document.getElementById('submitBtn');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');

    function validatePasswords() {
        if (confirmPassword.value && password.value !== confirmPassword.value) {
            errorSpan.style.display = 'inline';
            confirmPassword.classList.add('error');
            submitBtn.disabled = true;
        } else {
            errorSpan.style.display = 'none';
            confirmPassword.classList.remove('error');
            submitBtn.disabled = false;
        }
    }

    function validateEmail() {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (emailInput.value && !emailRegex.test(emailInput.value)) {
            emailInput.classList.add('error');
        } else {
            emailInput.classList.remove('error');
        }
    }

    function validatePhone() {
        const phoneRegex = /^[0-9]{10,11}$/;
        if (phoneInput.value && !phoneRegex.test(phoneInput.value)) {
            phoneInput.classList.add('error');
        } else {
            phoneInput.classList.remove('error');
        }
    }

    password.addEventListener('input', validatePasswords);
    confirmPassword.addEventListener('input', validatePasswords);
    emailInput.addEventListener('blur', validateEmail);
    phoneInput.addEventListener('blur', validatePhone);
});
</script>
@endsection