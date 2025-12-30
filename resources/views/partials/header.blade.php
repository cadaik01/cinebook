<header class="header">
    <nav class="navbar">
        <div class="nav-container">
            <!-- Logo and Brand -->
            <div class="nav-brand">
                <img src="{{ asset('images\tca-cine-logo.jpg') }}" alt="TCA Cine Logo" class="logo" style="size: 1px">
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

            <!-- Auth Buttons -->
            <div class="nav-auth">
                @auth
                    <div class="user-menu">
                        <span class="user-greeting">Hello, {{ Auth::user()->name }}!</span>
                        <a href="" class="btn btn-outline">
                            <i class="fas fa-user"></i>
                            Profile
                        </a>
                        <form action="" method="POST" class="logout-form">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-sign-out-alt"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                @else
                    <div class="auth-buttons">
                        <a href="" class="btn btn-outline">
                            <i class="fas fa-sign-in-alt"></i>
                            Login
                        </a>
                        <a href="" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i>
                            Sign Up
                        </a>
                    </div>
                @endauth
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




