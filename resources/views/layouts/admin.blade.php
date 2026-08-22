<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SSRIS - Student–Supervisor Research Interaction System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }
        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1020;
        }
        .admin-sidebar {
            background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
            min-height: calc(100vh - 60px);
            color: white;
            padding: 20px 0;
            overflow-y: auto;
        }
        .admin-profile {
            text-align: center;
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }
        .admin-profile-img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            border: 3px solid rgba(255,255,255,0.2);
        }
        .admin-profile-img i {
            font-size: 36px;
            color: white;
        }
        .admin-nav-item {
            padding: 12px 20px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.3s;
            border-left: 3px solid transparent;
            cursor: pointer;
        }
        .admin-nav-item:hover,
        .admin-nav-item.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: #667eea;
        }
        .admin-nav-item i {
            width: 25px;
            margin-right: 10px;
        }
        .admin-nav-item .arrow {
            margin-left: auto;
            font-size: 12px;
        }
        .admin-content {
            padding: 20px;
            background: white;
            min-height: calc(100vh - 60px);
        }
        .breadcrumb-item a {
            color: #667eea;
            text-decoration: none;
        }
        .admin-info-bar {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            border: 1px solid #e0e0e0;
        }
        .admin-info-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #666;
            font-size: 14px;
        }
        .admin-info-item i {
            color: #667eea;
        }
        .menu-toggle {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
        }
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            display: none;
        }
        .sidebar-overlay.show {
            display: block;
        }
        .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }
        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        .table-responsive::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        .card-title {
            font-size: 1.1rem;
        }
        .btn {
            white-space: nowrap;
        }
        @media (max-width: 991.98px) {
            .admin-sidebar {
                position: fixed;
                left: -280px;
                top: 60px;
                width: 280px;
                height: calc(100vh - 60px);
                z-index: 1001;
                transition: left 0.3s ease;
                overflow-y: auto;
            }
            .admin-sidebar.show {
                left: 0;
            }
            .admin-content {
                margin-left: 0;
            }
        }
        @media (max-width: 767.98px) {
            .admin-content {
                padding: 15px;
            }
            .admin-info-bar {
                flex-direction: column;
                align-items: flex-start;
                padding: 10px;
            }
            .card {
                margin-bottom: 15px;
            }
            .btn {
                font-size: 0.875rem;
                padding: 0.375rem 0.75rem;
            }
        }
        @media (max-width: 575.98px) {
            .admin-header h5 {
                font-size: 1rem;
            }
            .admin-profile {
                padding: 15px;
            }
            .admin-profile-img {
                width: 60px;
                height: 60px;
            }
            .admin-profile-img i {
                font-size: 28px;
            }
            .admin-nav-item {
                padding: 10px 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="admin-header">
        <div class="d-flex align-items-center">
            <button class="menu-toggle me-3 d-lg-none" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <h5 class="mb-0"><i class="fas fa-user-shield me-2"></i>Welcome to SSRIS - Admin.</h5>
        </div>
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-outline-light btn-sm">
                <i class="fas fa-sign-out-alt me-1"></i> <span class="d-none d-sm-inline">Log out</span>
            </button>
        </form>
    </div>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-2 admin-sidebar" id="adminSidebar">
                <!-- User Profile -->
                <div class="admin-profile">
                    <div class="admin-profile-img">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h6 class="mb-1">{{ auth()->user()->name ?? 'Admin' }}</h6>
                    <small class="text-muted">Administrator</small>
                </div>

                <!-- Navigation -->
                <nav class="admin-nav">
                    <a href="{{ route('admin.dashboard') }}" class="admin-nav-item {{ request()->route()->getName() === 'admin.dashboard' ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                        <i class="fas fa-chevron-right arrow"></i>
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="admin-nav-item {{ request()->route()->getName() === 'admin.users.index' ? 'active' : '' }}">
                        <i class="fas fa-users"></i> Users
                        <i class="fas fa-chevron-right arrow"></i>
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="admin-nav-item {{ request()->route()->getName() === 'admin.reports.index' ? 'active' : '' }}">
                        <i class="fas fa-chart-bar"></i> Reports
                        <i class="fas fa-chevron-right arrow"></i>
                    </a>
                    <a href="{{ route('admin.interaction-tracking.index') }}" class="admin-nav-item {{ request()->route()->getName() === 'admin.interaction-tracking.index' ? 'active' : '' }}">
                        <i class="fas fa-history"></i> Interaction Tracking
                        <i class="fas fa-chevron-right arrow"></i>
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-lg-10 admin-content">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const menuToggle = document.querySelector('.menu-toggle');
            
            if (window.innerWidth < 992 && 
                !sidebar.contains(event.target) && 
                !menuToggle.contains(event.target) &&
                sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            }
        });
        
        // Close sidebar when window is resized to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 992) {
                const sidebar = document.getElementById('adminSidebar');
                const overlay = document.getElementById('sidebarOverlay');
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
