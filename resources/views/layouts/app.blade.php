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
                    @if(auth()->user()->hasRole('superadmin'))
                        <a href="{{ route('dashboard') }}" class="block px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800">Dashboard</a>
                        <a href="{{ route('users.index') }}" class="block px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800">Users</a>
                        <a href="{{ route('roles.index') }}" class="block px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800">Roles</a>
                        <a href="{{ route('conferences.index') }}" class="block px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800">Conferences</a>
                        <a href="{{ route('participants.index') }}" class="block px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800">Participants</a>
                        <a href="{{ route('sessions.index') }}" class="block px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800">Sessions</a>
                        <a href="{{ route('tasks.index') }}" class="block px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800">Tasks</a>
                        <a href="{{ route('notifications.index') }}" class="block px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800">Notifications</a>
                        <a href="{{ route('bulk.email') }}" class="block px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800">Bulk Email</a>
                        <a href="{{ route('gmail.index') }}" class="block px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800">Gmail Conversations</a>
                        <a href="{{ route('speaker.register') }}" class="block px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800">Speaker Registration</a>
                        <a href="{{ route('guide') }}" class="block px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800">How to Use</a>
                    @elseif(auth()->user()->hasRole('tasker'))
                        <a href="{{ route('dashboard.tasker') }}" class="block px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800">Tasker Dashboard</a>
                        <a href="{{ route('tasks.index') }}" class="block px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800">Tasks</a>
                        <a href="{{ route('notifications.index') }}" class="block px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800">Notifications</a>
                        <a href="{{ route('gmail.index') }}" class="block px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800">Gmail Conversations</a>
                    @else
                        <a href="{{ route('dashboard') }}" class="block px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800">Dashboard</a>
                        <a href="{{ route('conferences.index') }}" class="block px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800">Conferences</a>
                        <a href="{{ route('participants.index') }}" class="block px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800">Participants</a>
                        <a href="{{ route('sessions.index') }}" class="block px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800">Sessions</a>
                        <a href="{{ route('tasks.index') }}" class="block px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800">Tasks</a>
                        <a href="{{ route('notifications.index') }}" class="block px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800">Notifications</a>
                        <a href="{{ route('gmail.index') }}" class="block px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800">Gmail Conversations</a>
                        <a href="{{ route('speaker.register') }}" class="block px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800">Speaker Registration</a>
                        <a href="{{ route('guide') }}" class="block px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800">How to Use</a>
                    @endif
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
                    <div class="flex items-center space-x-6">
                        <!-- Notification Icon with Dropdown -->
                        <div class="relative group" x-data="{ open: false }" @keydown.escape.window="open = false">
                            <button class="relative focus:outline-none group" aria-label="Notifications" @click="open = !open">
                                <svg class="w-7 h-7 text-gray-400 group-hover:text-yellow-600 transition-colors duration-150" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <!-- Future notification badge -->
                                <!-- <span class="absolute top-0 right-0 block w-2.5 h-2.5 bg-red-500 rounded-full ring-2 ring-white"></span> -->
                            </button>
                            <!-- Dropdown -->
                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded-lg shadow-lg py-2 z-50 transition-all duration-150" style="display: none;" x-transition>
                                <div class="px-4 py-2 font-semibold text-gray-700 border-b">Notifications</div>
                                <ul class="max-h-72 overflow-y-auto divide-y divide-gray-100">
                                    <li class="px-4 py-3 hover:bg-yellow-50 cursor-pointer">
                                        <div class="flex items-center justify-between">
                                            <span class="font-medium text-gray-800">System Update</span>
                                            <span class="text-xs text-gray-400">2m ago</span>
                                        </div>
                                        <div class="text-sm text-gray-600">The system will be updated tonight at 11:00 PM.</div>
                                    </li>
                                    <li class="px-4 py-3 hover:bg-yellow-50 cursor-pointer">
                                        <div class="flex items-center justify-between">
                                            <span class="font-medium text-gray-800">New Registration</span>
                                            <span class="text-xs text-gray-400">10m ago</span>
                                        </div>
                                        <div class="text-sm text-gray-600">Jane Smith has registered for Annual Tech Summit.</div>
                                    </li>
                                    <!-- Add more dummy notifications here -->
                                </ul>
                                <div class="px-4 py-2 text-center text-xs text-gray-400 border-t">No new notifications</div>
                            </div>
                        </div>
                        <!-- User Profile Dropdown -->
                        <div class="relative group">
                            <button class="flex items-center space-x-3 focus:outline-none">
                                <span class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center text-yellow-700 font-bold text-lg">
                                    {{ strtoupper(substr(auth()->user()->first_name,0,1)) }}
                                </span>
                                <span class="hidden md:flex flex-col items-start">
                                    <span class="font-semibold text-gray-800">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</span>
                                    <span class="text-xs text-gray-500">{{ auth()->user()->email }}</span>
                                </span>
                                <svg class="w-4 h-4 text-gray-400 ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg py-2 z-50 opacity-0 group-hover:opacity-100 group-focus-within:opacity-100 pointer-events-none group-hover:pointer-events-auto group-focus-within:pointer-events-auto transition-opacity duration-150">
                                <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-yellow-50">Profile</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-yellow-50">Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>
                <main class="flex-1 p-8">
                    @yield('content')
                </main>
                <!-- Sitewide Footer -->
                <footer class="bg-white border-t border-gray-200 py-6 px-8 text-center text-sm text-gray-500 mt-auto">
                    <div class="flex flex-col md:flex-row items-center justify-between max-w-7xl mx-auto">
                        <div class="mb-2 md:mb-0">
                            &copy; {{ date('Y') }} <span class="font-semibold text-yellow-700">CGS Events</span>. All rights reserved.
                        </div>
                        <div>
                            <!-- Future footer links or content can go here -->
                            <span class="text-xs text-gray-400">Powered by CGS Conference Suite</span>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        @stack('scripts')
        <!-- Scroll to Top Button -->
        <button id="scrollToTopBtn" class="fixed bottom-6 right-6 z-50 w-12 h-12 bg-yellow-500 text-white rounded-full shadow-lg flex items-center justify-center transition-opacity duration-300 opacity-0 hover:bg-yellow-600 focus:outline-none" aria-label="Scroll to top">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
        </button>
        <script>
            // Show/hide scroll-to-top button
            const scrollBtn = document.getElementById('scrollToTopBtn');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 200) {
                    scrollBtn.classList.add('opacity-100');
                    scrollBtn.classList.remove('opacity-0');
                } else {
                    scrollBtn.classList.add('opacity-0');
                    scrollBtn.classList.remove('opacity-100');
                }
            });
            scrollBtn.addEventListener('click', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        </script>
        <!-- Alpine.js for dropdowns -->
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    </body>
</html>
