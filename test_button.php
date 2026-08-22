<!DOCTYPE html>
<html>
<head>
    <title>Test Button</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Button Test</h1>
        
        <div class="card mt-3">
            <div class="card-body">
                <form method="POST" action="http://127.0.0.1:8000/admin/users">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label>Role</label>
                        <select name="role" class="form-control" required>
                            <option value="student">Student</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label>Registration Number</label>
                        <input type="text" name="registration_number" class="form-control" value="MOCU/BBICT/2000/23" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Test Submit Button</button>
                </form>
            </div>
        </div>
        
        <div class="mt-3">
            <button onclick="alert('Button clicked!')" class="btn btn-success">Test Alert Button</button>
        </div>
    </div>
    
    <script>
        console.log('Page loaded');
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded');
            const buttons = document.querySelectorAll('button');
            console.log('Found buttons:', buttons.length);
        });
    </script>
</body>
</html>
