<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Your Profile')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/root.css', 'resources/css/admin_layout.css'])

    @stack('styles')

    @yield('extra-css')
</head>

<body>
    <div class="admin-layout-wrapper">

        <aside class="admin-layout-sidebar">

            <div class="admin-layout-sidebar-header">
                <h3 style="margin: 0;">
                    <i class="fas fa-film"></i>
                    <a href="/" style="color: inherit; text-decoration: none;">TCA Cine</a>
                </h3>
            </div>

            <nav class="admin-layout-sidebar-nav">

                <ul class="admin-layout-sidebar-menu">

                    {{-- Menu Item 1: User Profile --}}
                    <li class="admin-layout-sidebar-item {{ request()->routeIs('user.profile') ? 'active' : '' }}">
                        <a href="{{ route('user.profile') }}" class="admin-layout-sidebar-link">
                            <i class="fas fa-user"></i>
                            <span>User Profile</span>
                        </a>
                    </li>
                    {{-- Menu Item 2: Booking History --}}
                    <li class="admin-layout-sidebar-item {{ request()->routeIs('user.bookings.*') ? 'active' : '' }}">
                        <a href="{{ route('user.bookings.list') }}" class="admin-layout-sidebar-link">
                            <i class="fas fa-history"></i>
                            <span>Booking History</span>
                        </a>
                    </li>
                    {{-- Menu Item 3: My Reviews --}}
                    <li class="admin-layout-sidebar-item {{ request()->routeIs('user.reviews.*') ? 'active' : '' }}">
                        <a href="{{ route('user.reviews.list') }}" class="admin-layout-sidebar-link">
                            <i class="fas fa-star"></i>
                            <span>My Reviews</span>
                        </a>
                    </li>
                    {{-- Menu Item 4: View Website --}}
                    <li class="admin-layout-sidebar-item">
                        <a href="{{ route('homepage') }}" class="admin-layout-sidebar-link">
                            <i class="fas fa-home"></i>
                            <span>View Website</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>
        <div class="admin-layout-main">

            <nav class="navbar navbar-expand-lg admin-layout-navbar">

                <div class="container-fluid">

                    <button class="btn btn-link sidebar-toggle" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>

                    <span class="navbar-text navbar-title">
                        @yield('page-title', 'Your Profile')
                    </span>

                    <div class="navbar-nav ms-auto">

                        <div class="nav-item dropdown">

                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle"></i>
                                <span class="ms-2">{{ Auth::user()->name }}</span>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">

                                <li>
                                    <a class="dropdown-item" href="{{ route('user.profile.edit') }}">
                                        <i class="fas fa-user me-2"></i>
                                        Edit Your Profile
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('user.profile.change-password') }}">
                                        <i class="fas fa-key me-2"></i>
                                        Change Password
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('user.bookings.list') }}">
                                        <i class="fas fa-history me-2"></i>
                                        Booking History
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('user.reviews.list') }}">
                                        <i class="fas fa-star me-2"></i>
                                        My Reviews
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger"
                                            style="border: none; background: none; width: 100%; text-align: left; cursor: pointer;">
                                            <i class="fas fa-sign-out-alt me-2"></i>
                                            Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <main class="admin-layout-content">

                <div class="container-fluid">

                    {{-- Breadcrumb navigation --}}
                    @yield('breadcrumb')

                    {{-- Alert Messages: show success/error --}}
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    {{-- Main Content Area --}}
                    @yield('content')
                </div>
            </main>

            <footer class="admin-footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="text-muted mb-0">
                                © 2026 TCA Cine
                            </p>
                        </div>
                        <div class="col-md-6 text-end">
                            <p class="text-muted mb-0">
                                Version 1.0.0
                            </p>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    @yield('extra-js')

    {{-- Bootstrap JS for dropdowns and components --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Sidebar Toggle Script --}}
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.admin-layout-sidebar');
        const mainContent = document.querySelector('.admin-layout-main');

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            });
        }
    });
    </script>
</body>

</html>