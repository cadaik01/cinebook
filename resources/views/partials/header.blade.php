<header class="header">
    <nav class="navbar">
        <div class="nav-container">
            <!-- Logo and Brand -->
            <div class="nav-brand">
                <img src="{{ asset('images\tca-cine-logo.jpg') }}" alt="TCA Cine Logo" class="logo" style="width: 40px; height: 40px; object-fit: contain;">
                <h1 class="brand-name"><a href="{{ route('homepage') }}">TCA Cine</a></h1>
            </div>

            <!-- Navigation Menu -->
            <div class="nav-menu" id="nav-menu">
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="{{ route('homepage') }}" class="nav-link">
                            <i class="fas fa-film"></i>
                            HomePage
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('now_showing') }}" class="nav-link">
                            <i class="fas fa-film"></i>
                            Now Showing
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('upcoming_movies') }}" class="nav-link">
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
                @php
                    $currentUser = session('user_id') ? \App\Models\User::find(session('user_id')) : null;
                @endphp
                @if($currentUser)
                    <div class="user-menu">
                        <span class="user-greeting">Hello, {{ $currentUser->name }}!</span>
                        @if($currentUser->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-warning">
                                <i class="fas fa-tools"></i>
                                Admin Panel
                            </a>
                        @endif
                        <a href="" class="btn btn-outline">
                            <i class="fas fa-user"></i>
                            Profile
                        </a>
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-primary" style="border: none; cursor: pointer;">
                                <i class="fas fa-sign-out-alt"></i>
                                Logout
                            </button>
                        </form>
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




