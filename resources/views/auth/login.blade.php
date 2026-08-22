<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SSRIS Login - Student–Supervisor Research Interaction System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }
        .login-header {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 2.5rem 2rem;
            text-align: center;
        }
        .login-body {
            padding: 2rem;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-login {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            color: white;
            padding: 0.875rem;
            border-radius: 8px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
        }
        .btn-login:hover {
            background: linear-gradient(45deg, #5568d3, #653a8f);
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        .system-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .system-subtitle {
            font-size: 0.95rem;
            opacity: 0.9;
            line-height: 1.4;
        }
        .login-info {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }
        .login-info h6 {
            color: #495057;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .login-info .badge {
            font-size: 0.8em;
        }
        
        @media (max-width: 575.98px) {
            body {
                padding: 15px;
            }
            .login-header {
                padding: 2rem 1.5rem;
            }
            .login-body {
                padding: 1.5rem;
            }
            .system-title {
                font-size: 1.5rem;
            }
            .system-subtitle {
                font-size: 0.875rem;
            }
        }
        
        @media (max-width: 374.98px) {
            .login-header {
                padding: 1.5rem 1.25rem;
            }
            .login-body {
                padding: 1.25rem;
            }
            .system-title {
                font-size: 1.35rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
                <div class="login-card">
                    <div class="login-header">
                        <div class="system-title">SSRIS</div>
                        <div class="system-subtitle">Student–Supervisor Research Interaction System</div>
                        <div class="system-subtitle mt-1">Moshi Co-operative University</div>
                    </div>
                    <div class="login-body">

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" autocomplete="off" id="loginForm">
                            @csrf
                            <input type="text" style="display:none" aria-hidden="true">
                            <input type="password" style="display:none" aria-hidden="true">

                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-user me-1"></i> Username or Email
                                </label>
                                <input type="text" class="form-control form-control-lg" id="username" name="username"
                                       placeholder="Username or Email"
                                       value="{{ old('username') }}" required autofocus autocomplete="new-username">
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-1"></i> Password
                                </label>
                                <input type="password" class="form-control form-control-lg" id="password" name="password" required autocomplete="new-password">
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">
                                    Remember me
                                </label>
                            </div>

                            <button type="submit" class="btn btn-login btn-lg" id="loginBtn">
                                <i class="fas fa-sign-in-alt me-2"></i> Login
                            </button>
                        </form>

                        <div class="mt-3 text-center">
                            <small class="text-muted">
                                SSRIS © {{ date('Y') }} MoCU
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                document.getElementById('username').value = '';
                document.getElementById('password').value = '';
            }, 100);
        });
    </script>
</body>
</html>
