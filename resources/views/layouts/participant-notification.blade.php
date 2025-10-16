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

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
        <style>
            body { overflow-x: hidden; max-width: 100vw; }
        </style>
    </head>
    <body class="bg-gray-100 font-sans antialiased">
        <div class="min-h-screen flex flex-col">
            <!-- Minimal Topbar -->
            <header class="h-16 bg-white shadow flex items-center px-8 justify-between">
                <div class="flex items-center space-x-3">
                    <a href="{{ url()->previous() }}" class="p-2 rounded-lg hover:bg-gray-100" aria-label="Back">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                    <h1 class="text-xl font-bold text-gray-900">@yield('title', 'Notifications')</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <div class="font-semibold text-gray-800">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
                        <div class="text-xs text-gray-500">{{ auth()->user()->email }}</div>
                    </div>
                    <div class="w-10 h-10 bg-slate-800 rounded-full flex items-center justify-center text-white font-bold">
                        {{ strtoupper(substr(auth()->user()->first_name,0,1)) }}
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-8">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 py-6 px-8 text-center text-sm text-gray-500 mt-auto">
                <div class="flex flex-col md:flex-row items-center justify-between max-w-7xl mx-auto">
                    <div class="mb-2 md:mb-0">
                        &copy; {{ date('Y') }} <span class="font-semibold text-yellow-700">CGS Events</span>. All rights reserved.
                    </div>
                    <div>
                        <span class="text-xs text-gray-400">Powered by CGS Conference Suite</span>
                    </div>
                </div>
            </footer>
        </div>
        @stack('scripts')
    </body>
    </html>


