<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SSRIS - Student–Supervisor Research Interaction System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-weight: bold;
        }
        .nav-message-link {
            position: relative;
        }
        .message-notification-badge {
            position: absolute;
            top: -6px;
            right: -10px;
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            border-radius: 999px;
            background: #1877f2;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            line-height: 18px;
            text-align: center;
            box-shadow: 0 0 0 2px #0d6efd;
        }
        .message-notification-badge-inline {
            position: static;
            display: inline-block;
            box-shadow: none;
        }
        .btn.position-relative .message-notification-badge {
            top: -8px;
            right: -8px;
            box-shadow: 0 0 0 2px #fff;
        }
        .chat-container .message-new-indicator {
            font-size: 10px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="fas fa-graduation-cap"></i> SSRIS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @auth
                        @if(auth()->user()->role === 'admin')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a>
                            </li>
                        @elseif(auth()->user()->role === 'supervisor')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('supervisor.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('supervisor.messages.index') }}">
                                    @include('components.message-nav-link', ['count' => $unreadMessageCount ?? 0])
                                </a>
                            </li>
                        @elseif(auth()->user()->role === 'student')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('student.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('student.documents.index') }}?type=proposal">Documents</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('student.messages.index') }}">
                                    @include('components.message-nav-link', ['count' => $unreadMessageCount ?? 0])
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>
                <ul class="navbar-nav">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a></li>
                            </ul>
                        </li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        @yield('content')
    </main>

    <footer class="bg-dark text-white py-3 mt-5">
        <div class="container text-center">
            <p class="mb-0">SSRIS - Student–Supervisor Interaction System | Moshi Co-operative University</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @auth
        @if(in_array(auth()->user()->role, ['student', 'supervisor']))
            <script>
                (function () {
                    const badge = document.getElementById('message-notification-badge');
                    if (!badge) return;

                    const pollUrl = @json(route('messages.unread-count'));
                    let lastCount = parseInt(badge.dataset.count || '0', 10);

                    function updateBadge(count) {
                        if (count <= 0) {
                            badge.classList.add('d-none');
                            badge.textContent = '0';
                            badge.dataset.count = '0';
                            return;
                        }

                        badge.classList.remove('d-none');
                        badge.textContent = count > 99 ? '99+' : String(count);
                        badge.dataset.count = String(count);

                        if (count > lastCount && document.hidden && 'Notification' in window && Notification.permission === 'granted') {
                            new Notification('New message', {
                                body: count === 1 ? 'You have 1 unread message' : `You have ${count} unread messages`,
                            });
                        }

                        lastCount = count;
                    }

                    function pollUnreadCount() {
                        fetch(pollUrl, {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                            credentials: 'same-origin',
                        })
                            .then(response => response.ok ? response.json() : { count: lastCount })
                            .then(data => updateBadge(parseInt(data.count || 0, 10)))
                            .catch(() => {});
                    }

                    if ('Notification' in window && Notification.permission === 'default') {
                        Notification.requestPermission().catch(() => {});
                    }

                    pollUnreadCount();
                    setInterval(pollUnreadCount, 10000);
                })();
            </script>
        @endif
    @endauth
    @stack('scripts')
</body>
</html>
