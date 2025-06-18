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

        <!-- Hardcoded CSS -->
        <style>
            /* Base styles */
            *, ::before, ::after {
                box-sizing: border-box;
                border-width: 0;
                border-style: solid;
                border-color: #e5e7eb;
            }

            body {
                margin: 0;
                font-family: 'Figtree', sans-serif;
                background-color: #f3f4f6;
                color: #1f2937;
            }

            /* Layout */
            .min-h-screen {
                min-height: 100vh;
            }

            .flex {
                display: flex;
            }

            .flex-col {
                flex-direction: column;
            }

            .flex-1 {
                flex: 1 1 0%;
            }

            /* Sidebar */
            .w-64 {
                width: 16rem;
            }

            .bg-white {
                background-color: #ffffff;
            }

            .shadow-lg {
                box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            }

            /* Navigation */
            .px-4 {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .py-6 {
                padding-top: 1.5rem;
                padding-bottom: 1.5rem;
            }

            .space-y-2 > * + * {
                margin-top: 0.5rem;
            }

            /* Links */
            a {
                color: inherit;
                text-decoration: none;
            }

            .block {
                display: block;
            }

            .rounded-lg {
                border-radius: 0.5rem;
            }

            .hover\:bg-yellow-50:hover {
                background-color: #fefce8;
            }

            .font-medium {
                font-weight: 500;
            }

            .text-gray-800 {
                color: #1f2937;
            }

            /* Header */
            .h-16 {
                height: 4rem;
            }

            .px-8 {
                padding-left: 2rem;
                padding-right: 2rem;
            }

            .justify-between {
                justify-content: space-between;
            }

            .items-center {
                align-items: center;
            }

            .text-xl {
                font-size: 1.25rem;
                line-height: 1.75rem;
            }

            .font-bold {
                font-weight: 700;
            }

            .text-gray-900 {
                color: #111827;
            }

            /* Main content */
            .p-8 {
                padding: 2rem;
            }

            /* User profile section */
            .border-t {
                border-top-width: 1px;
            }

            .p-4 {
                padding: 1rem;
            }

            .space-x-3 > * + * {
                margin-left: 0.75rem;
            }

            .w-10 {
                width: 2.5rem;
            }

            .h-10 {
                height: 2.5rem;
            }

            .bg-yellow-100 {
                background-color: #fef9c3;
            }

            .rounded-full {
                border-radius: 9999px;
            }

            .text-yellow-700 {
                color: #a16207;
            }

            .text-xs {
                font-size: 0.75rem;
                line-height: 1rem;
            }

            .text-gray-500 {
                color: #6b7280;
            }

            .hover\:text-yellow-700:hover {
                color: #a16207;
            }

            /* Utility classes */
            .antialiased {
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
            }
        </style>
    </head>
    <body class="antialiased">
        <div class="min-h-screen flex">
            <!-- Sidebar -->
            <aside class="w-64 bg-white shadow-lg flex flex-col">
                <div class="h-16 flex items-center justify-center border-b">
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