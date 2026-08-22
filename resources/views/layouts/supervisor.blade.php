<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        .supervisor-header {
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
        .supervisor-sidebar {
            background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
            min-height: calc(100vh - 60px);
            color: white;
            padding: 20px 0;
            overflow-y: auto;
        }
        .supervisor-profile {
            text-align: center;
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }
        .supervisor-profile-img {
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
        .supervisor-profile-img i {
            font-size: 36px;
            color: white;
        }
        .supervisor-nav-item {
            padding: 12px 20px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.3s;
            border-left: 3px solid transparent;
            cursor: pointer;
        }
        .supervisor-nav-item:hover,
        .supervisor-nav-item.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: #667eea;
        }
        .supervisor-nav-item i {
            width: 25px;
            margin-right: 10px;
        }
        .supervisor-nav-item .arrow {
            margin-left: auto;
            font-size: 12px;
        }
        .supervisor-nav-dropdown {
            position: relative;
        }
        .supervisor-nav-submenu {
            display: none;
            background: rgba(0,0,0,0.2);
            padding-left: 20px;
        }
        .supervisor-nav-submenu.show {
            display: block;
        }
        .supervisor-nav-subitem {
            padding: 10px 20px 10px 45px;
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.3s;
            border-left: 3px solid transparent;
            font-size: 14px;
        }
        .supervisor-nav-subitem:hover,
        .supervisor-nav-subitem.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: #667eea;
        }
        .supervisor-nav-subitem i {
            width: 20px;
            margin-right: 10px;
            font-size: 14px;
        }
        .supervisor-content {
            padding: 20px;
            background: white;
            min-height: calc(100vh - 60px);
        }
        .breadcrumb-item a {
            color: #667eea;
            text-decoration: none;
        }
        .supervisor-info-bar {
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
        .supervisor-info-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #666;
            font-size: 14px;
        }
        .supervisor-info-item i {
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
            .supervisor-sidebar {
                position: fixed;
                left: -280px;
                top: 60px;
                width: 280px;
                height: calc(100vh - 60px);
                z-index: 1001;
                transition: left 0.3s ease;
                overflow-y: auto;
            }
            .supervisor-sidebar.show {
                left: 0;
            }
            .supervisor-content {
                margin-left: 0;
            }
        }
        @media (max-width: 767.98px) {
            .supervisor-content {
                padding: 15px;
            }
            .supervisor-info-bar {
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
            .supervisor-header h5 {
                font-size: 1rem;
            }
            .supervisor-profile {
                padding: 15px;
            }
            .supervisor-profile-img {
                width: 60px;
                height: 60px;
            }
            .supervisor-profile-img i {
                font-size: 28px;
            }
            .supervisor-nav-item {
                padding: 10px 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="supervisor-header">
        <div class="d-flex align-items-center">
            <button class="menu-toggle me-3 d-lg-none" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <h5 class="mb-0"><i class="fas fa-chalkboard-teacher me-2"></i>Welcome to SSRIS - Supervisor.</h5>
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
            <div class="col-lg-2 supervisor-sidebar" id="supervisorSidebar">
                <!-- User Profile -->
                <div class="supervisor-profile">
                    <div class="supervisor-profile-img">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h6 class="mb-1">{{ auth()->user()->name ?? 'Supervisor' }}</h6>
                    <small class="text-muted">Supervisor</small>
                </div>

                <!-- Navigation -->
                <nav class="supervisor-nav">
            <a href="{{ route('supervisor.dashboard') }}" class="supervisor-nav-item {{ request()->route()->getName() === 'supervisor.dashboard' ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
                <i class="fas fa-chevron-right arrow"></i>
            </a>
            <a href="{{ route('supervisor.research-progress.index') }}" class="supervisor-nav-item {{ request()->route()->getName() === 'supervisor.research-progress.index' ? 'active' : '' }}">
                <i class="fas fa-route"></i> Research Progress
                <i class="fas fa-chevron-right arrow"></i>
            </a>
            <a href="{{ route('supervisor.students.index') }}" class="supervisor-nav-item {{ request()->route()->getName() === 'supervisor.students.index' ? 'active' : '' }}">
                <i class="fas fa-user-graduate"></i> Students
                <i class="fas fa-chevron-right arrow"></i>
            </a>
            <div class="supervisor-nav-dropdown">
                <a href="#" class="supervisor-nav-item dropdown-toggle" onclick="toggleDocumentDropdown(event)">
                    <i class="fas fa-file-alt"></i> Documents
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <div class="supervisor-nav-submenu" id="documentSubmenu">
                    <a href="{{ route('supervisor.proposals.index') }}?type=concept_notes" class="supervisor-nav-subitem {{ request()->route()->getName() === 'supervisor.proposals.index' && request()->get('type') === 'concept_notes' ? 'active' : '' }}">
                        <i class="fas fa-lightbulb"></i> Concept Notes
                    </a>
                    <a href="{{ route('supervisor.proposals.index') }}" class="supervisor-nav-subitem {{ request()->route()->getName() === 'supervisor.proposals.index' ? 'active' : '' }}">
                        <i class="fas fa-file-alt"></i> Proposals
                    </a>
                    <a href="{{ route('supervisor.proposals.index') }}?type=data_collection" class="supervisor-nav-subitem {{ request()->route()->getName() === 'supervisor.proposals.index' && request()->get('type') === 'data_collection' ? 'active' : '' }}">
                        <i class="fas fa-database"></i> Data Collection and Analysis
                    </a>
                    <a href="{{ route('supervisor.proposals.index') }}?type=report" class="supervisor-nav-subitem {{ request()->route()->getName() === 'supervisor.proposals.index' && request()->get('type') === 'report' ? 'active' : '' }}">
                        <i class="fas fa-book"></i> Reports
                    </a>
                </div>
            </div>
            <a href="{{ route('supervisor.meetings.index') }}" class="supervisor-nav-item {{ request()->route()->getName() === 'supervisor.meetings.index' ? 'active' : '' }}">
                <i class="fas fa-calendar"></i> Meetings
                <i class="fas fa-chevron-right arrow"></i>
            </a>

        </nav>
            </div>

            <!-- Main Content -->
            <div class="col-lg-10 supervisor-content">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('supervisorSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }

        function toggleDocumentDropdown(event) {
            event.preventDefault();
            const submenu = document.getElementById('documentSubmenu');
            submenu.classList.toggle('show');
            const arrow = event.currentTarget.querySelector('.arrow');
            arrow.classList.toggle('fa-chevron-down');
            arrow.classList.toggle('fa-chevron-up');
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('supervisorSidebar');
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
                const sidebar = document.getElementById('supervisorSidebar');
                const overlay = document.getElementById('sidebarOverlay');
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
