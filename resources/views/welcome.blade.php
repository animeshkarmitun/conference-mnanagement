<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conference Management System</title>
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f8fafc;
        }
        h1 {
            font-size: 2.5rem;
            color: #22223b;
            letter-spacing: 1px;
            margin-bottom: 2rem;
        }
        .btn {
            display: inline-block;
            padding: 0.75rem 2rem;
            font-size: 1rem;
            color: #fff;
            background: #22223b;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            transition: background 0.2s;
            margin-top: 1rem;
        }
        .btn:hover {
            background: #4a4e69;
        }
    </style>
</head>
<body>
    <h1>Conference Management System</h1>
    @if (Route::has('login'))
        @auth
            <a href="{{ url('/dashboard') }}" class="btn">Dashboard</a>
        @else
            <a href="{{ route('login') }}" class="btn">Login</a>
        @endauth
    @endif
</body>
</html>
