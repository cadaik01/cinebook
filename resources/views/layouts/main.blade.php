<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'TCA Cine - Your Ultimate Movie Experience')</title>
    
    <!-- Meta Description -->
    <meta name="description" content="@yield('description', 'TCA Cine - Discover the latest movies, book tickets, and read reviews. Your ultimate cinema experience starts here.')">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Cinzel:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    <!-- Additional CSS -->
    @stack('styles')
    
    <style>
        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            background-color: #0f0f23;
            color: #ffffff;
            overflow-x: hidden;
        }
        
        /* Main Content Area */
        .main-content {
            min-height: calc(100vh - 140px);
            padding-top: 2rem;
        }
        
        /* Container Wrapper */
        .content-wrapper {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        /* Section Spacing */
        .section {
            padding: 3rem 0;
        }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, rgba(26, 26, 46, 0.9), rgba(22, 33, 62, 0.9)), 
                        url('{{ asset("images/cinema-bg.jpg") }}') center/cover;
            min-height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
        }
        
        .hero-content h1 {
            font-family: 'Cinzel', serif;
            font-size: 3.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #f39c12;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
        }
        
        .hero-content p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            color: #ecf0f1;
            max-width: 600px;
        }
        
        /* Utility Classes */
        .text-gold {
            color: #f39c12 !important;
        }
        
        .bg-dark-blue {
            background-color: #1a1a2e;
        }
        
        .bg-darker {
            background-color: #16213e;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2.5rem;
            }
            
            .hero-content p {
                font-size: 1rem;
            }
            
            .main-content {
                padding-top: 1rem;
            }
        }
        
        /* Smooth Scrolling */
        html {
            scroll-behavior: smooth;
        }
        
        /* Loading Animation */
        .loading-spinner {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
        }
        
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #f39c12;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Loading Spinner -->
    <div class="loading-spinner" id="loading-spinner">
        <div class="spinner"></div>
    </div>
    
    <!-- Header -->
    @include('partials.header')
    
    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>
    
    <!-- Footer -->
    @include('partials.footer')
    
    <!-- Back to Top Button -->
    <button class="back-to-top" id="backToTop" title="Back to top">
        <i class="fas fa-chevron-up"></i>
    </button>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery (if needed) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Custom JS -->
    <script src="{{ asset('js/app.js') }}"></script>
    
    <!-- Additional Scripts -->
    @stack('scripts')
    
    <script>
        // Back to Top Button
        window.onscroll = function() {
            const backToTop = document.getElementById('backToTop');
            if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
                backToTop.style.display = 'block';
            } else {
                backToTop.style.display = 'none';
            }
        };
        
        document.getElementById('backToTop').onclick = function() {
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        };
        
        // Loading Animation
        window.addEventListener('load', function() {
            const spinner = document.getElementById('loading-spinner');
            spinner.style.display = 'none';
        });
        
        // Show loading on page navigation
        document.addEventListener('DOMContentLoaded', function() {
            const links = document.querySelectorAll('a[href^="/"], a[href^="{{ url("/") }}"]');
            links.forEach(link => {
                link.addEventListener('click', function() {
                    document.getElementById('loading-spinner').style.display = 'block';
                });
            });
        });
    </script>
    
    <style>
        /* Back to Top Button */
        .back-to-top {
            display: none;
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 99;
            border: none;
            outline: none;
            background-color: #f39c12;
            color: white;
            cursor: pointer;
            padding: 15px;
            border-radius: 50%;
            font-size: 18px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(243, 156, 18, 0.3);
        }
        
        .back-to-top:hover {
            background-color: #e67e22;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(243, 156, 18, 0.4);
        }
        
        @media (max-width: 768px) {
            .back-to-top {
                bottom: 20px;
                right: 20px;
                padding: 12px;
                font-size: 16px;
            }
        }
    </style>
</body>
</html>
