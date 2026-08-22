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
        .student-header {
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
        .student-sidebar {
            background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
            min-height: calc(100vh - 60px);
            color: white;
            padding: 20px 0;
            overflow-y: auto;
        }
        .student-profile {
            text-align: center;
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }
        .student-profile-img {
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
        .student-profile-img i {
            font-size: 36px;
            color: white;
        }
        .student-nav-item {
            padding: 12px 20px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.3s;
            border-left: 3px solid transparent;
            cursor: pointer;
        }
        .student-nav-item:hover,
        .student-nav-item.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: #667eea;
        }
        .student-nav-item i {
            width: 25px;
            margin-right: 10px;
        }
        .student-nav-item .arrow {
            margin-left: auto;
            font-size: 12px;
        }
        .student-nav-dropdown {
            position: relative;
        }
        .student-nav-submenu {
            display: none;
            background: rgba(0,0,0,0.2);
            padding-left: 20px;
        }
        .student-nav-submenu.show {
            display: block;
        }
        .student-nav-subitem {
            padding: 10px 20px 10px 45px;
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.3s;
            border-left: 3px solid transparent;
            font-size: 14px;
        }
        .student-nav-subitem:hover,
        .student-nav-subitem.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: #667eea;
        }
        .student-nav-subitem i {
            width: 20px;
            margin-right: 10px;
            font-size: 14px;
        }
        .student-content {
            padding: 20px;
            background: white;
            min-height: calc(100vh - 60px);
        }
        .breadcrumb-item a {
            color: #667eea;
            text-decoration: none;
        }
        .student-info-bar {
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
        .student-info-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #666;
            font-size: 14px;
        }
        .student-info-item i {
            color: #667eea;
        }
        .student-panel {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 15px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .student-panel-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s;
        }
        .student-panel-header:hover {
            background: linear-gradient(135deg, #5568d3 0%, #653a8f 100%);
        }
        .student-panel-header i {
            transition: transform 0.3s;
        }
        .student-panel-header.collapsed i {
            transform: rotate(-90deg);
        }
        .student-panel-body {
            padding: 20px;
            background: white;
            display: block;
        }
        .student-panel-body.hidden {
            display: none;
        }
        .student-panel-body a {
            display: block;
            padding: 8px 0;
            color: #667eea;
            text-decoration: none;
            transition: color 0.3s;
            border-bottom: 1px solid #f0f0f0;
        }
        .student-panel-body a:last-child {
            border-bottom: none;
        }
        .student-panel-body a:hover {
            color: #764ba2;
            padding-left: 5px;
        }
        .student-panel-body a i {
            margin-right: 8px;
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
            .student-sidebar {
                position: fixed;
                left: -280px;
                top: 60px;
                width: 280px;
                height: calc(100vh - 60px);
                z-index: 1001;
                transition: left 0.3s ease;
                overflow-y: auto;
            }
            .student-sidebar.show {
                left: 0;
            }
            .student-content {
                margin-left: 0;
            }
        }
        @media (max-width: 767.98px) {
            .student-content {
                padding: 15px;
            }
            .student-info-bar {
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
            .student-header h5 {
                font-size: 1rem;
            }
            .student-profile {
                padding: 15px;
            }
            .student-profile-img {
                width: 60px;
                height: 60px;
            }
            .student-profile-img i {
                font-size: 28px;
            }
            .student-nav-item {
                padding: 10px 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="student-header">
        <div class="d-flex align-items-center">
            <button class="menu-toggle me-3 d-lg-none" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <h5 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Welcome to SSRIS.</h5>
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
            <div class="col-lg-2 student-sidebar" id="studentSidebar">
                <!-- User Profile -->
                <div class="student-profile">
                    <div class="student-profile-img">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <h6 class="mb-1">{{ auth()->user()->username ?? 'Student' }}</h6>
                    <small class="text-muted">Student</small>
                </div>

                <!-- Navigation -->
                <nav class="student-nav">
            <a href="{{ route('student.dashboard') }}" class="student-nav-item {{ request()->route()->getName() === 'student.dashboard' ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
                <i class="fas fa-chevron-right arrow"></i>
            </a>
            <a href="{{ route('student.research-progress.index') }}" class="student-nav-item {{ request()->route()->getName() === 'student.research-progress.index' ? 'active' : '' }}">
                <i class="fas fa-route"></i> Research Progress
                <i class="fas fa-chevron-right arrow"></i>
            </a>
            <div class="student-nav-dropdown">
                <a href="#" class="student-nav-item dropdown-toggle" onclick="toggleDocumentDropdown(event)">
                    <i class="fas fa-file-alt"></i> Documents
                    <i class="fas fa-chevron-down arrow"></i>
                </a>
                <div class="student-nav-submenu" id="documentSubmenu">
                    <a href="{{ route('student.documents.index') }}?type=concept_notes" class="student-nav-subitem {{ request()->route()->getName() === 'student.documents.index' && request()->get('type') === 'concept_notes' ? 'active' : '' }}">
                        <i class="fas fa-lightbulb"></i> Concept Notes
                    </a>
                    <a href="{{ route('student.documents.index') }}?type=proposal" class="student-nav-subitem {{ request()->route()->getName() === 'student.documents.index' && request()->get('type') === 'proposal' ? 'active' : '' }}">
                        <i class="fas fa-file-alt"></i> Proposals
                    </a>
                    <a href="{{ route('student.documents.index') }}?type=data_collection" class="student-nav-subitem {{ request()->route()->getName() === 'student.documents.index' && request()->get('type') === 'data_collection' ? 'active' : '' }}">
                        <i class="fas fa-database"></i> Data Collection and Analysis
                    </a>
                    <a href="{{ route('student.documents.index') }}?type=report" class="student-nav-subitem {{ request()->route()->getName() === 'student.documents.index' && request()->get('type') === 'report' ? 'active' : '' }}">
                        <i class="fas fa-book"></i> Reports
                    </a>
                </div>
            </div>
            <a href="{{ route('student.meetings.index') }}" class="student-nav-item {{ request()->route()->getName() === 'student.meetings.index' ? 'active' : '' }}">
                <i class="fas fa-calendar"></i> Meetings
                <i class="fas fa-chevron-right arrow"></i>
            </a>
            <a href="{{ route('student.feedback.index') }}" class="student-nav-item {{ request()->route()->getName() === 'student.feedback.index' ? 'active' : '' }}">
                <i class="fas fa-comments"></i> Supervisor Feedback
                @if($pendingFeedbackCount ?? 0 > 0)
                    <span class="badge bg-danger ms-auto">{{ $pendingFeedbackCount }}</span>
                @endif
                <i class="fas fa-chevron-right arrow"></i>
            </a>
        </nav>
            </div>

            <!-- Main Content -->
            <div class="col-lg-10 student-content">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('studentSidebar');
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

        // Panel toggle functionality
        document.querySelectorAll('.student-panel-header').forEach(header => {
            header.addEventListener('click', function() {
                const body = this.nextElementSibling;
                const icon = this.querySelector('i:last-child');
                
                if (body.classList.contains('hidden')) {
                    body.classList.remove('hidden');
                    icon.style.transform = 'rotate(0deg)';
                    this.classList.remove('collapsed');
                } else {
                    body.classList.add('hidden');
                    icon.style.transform = 'rotate(-90deg)';
                    this.classList.add('collapsed');
                }
            });
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('studentSidebar');
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
                const sidebar = document.getElementById('studentSidebar');
                const overlay = document.getElementById('sidebarOverlay');
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
