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

            .w-16 {
                width: 4rem;
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

            /* Additional styles for collapsible sidebar */
            .transition-all {
                transition-property: all;
                transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
                transition-duration: 300ms;
            }

            .transition-opacity {
                transition-property: opacity;
                transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
                transition-duration: 300ms;
            }

            .transition-transform {
                transition-property: transform;
                transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
                transition-duration: 300ms;
            }

            .transition-colors {
                transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
                transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
                transition-duration: 150ms;
            }

            .duration-300 {
                transition-duration: 300ms;
            }

            .duration-150 {
                transition-duration: 150ms;
            }

            .opacity-0 {
                opacity: 0;
            }

            .opacity-100 {
                opacity: 1;
            }

            .rotate-180 {
                transform: rotate(180deg);
            }

            .flex-shrink-0 {
                flex-shrink: 0;
            }

            .mr-3 {
                margin-right: 0.75rem;
            }

            .group {
                position: relative;
            }

            /* Tooltip styles for collapsed sidebar */
            .sidebar-collapsed [title]:hover::after {
                content: attr(title);
                position: absolute;
                left: 100%;
                top: 50%;
                transform: translateY(-50%);
                background: #1f2937;
                color: white;
                padding: 0.5rem 0.75rem;
                border-radius: 0.375rem;
                font-size: 0.875rem;
                white-space: nowrap;
                z-index: 50;
                margin-left: 0.5rem;
                opacity: 1;
                pointer-events: none;
            }
            
            .sidebar-collapsed [title]:hover::before {
                content: '';
                position: absolute;
                left: 100%;
                top: 50%;
                transform: translateY(-50%);
                border: 4px solid transparent;
                border-right-color: #1f2937;
                margin-left: -0.25rem;
                z-index: 50;
                opacity: 1;
                pointer-events: none;
            }
            
            /* Ensure toggle button is always visible */
            .sidebar-toggle-btn {
                position: relative;
                z-index: 10;
                min-width: 2rem;
                min-height: 2rem;
            }
            
            /* When collapsed, center the button */
            .sidebar-collapsed .sidebar-toggle-btn {
                position: absolute;
                right: 0.5rem;
                top: 50%;
                transform: translateY(-50%);
            }
        </style>
    </head>
    <body class="antialiased" x-data="{ sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true' || window.innerWidth < 768 }" x-init="$watch('sidebarCollapsed', value => localStorage.setItem('sidebarCollapsed', value))" :class="sidebarCollapsed ? 'sidebar-collapsed' : ''">
        <div class="min-h-screen flex">
            <!-- Sidebar -->
            <aside class="bg-white shadow-lg flex flex-col transition-all duration-300" :class="sidebarCollapsed ? 'w-16' : 'w-64'">
                <div class="h-16 flex items-center justify-between border-b px-4">
                    <span class="text-2xl font-bold text-yellow-700 transition-opacity duration-300" :class="sidebarCollapsed ? 'opacity-0' : 'opacity-100'">CGS Events</span>
                    <button @click="sidebarCollapsed = !sidebarCollapsed" class="p-2 rounded-lg hover:bg-yellow-50 transition-colors duration-150 sidebar-toggle-btn" :title="sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'">
                        <svg class="w-5 h-5 text-gray-600 transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                </div>
                <nav class="flex-1 px-4 py-6 space-y-2">
                    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800 group" :title="sidebarCollapsed ? 'Dashboard' : ''">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"/>
                        </svg>
                        <span class="transition-opacity duration-300" :class="sidebarCollapsed ? 'opacity-0' : 'opacity-100'">Dashboard</span>
                    </a>
                    <a href="{{ route('conferences.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800 group" :title="sidebarCollapsed ? 'Conferences' : ''">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="transition-opacity duration-300" :class="sidebarCollapsed ? 'opacity-0' : 'opacity-100'">Conferences</span>
                    </a>
                    <a href="{{ route('participants.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800 group" :title="sidebarCollapsed ? 'Participants' : ''">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="transition-opacity duration-300" :class="sidebarCollapsed ? 'opacity-0' : 'opacity-100'">Participants</span>
                    </a>
                    <a href="{{ route('sessions.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800 group" :title="sidebarCollapsed ? 'Sessions' : ''">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <span class="transition-opacity duration-300" :class="sidebarCollapsed ? 'opacity-0' : 'opacity-100'">Sessions</span>
                    </a>
                    <a href="{{ route('tasks.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800 group" :title="sidebarCollapsed ? 'Tasks' : ''">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        <span class="transition-opacity duration-300" :class="sidebarCollapsed ? 'opacity-0' : 'opacity-100'">Tasks</span>
                    </a>
                    <a href="{{ route('notifications.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800 group" :title="sidebarCollapsed ? 'Notifications' : ''">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="transition-opacity duration-300" :class="sidebarCollapsed ? 'opacity-0' : 'opacity-100'">Notifications</span>
                    </a>
                    <a href="{{ route('speaker.register') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800 group" :title="sidebarCollapsed ? 'Speaker Registration' : ''">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                        </svg>
                        <span class="transition-opacity duration-300" :class="sidebarCollapsed ? 'opacity-0' : 'opacity-100'">Speaker Registration</span>
                    </a>
                    <a href="{{ route('guide') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-yellow-50 font-medium text-gray-800 group" :title="sidebarCollapsed ? 'How to Use' : ''">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="transition-opacity duration-300" :class="sidebarCollapsed ? 'opacity-0' : 'opacity-100'">How to Use</span>
                    </a>
                </nav>
                <div class="border-t p-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center text-yellow-700 font-bold flex-shrink-0">
                            {{ strtoupper(substr(auth()->user()->first_name,0,1)) }}
                        </div>
                        <div class="transition-opacity duration-300" :class="sidebarCollapsed ? 'opacity-0' : 'opacity-100'">
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
        <!-- Alpine.js for sidebar functionality -->
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    </body>
</html> 