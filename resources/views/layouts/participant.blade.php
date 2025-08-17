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

        <!-- Static CSS for shared hosting -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        
        <!-- Alpine.js for interactive components -->
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
        
        <!-- Additional styles for collapsible sidebar -->
        <style>
            /* Tooltip styles for collapsed sidebar */
            [title]:hover::after {
                content: attr(title);
                position: absolute;
                left: 100%;
                top: 50%;
                transform: translateY(-50%);
                background: #0f172a;
                color: white;
                padding: 0.5rem 0.75rem;
                border-radius: 0.375rem;
                font-size: 0.875rem;
                white-space: nowrap;
                z-index: 50;
                margin-left: 0.5rem;
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.2s;
                border: 1px solid #1e293b;
            }
            
            [title]:hover::before {
                content: '';
                position: absolute;
                left: 100%;
                top: 50%;
                transform: translateY(-50%);
                border: 4px solid transparent;
                border-right-color: #0f172a;
                margin-left: -0.25rem;
                z-index: 50;
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.2s;
            }
            
            /* Show tooltip only when sidebar is collapsed */
            .sidebar-collapsed [title]:hover::after,
            .sidebar-collapsed [title]:hover::before {
                opacity: 1;
            }
            
            /* Ensure proper spacing for icons when collapsed */
            .sidebar-collapsed .flex-shrink-0 {
                margin-right: 0;
            }
            
            /* Smooth transitions for all elements */
            * {
                transition-property: width, opacity, transform;
                transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            /* Ensure toggle button is always visible */
            .sidebar-toggle-btn {
                position: relative;
                z-index: 10;
                min-width: 2rem;
                min-height: 2rem;
            }
            
            /* When collapsed, make button more prominent */
            .sidebar-collapsed .sidebar-toggle-btn {
                position: absolute;
                right: 0.25rem;
                top: 50%;
                transform: translateY(-50%);
                background: #6366f1 !important;
                border: 1px solid #4f46e5 !important;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
                z-index: 20;
            }
            
            /* Ensure the header doesn't hide the button */
            .sidebar-collapsed .h-20 {
                overflow: visible;
            }
            
            /* Thin scrollbar styling for sidebar navigation */
            .sidebar-nav::-webkit-scrollbar {
                width: 4px;
            }
            
            .sidebar-nav::-webkit-scrollbar-track {
                background: transparent;
            }
            
            .sidebar-nav::-webkit-scrollbar-thumb {
                background: #475569;
                border-radius: 2px;
            }
            
            .sidebar-nav::-webkit-scrollbar-thumb:hover {
                background: #64748b;
            }
            
            /* Firefox scrollbar styling */
            .sidebar-nav {
                scrollbar-width: thin;
                scrollbar-color: #475569 transparent;
            }
            
            /* Ensure nav section is scrollable */
            .sidebar-nav {
                overflow-y: auto;
                overflow-x: hidden;
            }
            
            /* Active menu item styling */
            .sidebar-nav a.active {
                background: #6366f1;
                color: white;
                box-shadow: 0 2px 4px rgba(99, 102, 241, 0.3);
            }
            
            .sidebar-nav a.active:hover {
                background: #5855eb;
            }
            
            /* Hover effects for menu items */
            .sidebar-nav a:hover:not(.active) {
                background: #1e293b;
                color: #f8fafc;
            }
            
            /* Section divider styling */
            .sidebar-divider {
                border-color: #334155;
            }
            
            /* Section header styling */
            .sidebar-section-header {
                color: #cbd5e1;
                font-weight: 600;
            }
        </style>
    </head>
    <body class="bg-gray-100 font-sans antialiased" x-data="{ sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true' || window.innerWidth < 768 }" x-init="$watch('sidebarCollapsed', value => localStorage.setItem('sidebarCollapsed', value))" :class="sidebarCollapsed ? 'sidebar-collapsed' : ''">
        <div class="min-h-screen flex">
            <!-- Sidebar -->
            <aside class="bg-slate-900 shadow-xl flex flex-col transition-all duration-300" :class="sidebarCollapsed ? 'w-16' : 'w-64'">
                <div class="h-20 flex items-center justify-between border-b border-slate-800 px-4 relative">
                    <span class="text-2xl font-bold text-indigo-200 transition-opacity duration-300" :class="sidebarCollapsed ? 'opacity-0' : 'opacity-100'">CGS Events</span>
                    <button @click="sidebarCollapsed = !sidebarCollapsed" class="p-3 rounded-lg hover:bg-slate-800 transition-colors duration-150 flex-shrink-0 sidebar-toggle-btn bg-slate-800 border border-slate-700 shadow-sm" :title="sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'">
                        <svg class="w-6 h-6 text-slate-300 transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                </div>
                <nav class="flex-1 px-4 py-6 space-y-3 sidebar-nav">
                    <!-- Participant Dashboard -->
                    <a href="{{ route('participant-dashboard') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-slate-800 font-medium text-slate-200 group transition-all duration-200 {{ request()->routeIs('participant-dashboard') ? 'active' : '' }}" :title="sidebarCollapsed ? 'Dashboard' : ''">
                        <svg class="w-5 h-5 mr-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                        <span class="transition-opacity duration-300" :class="sidebarCollapsed ? 'opacity-0' : 'opacity-100'">Dashboard</span>
                    </a>
                    
                    <!-- My Profile -->
                    <a href="{{ route('participants.profile') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-slate-800 font-medium text-slate-200 group transition-all duration-200 {{ request()->routeIs('participants.profile') ? 'active' : '' }}" :title="sidebarCollapsed ? 'My Profile' : ''">
                        <svg class="w-5 h-5 mr-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="transition-opacity duration-300" :class="sidebarCollapsed ? 'opacity-0' : 'opacity-100'">My Profile</span>
                    </a>
                    
                    <!-- Conference Kit -->
                    <a href="{{ route('conference-kit.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-slate-800 font-medium text-slate-200 group transition-all duration-200 {{ request()->routeIs('conference-kit.*') ? 'active' : '' }}" :title="sidebarCollapsed ? 'Conference Kit' : ''">
                        <svg class="w-5 h-5 mr-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="transition-opacity duration-300" :class="sidebarCollapsed ? 'opacity-0' : 'opacity-100'">Conference Kit</span>
                    </a>
                    
                    <!-- My Sessions -->
                    <a href="{{ route('participants.profile') }}#tab-sessions" class="flex items-center px-4 py-3 rounded-lg hover:bg-slate-800 font-medium text-slate-200 group transition-all duration-200" :title="sidebarCollapsed ? 'My Sessions' : ''">
                        <svg class="w-5 h-5 mr-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="transition-opacity duration-300" :class="sidebarCollapsed ? 'opacity-0' : 'opacity-100'">My Sessions</span>
                    </a>
                    
                                         <!-- Notifications -->
                     <a href="{{ route('participant.notifications.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-slate-800 font-medium text-slate-200 group transition-all duration-200 {{ request()->routeIs('participant.notifications.*') ? 'active' : '' }}" :title="sidebarCollapsed ? 'Notifications' : ''">
                        <svg class="w-5 h-5 mr-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span class="transition-opacity duration-300" :class="sidebarCollapsed ? 'opacity-0' : 'opacity-100'">Notifications</span>
                    </a>
                    
                    <!-- Help/Support -->
                    <a href="{{ route('guide') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-slate-800 font-medium text-slate-200 group transition-all duration-200 {{ request()->routeIs('guide') ? 'active' : '' }}" :title="sidebarCollapsed ? 'Help' : ''">
                        <svg class="w-5 h-5 mr-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="transition-opacity duration-300" :class="sidebarCollapsed ? 'opacity-0' : 'opacity-100'">Help & Support</span>
                    </a>
                </nav>
                <div class="border-t p-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-slate-800 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
                            {{ strtoupper(substr(auth()->user()->first_name,0,1)) }}
                        </div>
                        <div class="transition-opacity duration-300" :class="sidebarCollapsed ? 'opacity-0' : 'opacity-100'">
                            <div class="font-semibold text-slate-200">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
                            <div class="text-xs text-slate-400">{{ ucfirst(auth()->user()->roles->first()->name ?? 'Participant') }}</div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-xs text-slate-400 hover:text-slate-200">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </aside>
            <!-- Main Content -->
            <div class="flex-1 flex flex-col min-h-screen">
                <!-- Topbar -->
                <header class="h-16 bg-white shadow flex items-center px-8 justify-between">
                    <h1 class="text-xl font-bold text-gray-900">@yield('title', 'Participant Dashboard')</h1>
                    <div class="flex items-center space-x-6">
                        <!-- Notification Icon with Dropdown -->
                        <div class="relative group" x-data="{ open: false }" @keydown.escape.window="open = false">
                            @php
                                $unreadNotifications = \App\Models\Notification::where('user_id', auth()->id())->where('read_status', false)->count();
                                $recentNotifications = \App\Models\Notification::where('user_id', auth()->id())->latest()->take(5)->get();
                            @endphp
                            <button class="relative focus:outline-none group" aria-label="Notifications" @click="open = !open">
                                <svg class="w-7 h-7 text-gray-400 group-hover:text-yellow-600 transition-colors duration-150" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                @if($unreadNotifications > 0)
                                    <span class="absolute top-0 right-0 transform translate-x-1/2 -translate-y-1/2 inline-flex items-center justify-center w-4 h-4 text-[10px] font-bold text-white bg-red-500 rounded-full border border-white shadow-sm z-10 notification-badge">
                                        {{ $unreadNotifications > 9 ? '9+' : $unreadNotifications }}
                                    </span>
                                @endif
                            </button>
                            <!-- Dropdown -->
                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded-lg shadow-lg py-2 z-50 transition-all duration-150" style="display: none;" x-transition>
                                <div class="px-4 py-2 font-semibold text-gray-700 border-b">Notifications</div>
                                @if($recentNotifications->count() > 0)
                                    <ul class="max-h-72 overflow-y-auto divide-y divide-gray-100">
                                        @foreach($recentNotifications as $notification)
                                            <li class="px-4 py-3 hover:bg-yellow-50 cursor-pointer notification-item" 
                                                data-notification-id="{{ $notification->id }}"
                                                onclick="markNotificationAsRead({{ $notification->id }}, this)">
                                                <div class="flex items-center justify-between">
                                                    <span class="font-medium text-gray-800">{{ $notification->type }}</span>
                                                    <span class="text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                                                </div>
                                                <div class="text-sm text-gray-600">{{ $notification->message }}</div>
                                                @if(!$notification->read_status)
                                                    <div class="mt-1 unread-badge" id="unread-badge-{{ $notification->id }}">
                                                        <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">Unread</span>
                                                    </div>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                                                         <div class="px-4 py-2 text-center">
                                         <a href="{{ route('participant.notifications.index') }}" class="text-xs text-blue-600 hover:text-blue-800">View all notifications</a>
                                     </div>
                                @else
                                    <div class="px-4 py-2 text-center text-xs text-gray-400 border-t">No notifications</div>
                                @endif
                            </div>
                        </div>
                        <!-- User Profile Dropdown -->
                        <div class="relative group">
                            <button class="flex items-center space-x-3 focus:outline-none">
                                <span class="w-10 h-10 bg-slate-800 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                    {{ strtoupper(substr(auth()->user()->first_name,0,1)) }}
                                </span>
                                <span class="hidden md:flex flex-col items-start">
                                    <span class="font-semibold text-slate-200">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</span>
                                    <span class="text-xs text-slate-400">{{ auth()->user()->email }}</span>
                                </span>
                                <svg class="w-4 h-4 text-gray-400 ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg py-2 z-50 opacity-0 group-hover:opacity-100 group-focus-within:opacity-100 pointer-events-none group-hover:pointer-events-auto group-focus-within:pointer-events-auto transition-opacity duration-150">
                                <a href="{{ route('participants.profile') }}" class="block px-4 py-2 text-gray-700 hover:bg-slate-800">My Profile</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-slate-800">Logout</button>
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
        <!-- Add jQuery and DataTables JS (only load when needed) -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        @stack('datatables')
        
        <!-- Notification click functionality -->
        <script>
            function markNotificationAsRead(notificationId, element) {
                console.log('markNotificationAsRead called with ID:', notificationId);
                
                // Show loading state
                element.style.opacity = '0.6';
                element.style.pointerEvents = 'none';
                
                fetch(`/notifications/${notificationId}/mark-read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Mark read response:', data);
                    if (data.success) {
                        // Remove the unread badge
                        const unreadBadge = document.getElementById(`unread-badge-${notificationId}`);
                        if (unreadBadge) {
                            unreadBadge.remove();
                        }
                        
                        // Update the notification count in the bell icon
                        const notificationBadge = document.querySelector('.notification-badge');
                        if (notificationBadge) {
                            if (data.unread_count > 0) {
                                notificationBadge.textContent = data.unread_count > 9 ? '9+' : data.unread_count;
                                notificationBadge.style.display = 'inline-flex';
                            } else {
                                notificationBadge.style.display = 'none';
                            }
                        }
                        
                        // Add visual feedback
                        element.style.backgroundColor = '#fef3c7'; // Light yellow background
                        setTimeout(() => {
                            element.style.backgroundColor = '';
                            element.style.opacity = '1';
                            element.style.pointerEvents = 'auto';
                        }, 1000);
                        
                        // Get notification data to find related content
                        console.log('Fetching notification data from:', `/notifications/${notificationId}/data`);
                        fetch(`/notifications/${notificationId}/data`)
                            .then(response => {
                                console.log('Response status:', response.status);
                                if (!response.ok) {
                                    throw new Error(`HTTP error! status: ${response.status}`);
                                }
                                return response.json();
                            })
                            .then(notification => {
                                console.log('Notification data received:', notification);
                                
                                // Navigate based on notification data
                                if (notification.related_model === 'Task' && notification.related_id) {
                                    // Navigate to task details
                                    const taskUrl = `/tasks/${notification.related_id}`;
                                    console.log('About to navigate to task URL:', taskUrl);
                                    window.location.href = taskUrl;
                                } else if (notification.related_model === 'Participant' && notification.related_id) {
                                    // Navigate to participant details
                                    const participantUrl = `/participants/${notification.related_id}`;
                                    console.log('About to navigate to participant URL:', participantUrl);
                                    window.location.href = participantUrl;
                                } else if (notification.related_model === 'Session' && notification.related_id) {
                                    // Navigate to session details
                                    const sessionUrl = `/sessions/${notification.related_id}`;
                                    console.log('About to navigate to session URL:', sessionUrl);
                                    window.location.href = sessionUrl;
                                } else if (notification.type === 'TaskUpdate') {
                                    // Fallback for task notifications without related_id
                                    console.log('TaskUpdate notification clicked - no related_id found, redirecting to tasks index');
                                    window.location.href = '/tasks';
                                } else if (notification.type === 'TravelUpdate') {
                                    // Fallback for travel notifications without related_id
                                    console.log('TravelUpdate notification clicked - no related_id found, redirecting to participants');
                                    window.location.href = '/participants';
                                } else if (notification.type === 'SessionUpdate') {
                                    // Fallback for session notifications without related_id
                                    console.log('SessionUpdate notification clicked - no related_id found, redirecting to sessions');
                                    window.location.href = '/sessions';
                                } else if (notification.type === 'General') {
                                    // Handle General notifications - redirect to dashboard
                                    console.log('General notification clicked - redirecting to dashboard');
                                    window.location.href = '/participant-dashboard';
                                } else {
                                    console.log('No navigation logic for this notification type:', notification.type);
                                    // Default fallback to participant dashboard
                                    window.location.href = '/participant-dashboard';
                                }
                            })
                            .catch(error => {
                                console.error('Error fetching notification data:', error);
                            });
                    } else {
                        console.log('Mark read was not successful:', data);
                    }
                })
                .catch(error => {
                    console.error('Error marking notification as read:', error);
                    element.style.opacity = '1';
                    element.style.pointerEvents = 'auto';
                });
            }
        </script>
    </body>
</html>
