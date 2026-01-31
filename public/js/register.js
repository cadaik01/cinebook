/**
 * Register Form Validation
 */
document.addEventListener('DOMContentLoaded', function() {
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('password_confirmation');
    const errorSpan = document.getElementById('passwordError');
    const submitBtn = document.getElementById('submitBtn');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const nameInput = document.getElementById('name');

    /**
     * Validate password length (minimum 8 characters)
     * Adds error class if password is too short
     */
    function validatePasswordLength() {
        if (password.value && password.value.length < 8) {
            password.classList.add('error');
            password.setCustomValidity('Password must be at least 8 characters long');
            return false;
        } else {
            password.classList.remove('error');
            password.setCustomValidity('');
            return true;
        }
    }

    /**
     * Validate password confirmation match
     * Checks if password and confirm password fields match
     */
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

    /**
     * Validate name - no leading/trailing spaces, no consecutive spaces
     */
    function validateName() {
        const nameValue = nameInput.value;
        // Check for leading/trailing spaces or consecutive spaces
        if (nameValue && (nameValue !== nameValue.trim() || /\s{2,}/.test(nameValue))) {
            nameInput.classList.add('error');
            nameInput.setCustomValidity('Name cannot have leading/trailing spaces or consecutive spaces');
            return false;
        } else {
            nameInput.classList.remove('error');
            nameInput.setCustomValidity('');
            return true;
        }
    }

    /**
     * Validate email format - no whitespace allowed
     * Uses regex pattern to check valid email structure
     */
    function validateEmail() {
        const emailRegex = /^\S+@\S+\.\S+$/;
        if (emailInput.value && !emailRegex.test(emailInput.value)) {
            emailInput.classList.add('error');
            emailInput.setCustomValidity('Email cannot contain spaces');
            return false;
        } else {
            emailInput.classList.remove('error');
            emailInput.setCustomValidity('');
            return true;
        }
    }

    /**
     * Validate phone number format
     * Accepts 10-11 digit phone numbers only
     */
    function validatePhone() {
        const phoneRegex = /^[0-9]{10,11}$/;
        if (phoneInput.value && !phoneRegex.test(phoneInput.value)) {
            phoneInput.classList.add('error');
        } else {
            phoneInput.classList.remove('error');
        }
    }

    // Event listeners
    nameInput.addEventListener('input', validateName);
    emailInput.addEventListener('input', validateEmail);
    password.addEventListener('input', function() {
        validatePasswordLength();
        validatePasswords();
    });
    confirmPassword.addEventListener('input', validatePasswords);
    phoneInput.addEventListener('blur', validatePhone);
    
    // Form submission validation
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        if (!validatePasswordLength() || !validateName() || !validateEmail()) {
            e.preventDefault();
            alert('Please fix all errors before submitting');
            return false;
        }
    });
});
