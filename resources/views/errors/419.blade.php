<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Expired - SSRIS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">
    <div class="text-center p-4">
        <h1 class="display-6 text-warning">Session Expired</h1>
        <p class="text-muted mb-4">This page has expired. Please go back to login.</p>
        <a href="{{ url('/login') }}" class="btn btn-primary">Back to Login</a>
    </div>
</body>
</html>
