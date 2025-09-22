<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
        }
        .sidebar {
            width: 250px;
            min-height: 100vh;
            background-color: #0745ca;
            color: white;
        }
        .sidebar .brand {
            text-align: center;
            padding: 20px 10px;
            border-bottom: 1px solid #495057;
        }
        .sidebar .brand img {
            max-width: 100px;
            border-radius: 100%;
            margin-bottom: 10px;
        }
        .sidebar .brand h4 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: bold;
        }
        .sidebar a {
            display: block;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: #495057;
        }
        .content {
            flex-grow: 1;
            padding: 20px;
            background: #f8f9fa;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="brand">
            <img src="{{ asset('images/fm.png') }}" alt="First Mutual Logo">
            <h4>First Mutual</h4>
        </div>
        <a href="{{ route('medical.index') }}" class="{{ request()->routeIs('medical.index') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
        </a>
        <a href="{{ route('medical.create') }}" class="{{ request()->routeIs('medical.create') ? 'active' : '' }}">
            <i class="fas fa-user-plus me-2"></i> Add Student
        </a>
        <a href="#">
            <i class="fas fa-cog me-2"></i> Settings
        </a>
    </div>

    <!-- Main Content -->
    <div class="content">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
