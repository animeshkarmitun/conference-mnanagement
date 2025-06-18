<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CGS Events') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts and Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Fallback for production -->
        @if (app()->environment('production'))
            <link rel="stylesheet" href="{{ asset('build/assets/app.css') }}">
            <script src="{{ asset('build/assets/app.js') }}" defer></script>
        @endif
    </head>
    <body class="bg-gray-100 font-sans antialiased">
        <div class="min-h-screen flex">
            <!-- Sidebar -->
            <aside class="w-64 bg-white shadow-lg flex flex-col">
                <div class="h-20 flex items-center justify-center border-b">
                    <span class="text-2xl font-bold text-yellow-700">CGS Events</span>
                </div>
                <nav class="flex-1 px-4 py-6 space-y-2">
                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800">Dashboard</a>
                    <a href="{{ route('conferences.index') }}" class="block px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800">Conferences</a>
                    <a href="{{ route('participants.index') }}" class="block px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800">Participants</a>
                    <a href="{{ route('sessions.index') }}" class="block px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800">Sessions</a>
                    <a href="{{ route('tasks.index') }}" class="block px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800">Tasks</a>
                    <a href="{{ route('notifications.index') }}" class="block px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800">Notifications</a>
                    <a href="{{ route('speaker.register') }}" class="block px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800">Speaker Registration</a>
                    <a href="{{ route('guide') }}" class="block px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800">How to Use</a>
                </nav>
                <div class="border-t p-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center text-yellow-700 font-bold">
                            {{ strtoupper(substr(auth()->user()->first_name,0,1)) }}
                        </div>
                        <div>
                            <div class="font-semibold text-gray-800">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-xs text-gray-500 hover:text-yellow-700">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </aside>
            <!-- Main Content -->
            <div class="flex-1 flex flex-col min-h-screen">
                <!-- Topbar -->
                <header class="h-16 bg-white shadow flex items-center px-8 justify-between">
                    <h1 class="text-xl font-bold text-gray-900">@yield('title', 'Dashboard')</h1>
                    <div>
                        <!-- Placeholder for notifications, profile, etc. -->
                    </div>
                </header>
                <main class="flex-1 p-8">
                    @yield('content')
                </main>
            </div>
        </div>
    </body>
</html>
