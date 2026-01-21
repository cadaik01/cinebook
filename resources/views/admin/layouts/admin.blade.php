<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Admin Panel - TCA Cine')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- @vite() compile CSS từ resources/ vào browser + auto hot reload khi sửa (cần npm run dev chạy liên tục) --}}
    @vite(['resources/css/admin_layout.css'])

    @yield('extra-css')
</head>

<body>
    <div class="admin-layout-wrapper">

        <aside class="admin-layout-sidebar">

            <div class="admin-layout-sidebar-header">
                <h3>
                    <i class="fas fa-film"></i>
                    TCA Cine Admin
                </h3>
            </div>

            <nav class="admin-layout-sidebar-nav">

                <ul class="admin-layout-sidebar-menu">

                    {{-- Menu Item 1: Dashboard --}}
                    <li class="admin-layout-sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}" class="admin-layout-sidebar-link">
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
                                © 2026 TCA Cine Admin Panel
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    // Toggle Sidebar for Mobile
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.admin-sidebar');
        const wrapper = document.querySelector('.admin-wrapper');

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                wrapper.classList.toggle('sidebar-collapsed');
            });
        }
    });
    </script>

    @yield('extra-js')
</body>

</html>