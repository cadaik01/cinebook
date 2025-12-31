<header class="header">
    <nav class="navbar">
        <div class="nav-container">
            <!-- Logo and Brand -->
            <div class="nav-brand">
                <img src="{{ asset('images\tca-cine-logo.jpg') }}" alt="TCA Cine Logo" class="logo" style="width: 40px; height: 40px; object-fit: contain;">
                <h1 class="brand-name">TCA Cine</h1>
            </div>

            <!-- Navigation Menu -->
            <div class="nav-menu" id="nav-menu">
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="" class="nav-link">
                            <i class="fas fa-film"></i>
                            Now Showing
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="" class="nav-link">
                            <i class="fas fa-calendar-alt"></i>
                            Upcoming Movies
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="" class="nav-link">
                            <i class="fas fa-star"></i>
                            Reviews
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Credential Buttons -->
            <div class="nav-auth">
                @if(session('user_id'))
                    <div class="user-menu">
                        <span class="user-greeting">Hello, {{ session('user_name') }}!</span>
                        <a href="" class="btn btn-outline">
                            <i class="fas fa-user"></i>
                            Profile
                        </a>
                        <a href="{{ route('logout') }}" class="btn btn-primary">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </a>
                    </div>
                @else
                    <div class="auth-buttons">
                        <a href="{{ route('login') }}" class="btn btn-outline">
                            <i class="fas fa-sign-in-alt"></i>
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i>
                            Sign Up
                        </a>
                    </div>
                @endif
            </div>

            <!-- Mobile Menu Toggle -->
            <div class="nav-toggle" id="nav-toggle">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </div>
    </nav>
</header>




