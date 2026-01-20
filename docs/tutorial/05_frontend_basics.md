# 05. LAYOUT VÀ GIAO DIỆN CƠ BẢN

## 🎯 Mục tiêu bài học

Sau bài học này, bạn sẽ có:
- ✅ Master layout với header và footer
- ✅ Navigation menu responsive
- ✅ Trang chủ hiển thị phim
- ✅ Grid system cho movies
- ✅ CSS animations và transitions

**Thời gian ước tính**: 75-90 phút

---

## 📚 Kiến thức cần biết

- Blade layouts và sections
- CSS Flexbox và Grid
- Responsive design basics
- CSS variables (đã học ở bài 01)

---

## 🛠️ BƯỚC 1: TẠO MASTER LAYOUT

### 1.1. Tạo Layout chính

**File**: `resources/views/layouts/app.blade.php`

```blade
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Cinebook - Đặt vé xem phim online')</title>

    {{-- Vite CSS --}}
    @vite(['resources/css/app.css'])

    {{-- Custom page CSS --}}
    @stack('styles')
</head>
<body>
    {{-- Header --}}
    @include('partials.header')

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('partials.footer')

    {{-- Vite JS --}}
    @vite(['resources/js/app.js'])

    {{-- Custom page JS --}}
    @stack('scripts')
</body>
</html>
```

📝 **Giải thích**:
- `@yield('content')`: Nơi các page con sẽ inject nội dung
- `@include('partials.header')`: Include header component
- `@stack('styles')`: Cho phép page con thêm CSS riêng
- `csrf_token()`: Token bảo mật cho AJAX requests

---

## 🛠️ BƯỚC 2: TẠO HEADER

### 2.1. Tạo CSS cho Header

**File**: `resources/css/header.css`

```css
/* Header Styles */

.header {
    background-color: var(--bg-dark);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    position: sticky;
    top: 0;
    z-index: var(--z-sticky);
    box-shadow: var(--shadow-md);
}

.header-container {
    max-width: var(--container-max-width);
    margin: 0 auto;
    padding: 0 var(--spacing-lg);
    height: var(--header-height);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

/* Logo */
.header-logo {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    text-decoration: none;
    font-size: var(--font-size-2xl);
    font-weight: 700;
    color: var(--text-primary);
    transition: transform var(--transition-fast);
}

.header-logo:hover {
    transform: scale(1.05);
}

.header-logo-icon {
    font-size: var(--font-size-3xl);
}

.header-logo-text {
    color: var(--primary-color);
}

/* Navigation */
.header-nav {
    display: flex;
    align-items: center;
    gap: var(--spacing-xl);
}

.header-nav-links {
    display: flex;
    align-items: center;
    gap: var(--spacing-lg);
    list-style: none;
}

.header-nav-link {
    color: var(--text-secondary);
    text-decoration: none;
    font-size: var(--font-size-base);
    font-weight: 500;
    transition: color var(--transition-fast);
    position: relative;
}

.header-nav-link:hover,
.header-nav-link.active {
    color: var(--primary-color);
}

.header-nav-link.active::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 0;
    right: 0;
    height: 2px;
    background-color: var(--primary-color);
}

/* User Menu */
.header-user {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
}

.header-user-name {
    color: var(--text-primary);
    font-weight: 500;
}

.header-user-avatar {
    width: 40px;
    height: 40px;
    border-radius: var(--radius-full);
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: var(--font-size-lg);
}

/* Dropdown Menu */
.header-dropdown {
    position: relative;
}

.header-dropdown-toggle {
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.header-dropdown-menu {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    background-color: var(--bg-card);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-xl);
    min-width: 200px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all var(--transition-base);
}

.header-dropdown:hover .header-dropdown-menu {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.header-dropdown-item {
    display: block;
    padding: var(--spacing-md) var(--spacing-lg);
    color: var(--text-primary);
    text-decoration: none;
    transition: background-color var(--transition-fast);
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.header-dropdown-item:first-child {
    border-top-left-radius: var(--radius-md);
    border-top-right-radius: var(--radius-md);
}

.header-dropdown-item:last-child {
    border-bottom-left-radius: var(--radius-md);
    border-bottom-right-radius: var(--radius-md);
    border-bottom: none;
}

.header-dropdown-item:hover {
    background-color: rgba(255, 255, 255, 0.05);
}

.header-dropdown-item.danger {
    color: var(--error-color);
}

/* Mobile Menu Toggle */
.header-mobile-toggle {
    display: none;
    background: none;
    border: none;
    color: var(--text-primary);
    font-size: var(--font-size-2xl);
    cursor: pointer;
    padding: var(--spacing-sm);
}

/* Responsive */
@media (max-width: 768px) {
    .header-nav-links {
        display: none;
        position: absolute;
        top: var(--header-height);
        left: 0;
        right: 0;
        background-color: var(--bg-card);
        flex-direction: column;
        align-items: flex-start;
        padding: var(--spacing-lg);
        box-shadow: var(--shadow-lg);
    }

    .header-nav-links.show {
        display: flex;
    }

    .header-mobile-toggle {
        display: block;
    }

    .header-user-name {
        display: none;
    }
}

@media (max-width: 480px) {
    .header-logo-text {
        display: none;
    }
}
```

### 2.2. Tạo Header Partial

**File**: `resources/views/partials/header.blade.php`

```blade
<header class="header">
    <div class="header-container">
        {{-- Logo --}}
        <a href="{{ route('home') }}" class="header-logo">
            <span class="header-logo-icon">🎬</span>
            <span class="header-logo-text">CINEBOOK</span>
        </a>

        {{-- Navigation --}}
        <nav class="header-nav">
            <ul class="header-nav-links" id="headerNavLinks">
                <li>
                    <a
                        href="{{ route('home') }}"
                        class="header-nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
                    >
                        Trang chủ
                    </a>
                </li>
                <li>
                    <a
                        href="{{ route('movies.now-showing') }}"
                        class="header-nav-link {{ request()->routeIs('movies.now-showing') ? 'active' : '' }}"
                    >
                        Phim đang chiếu
                    </a>
                </li>
                <li>
                    <a
                        href="{{ route('movies.coming-soon') }}"
                        class="header-nav-link {{ request()->routeIs('movies.coming-soon') ? 'active' : '' }}"
                    >
                        Phim sắp chiếu
                    </a>
                </li>
            </ul>

            {{-- User Menu --}}
            <div class="header-user">
                @auth
                    <div class="header-dropdown">
                        <div class="header-dropdown-toggle">
                            <span class="header-user-name">{{ Auth::user()->name }}</span>
                            <div class="header-user-avatar">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        </div>

                        <div class="header-dropdown-menu">
                            <a href="{{ route('profile.index') }}" class="header-dropdown-item">
                                👤 Hồ sơ
                            </a>
                            <a href="{{ route('profile.bookings') }}" class="header-dropdown-item">
                                🎟️ Vé của tôi
                            </a>
                            <a href="{{ route('profile.reviews') }}" class="header-dropdown-item">
                                ⭐ Đánh giá của tôi
                            </a>

                            @if(Auth::user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="header-dropdown-item">
                                    ⚙️ Admin Panel
                                </a>
                            @endif

                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="header-dropdown-item danger" style="width: 100%; text-align: left; background: none; border: none; cursor: pointer;">
                                    🚪 Đăng xuất
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-secondary btn-sm">
                        Đăng nhập
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">
                        Đăng ký
                    </a>
                @endauth
            </div>

            {{-- Mobile Toggle --}}
            <button class="header-mobile-toggle" id="mobileToggle">
                ☰
            </button>
        </nav>
    </div>
</header>

{{-- Mobile Menu Script --}}
@push('scripts')
<script>
    document.getElementById('mobileToggle')?.addEventListener('click', function() {
        document.getElementById('headerNavLinks')?.classList.toggle('show');
    });
</script>
@endpush
```

---

## 🛠️ BƯỚC 3: TẠO FOOTER

### 3.1. Tạo CSS cho Footer

**File**: `resources/css/footer.css`

```css
/* Footer Styles */

.footer {
    background-color: var(--bg-dark-secondary);
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding: var(--spacing-2xl) 0 var(--spacing-lg);
    margin-top: var(--spacing-2xl);
}

.footer-container {
    max-width: var(--container-max-width);
    margin: 0 auto;
    padding: 0 var(--spacing-lg);
}

.footer-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: var(--spacing-xl);
    margin-bottom: var(--spacing-xl);
}

.footer-section-title {
    font-size: var(--font-size-lg);
    font-weight: 600;
    margin-bottom: var(--spacing-md);
    color: var(--text-primary);
}

.footer-section-text {
    color: var(--text-secondary);
    line-height: 1.6;
    margin-bottom: var(--spacing-sm);
}

.footer-links {
    list-style: none;
}

.footer-link-item {
    margin-bottom: var(--spacing-sm);
}

.footer-link {
    color: var(--text-secondary);
    text-decoration: none;
    transition: color var(--transition-fast);
}

.footer-link:hover {
    color: var(--primary-color);
}

.footer-social {
    display: flex;
    gap: var(--spacing-md);
}

.footer-social-link {
    width: 40px;
    height: 40px;
    border-radius: var(--radius-full);
    background-color: rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-primary);
    text-decoration: none;
    font-size: var(--font-size-xl);
    transition: all var(--transition-base);
}

.footer-social-link:hover {
    background-color: var(--primary-color);
    transform: translateY(-2px);
}

.footer-bottom {
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding-top: var(--spacing-lg);
    text-align: center;
    color: var(--text-muted);
    font-size: var(--font-size-sm);
}

/* Responsive */
@media (max-width: 768px) {
    .footer-grid {
        grid-template-columns: 1fr;
        gap: var(--spacing-lg);
    }
}
```

### 3.2. Tạo Footer Partial

**File**: `resources/views/partials/footer.blade.php`

```blade
<footer class="footer">
    <div class="footer-container">
        <div class="footer-grid">
            {{-- About Section --}}
            <div class="footer-section">
                <h3 class="footer-section-title">🎬 CINEBOOK</h3>
                <p class="footer-section-text">
                    Hệ thống đặt vé xem phim trực tuyến hàng đầu Việt Nam.
                    Trải nghiệm đặt vé nhanh chóng, tiện lợi.
                </p>
                <div class="footer-social">
                    <a href="#" class="footer-social-link" aria-label="Facebook">📘</a>
                    <a href="#" class="footer-social-link" aria-label="Instagram">📷</a>
                    <a href="#" class="footer-social-link" aria-label="YouTube">📺</a>
                    <a href="#" class="footer-social-link" aria-label="Twitter">🐦</a>
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="footer-section">
                <h3 class="footer-section-title">Liên kết</h3>
                <ul class="footer-links">
                    <li class="footer-link-item">
                        <a href="{{ route('home') }}" class="footer-link">Trang chủ</a>
                    </li>
                    <li class="footer-link-item">
                        <a href="{{ route('movies.now-showing') }}" class="footer-link">Phim đang chiếu</a>
                    </li>
                    <li class="footer-link-item">
                        <a href="{{ route('movies.coming-soon') }}" class="footer-link">Phim sắp chiếu</a>
                    </li>
                    @auth
                        <li class="footer-link-item">
                            <a href="{{ route('profile.index') }}" class="footer-link">Hồ sơ</a>
                        </li>
                    @endauth
                </ul>
            </div>

            {{-- Support --}}
            <div class="footer-section">
                <h3 class="footer-section-title">Hỗ trợ</h3>
                <ul class="footer-links">
                    <li class="footer-link-item">
                        <a href="#" class="footer-link">Câu hỏi thường gặp</a>
                    </li>
                    <li class="footer-link-item">
                        <a href="#" class="footer-link">Hướng dẫn đặt vé</a>
                    </li>
                    <li class="footer-link-item">
                        <a href="#" class="footer-link">Chính sách & Điều khoản</a>
                    </li>
                    <li class="footer-link-item">
                        <a href="#" class="footer-link">Liên hệ</a>
                    </li>
                </ul>
            </div>

            {{-- Contact --}}
            <div class="footer-section">
                <h3 class="footer-section-title">Liên hệ</h3>
                <p class="footer-section-text">
                    📍 123 Đường ABC, Quận 1, TP.HCM
                </p>
                <p class="footer-section-text">
                    📞 Hotline: 1900-xxxx
                </p>
                <p class="footer-section-text">
                    📧 Email: support@cinebook.vn
                </p>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} Cinebook. All rights reserved. Made with ❤️ for learning.</p>
        </div>
    </div>
</footer>
```

---

## 🛠️ BƯỚC 4: TẠO TRANG CHỦ

### 4.1. Tạo HomeController

```bash
php artisan make:controller HomeController
```

**File**: `app/Http/Controllers/HomeController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Lấy phim đang chiếu (top 6)
        $nowShowing = Movie::where('status', 'now_showing')
            ->orderBy('rating_avg', 'desc')
            ->take(6)
            ->get();

        // Lấy phim sắp chiếu (top 6)
        $comingSoon = Movie::where('status', 'coming_soon')
            ->orderBy('release_date', 'asc')
            ->take(6)
            ->get();

        // Lấy phim có rating cao nhất
        $topRated = Movie::where('rating_avg', '>', 0)
            ->orderBy('rating_avg', 'desc')
            ->take(3)
            ->get();

        return view('home', compact('nowShowing', 'comingSoon', 'topRated'));
    }
}
```

### 4.2. Tạo CSS cho Home page

**File**: `resources/css/home.css`

```css
/* Home Page Styles */

.hero-section {
    background: linear-gradient(135deg, var(--bg-dark) 0%, var(--bg-dark-secondary) 100%);
    padding: var(--spacing-2xl) 0;
    text-align: center;
}

.hero-title {
    font-size: var(--font-size-4xl);
    margin-bottom: var(--spacing-md);
    background: linear-gradient(135deg, var(--primary-color), #ff6b6b);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-subtitle {
    font-size: var(--font-size-xl);
    color: var(--text-secondary);
    margin-bottom: var(--spacing-xl);
}

/* Section */
.section {
    padding: var(--spacing-2xl) 0;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--spacing-xl);
}

.section-title {
    font-size: var(--font-size-3xl);
    position: relative;
    padding-left: var(--spacing-md);
}

.section-title::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background-color: var(--primary-color);
}

.section-link {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
    transition: color var(--transition-fast);
}

.section-link:hover {
    color: var(--primary-hover);
}

/* Movie Grid */
.movie-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: var(--spacing-xl);
}

.movie-card {
    background-color: var(--bg-card);
    border-radius: var(--radius-lg);
    overflow: hidden;
    transition: all var(--transition-base);
    cursor: pointer;
}

.movie-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-xl);
}

.movie-card-poster {
    width: 100%;
    height: 400px;
    object-fit: cover;
    background-color: var(--bg-dark);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: var(--font-size-4xl);
}

.movie-card-content {
    padding: var(--spacing-lg);
}

.movie-card-title {
    font-size: var(--font-size-lg);
    font-weight: 600;
    margin-bottom: var(--spacing-sm);
    color: var(--text-primary);
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.movie-card-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: var(--font-size-sm);
    color: var(--text-secondary);
    margin-bottom: var(--spacing-md);
}

.movie-card-rating {
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
    color: var(--warning-color);
}

.movie-card-duration {
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.movie-card-genres {
    font-size: var(--font-size-sm);
    color: var(--text-muted);
    margin-bottom: var(--spacing-md);
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.movie-card-action {
    width: 100%;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: var(--spacing-2xl);
    color: var(--text-secondary);
}

.empty-state-icon {
    font-size: 80px;
    margin-bottom: var(--spacing-lg);
}

.empty-state-text {
    font-size: var(--font-size-xl);
}

/* Responsive */
@media (max-width: 768px) {
    .hero-title {
        font-size: var(--font-size-3xl);
    }

    .movie-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: var(--spacing-lg);
    }

    .movie-card-poster {
        height: 300px;
    }
}

@media (max-width: 480px) {
    .movie-grid {
        grid-template-columns: 1fr;
    }
}
```

### 4.3. Tạo Home View

**File**: `resources/views/home.blade.php`

```blade
@extends('layouts.app')

@section('title', 'Trang chủ - Cinebook')

@push('styles')
    @vite(['resources/css/home.css'])
@endpush

@section('content')
    {{-- Hero Section --}}
    <section class="hero-section">
        <div class="container">
            <h1 class="hero-title">Chào mừng đến Cinebook</h1>
            <p class="hero-subtitle">Đặt vé xem phim online nhanh chóng, tiện lợi</p>
            @guest
                <div style="display: flex; gap: 1rem; justify-content: center;">
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                        Đăng ký ngay
                    </a>
                    <a href="{{ route('movies.now-showing') }}" class="btn btn-secondary btn-lg">
                        Xem phim
                    </a>
                </div>
            @endguest
        </div>
    </section>

    {{-- Now Showing Movies --}}
    <section class="section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Phim đang chiếu</h2>
                <a href="{{ route('movies.now-showing') }}" class="section-link">
                    Xem tất cả →
                </a>
            </div>

            @if($nowShowing->count() > 0)
                <div class="movie-grid">
                    @foreach($nowShowing as $movie)
                        <div class="movie-card" onclick="window.location='{{ route('movies.detail', $movie->id) }}'">
                            <div class="movie-card-poster">
                                @if($movie->poster_url)
                                    <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}">
                                @else
                                    🎬
                                @endif
                            </div>
                            <div class="movie-card-content">
                                <h3 class="movie-card-title">{{ $movie->title }}</h3>
                                <div class="movie-card-info">
                                    <span class="movie-card-rating">
                                        ⭐ {{ number_format($movie->rating_avg, 1) }}
                                    </span>
                                    <span class="movie-card-duration">
                                        🕐 {{ $movie->duration }} phút
                                    </span>
                                </div>
                                <div class="movie-card-genres">
                                    {{ $movie->genres->pluck('name')->join(', ') }}
                                </div>
                                <a href="{{ route('movies.detail', $movie->id) }}" class="btn btn-primary btn-block movie-card-action">
                                    Đặt vé
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">🎬</div>
                    <p class="empty-state-text">Hiện chưa có phim nào đang chiếu</p>
                </div>
            @endif
        </div>
    </section>

    {{-- Coming Soon Movies --}}
    <section class="section" style="background-color: var(--bg-dark-secondary);">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Phim sắp chiếu</h2>
                <a href="{{ route('movies.coming-soon') }}" class="section-link">
                    Xem tất cả →
                </a>
            </div>

            @if($comingSoon->count() > 0)
                <div class="movie-grid">
                    @foreach($comingSoon as $movie)
                        <div class="movie-card" onclick="window.location='{{ route('movies.detail', $movie->id) }}'">
                            <div class="movie-card-poster">
                                @if($movie->poster_url)
                                    <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}">
                                @else
                                    🎬
                                @endif
                            </div>
                            <div class="movie-card-content">
                                <h3 class="movie-card-title">{{ $movie->title }}</h3>
                                <div class="movie-card-info">
                                    <span class="movie-card-duration">
                                        📅 {{ $movie->release_date->format('d/m/Y') }}
                                    </span>
                                </div>
                                <div class="movie-card-genres">
                                    {{ $movie->genres->pluck('name')->join(', ') }}
                                </div>
                                <a href="{{ route('movies.detail', $movie->id) }}" class="btn btn-secondary btn-block movie-card-action">
                                    Xem chi tiết
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">📆</div>
                    <p class="empty-state-text">Hiện chưa có phim sắp chiếu</p>
                </div>
            @endif
        </div>
    </section>
@endsection
```

---

## 🛠️ BƯỚC 5: CẬP NHẬT ROUTES

**File**: `routes/web.php`

Thêm routes:

```php
use App\Http\Controllers\HomeController;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Movies (temporary routes - sẽ implement đầy đủ ở bài sau)
Route::get('/movies/now-showing', function() {
    return view('movies.now-showing');
})->name('movies.now-showing');

Route::get('/movies/coming-soon', function() {
    return view('movies.coming-soon');
})->name('movies.coming-soon');

Route::get('/movies/{id}', function() {
    return view('movies.detail');
})->name('movies.detail');

// Profile (temporary routes)
Route::middleware('auth')->group(function() {
    Route::get('/profile', function() {
        return view('profile.index');
    })->name('profile.index');

    Route::get('/profile/bookings', function() {
        return view('profile.bookings');
    })->name('profile.bookings');

    Route::get('/profile/reviews', function() {
        return view('profile.reviews');
    })->name('profile.reviews');
});

// Admin (temporary route)
Route::middleware(['auth', 'role:admin'])->group(function() {
    Route::get('/admin', function() {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});
```

---

## 🛠️ BƯỚC 6: CẬP NHẬT APP.CSS

**File**: `resources/css/app.css`

```css
/* Base Reset & Variables */
@import './root.css';
@import './base.css';

/* Components */
@import './buttons.css';
@import './header.css';
@import './footer.css';
@import './login.css';
@import './home.css';
```

---

## ✅ TEST & VERIFY

### Test 1: Chạy server

```bash
php artisan serve
npm run dev
```

Truy cập: `http://localhost:8000`

✅ **Kết quả**: Trang chủ hiển thị với header, footer, và danh sách phim

### Test 2: Kiểm tra responsive

1. Mở DevTools (F12)
2. Toggle device toolbar (Ctrl+Shift+M)
3. Test trên mobile, tablet views

✅ **Kết quả**: Layout responsive, mobile menu hoạt động

### Test 3: Test navigation

1. Click các link trong menu
2. Kiểm tra active state
3. Test dropdown menu khi đã đăng nhập

✅ **Kết quả**: Navigation hoạt động đúng

---

## 🎯 THỰC HÀNH

### Bài tập 1: Thêm search bar vào header
Thêm ô tìm kiếm phim vào header (chỉ UI)

### Bài tập 2: Customize hero section
Thay đổi gradient, thêm background image

### Bài tập 3: Thêm loading skeleton
Tạo loading animation khi đang load movies

---

## 📝 TÓM TẮT

### Files đã tạo

```
app/Http/Controllers/
└── HomeController.php

resources/
├── css/
│   ├── header.css
│   ├── footer.css
│   └── home.css
└── views/
    ├── layouts/
    │   └── app.blade.php
    ├── partials/
    │   ├── header.blade.php
    │   └── footer.blade.php
    └── home.blade.php
```

---

## 🚀 BƯỚC TIẾP THEO

**Bài tiếp**: [06. Movie Features →](06_movie_features.md)

---

**Bài trước**: [← 04. Authentication](04_authentication.md)
**Series**: Cinebook Tutorial
**Cập nhật**: January 2026
