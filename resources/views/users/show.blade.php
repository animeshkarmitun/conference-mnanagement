@extends('layouts.app')

@section('title', 'User Details')

@push('styles')
<style>
    .user-profile-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .info-card {
        transition: all 0.3s ease;
        border: 1px solid #e2e8f0;
    }
    
    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    
    .role-badge {
        transition: all 0.2s ease;
    }
    
    .role-badge:hover {
        transform: scale(1.05);
    }
    
    .action-btn {
        transition: all 0.2s ease;
    }
    
    .action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .status-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 8px;
    }
    
    .status-active {
        background-color: #10b981;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
    }
    
    .status-inactive {
        background-color: #6b7280;
        box-shadow: 0 0 0 2px rgba(107, 114, 128, 0.2);
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                {{ session('error') }}
            </div>
        </div>
    @endif
    <!-- User Profile Header -->
    <div class="user-profile-card rounded-2xl shadow-xl p-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-6">
                @if($user->profile_picture)
                    <img class="w-24 h-24 rounded-full border-4 border-white shadow-lg" 
                         src="{{ asset('storage/' . $user->profile_picture) }}" 
                         alt="{{ $user->first_name }}">
                @else
                    <div class="w-24 h-24 rounded-full border-4 border-white shadow-lg bg-white flex items-center justify-center">
                        <span class="text-3xl font-bold text-gray-600">
                            {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                        </span>
                    </div>
                @endif
                
                <div>
                    <h1 class="text-3xl font-bold">{{ $user->first_name }} {{ $user->last_name }}</h1>
                    <p class="text-xl opacity-90 mt-1">{{ $user->email }}</p>
                    <div class="flex items-center mt-3">
                        <span class="status-indicator {{ $user->email_verified_at ? 'status-active' : 'status-inactive' }}"></span>
                        <span class="text-lg font-medium">
                            {{ $user->email_verified_at ? 'Active User' : 'Inactive User' }}
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="flex space-x-3">
                @if(!$user->email_verified_at)
                    <form action="{{ route('users.activate', $user) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                class="action-btn bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-200 flex items-center"
                                onclick="return confirm('Are you sure you want to activate this user?')">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Activate User
                        </button>
                    </form>
                @else
                    <form action="{{ route('users.deactivate', $user) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                class="action-btn bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-200 flex items-center"
                                onclick="return confirm('Are you sure you want to deactivate this user?')">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            Deactivate User
                        </button>
                    </form>
                @endif
                
                <a href="{{ route('users.edit', $user) }}" 
                   class="action-btn bg-white bg-opacity-90 hover:bg-opacity-100 text-gray-800 px-6 py-3 rounded-lg font-semibold transition-all duration-200 flex items-center shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit User
                </a>
                
                <a href="{{ route('users.index') }}" 
                   class="action-btn bg-white bg-opacity-90 hover:bg-opacity-100 text-gray-800 px-6 py-3 rounded-lg font-semibold transition-all duration-200 flex items-center shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Users
                </a>
            </div>
        </div>
    </div>

    <!-- User Information Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Basic Information -->
        <div class="info-card bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center mb-4">
                <svg class="w-6 h-6 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-800">Basic Information</h3>
            </div>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600 font-medium">First Name:</span>
                    <span class="text-gray-900 font-semibold">{{ $user->first_name }}</span>
                </div>
                
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600 font-medium">Last Name:</span>
                    <span class="text-gray-900 font-semibold">{{ $user->last_name }}</span>
                </div>
                
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600 font-medium">Email:</span>
                    <span class="text-gray-900 font-semibold">{{ $user->email }}</span>
                </div>
                
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600 font-medium">Email Verified:</span>
                    <span class="font-semibold {{ $user->email_verified_at ? 'text-green-600' : 'text-red-600' }}">
                        {{ $user->email_verified_at ? 'Yes' : 'No' }}
                    </span>
                </div>
                
                @if($user->email_verified_at)
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600 font-medium">Verified At:</span>
                    <span class="text-gray-900 font-semibold">{{ $user->email_verified_at->format('M d, Y H:i') }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Account Information -->
        <div class="info-card bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center mb-4">
                <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-800">Account Information</h3>
            </div>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600 font-medium">User ID:</span>
                    <span class="text-gray-900 font-semibold">#{{ $user->id }}</span>
                </div>
                
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600 font-medium">Member Since:</span>
                    <span class="text-gray-900 font-semibold">{{ $user->created_at->format('M d, Y') }}</span>
                </div>
                
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600 font-medium">Last Updated:</span>
                    <span class="text-gray-900 font-semibold">{{ $user->updated_at->format('M d, Y H:i') }}</span>
                </div>
                
                @if($user->last_login_at)
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600 font-medium">Last Login:</span>
                    <span class="text-gray-900 font-semibold">{{ \Carbon\Carbon::parse($user->last_login_at)->format('M d, Y H:i') }}</span>
                </div>
                @else
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600 font-medium">Last Login:</span>
                    <span class="text-gray-500 font-semibold">Never</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Roles and Permissions -->
    <div class="info-card bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-800">Roles & Permissions</h3>
            </div>
            
            <a href="{{ route('users.edit', $user) }}#roles" 
               class="action-btn bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Manage Roles
            </a>
        </div>
        
        @if($user->roles->count() > 0)
            @php
                // Get the primary role for the main icon
                $primaryRole = $user->roles->first();
                $roleName = strtolower($primaryRole->name);
                
                // Define role-specific icons for badges
                $roleIcons = [
                    'superadmin' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                    'admin' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                    'event coordinator' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                    'tasker' => 'M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
                    'attendee' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                    'speaker' => 'M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 0h10m-10 0a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V6a2 2 0 00-2-2M9 4a2 2 0 012-2h2a2 2 0 012 2m-3 7h2m-2 4h2m-6-4h.01M9 16h.01',
                    'user' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'
                ];
            @endphp
            
            <!-- Primary Role Display with Large Icon -->
            <div class="text-center mb-6 p-6 bg-gradient-to-r from-purple-50 to-indigo-50 rounded-xl border border-purple-100">
                <svg class="w-12 h-12 text-purple-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $roleIcons[$roleName] ?? $roleIcons['user'] }}"></path>
                </svg>
                <h4 class="text-lg font-semibold text-gray-800 mb-1">Primary Role</h4>
                <p class="text-purple-600 font-medium">{{ $primaryRole->name }}</p>
            </div>
            
            <!-- All Roles Display -->
            <div class="flex flex-wrap gap-3">
                @foreach($user->roles as $role)
                    @php
                        $roleColors = [
                            'superadmin' => 'bg-red-100 text-red-800 border-red-200',
                            'admin' => 'bg-violet-100 text-violet-800 border-violet-200',
                            'event coordinator' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                            'tasker' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                            'attendee' => 'bg-blue-100 text-blue-800 border-blue-200',
                            'speaker' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                            'user' => 'bg-gray-100 text-gray-800 border-gray-200'
                        ];
                        $roleColor = $roleColors[strtolower($role->name)] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                        $currentRoleIcon = $roleIcons[strtolower($role->name)] ?? $roleIcons['user'];
                    @endphp
                    <span class="role-badge inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold shadow-sm border {{ $roleColor }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentRoleIcon }}"></path>
                        </svg>
                        {{ $role->name }}
                    </span>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                @php
                    // Get the primary role for icon selection
                    $primaryRole = $user->roles->first();
                    $roleName = $primaryRole ? strtolower($primaryRole->name) : 'no-role';
                    
                    // Define role-specific icons
                    $roleIcons = [
                        'superadmin' => [
                            'path' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                            'color' => 'text-red-500',
                            'title' => 'Super Admin'
                        ],
                        'admin' => [
                            'path' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                            'color' => 'text-violet-500',
                            'title' => 'Administrator'
                        ],
                        'event coordinator' => [
                            'path' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                            'color' => 'text-indigo-500',
                            'title' => 'Event Coordinator'
                        ],
                        'tasker' => [
                            'path' => 'M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
                            'color' => 'text-emerald-500',
                            'title' => 'Task Manager'
                        ],
                        'attendee' => [
                            'path' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                            'color' => 'text-blue-500',
                            'title' => 'Attendee'
                        ],
                        'speaker' => [
                            'path' => 'M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 0h10m-10 0a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V6a2 2 0 00-2-2M9 4a2 2 0 012-2h2a2 2 0 012 2m-3 7h2m-2 4h2m-6-4h.01M9 16h.01',
                            'color' => 'text-yellow-500',
                            'title' => 'Speaker'
                        ],
                        'user' => [
                            'path' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                            'color' => 'text-gray-500',
                            'title' => 'User'
                        ],
                        'no-role' => [
                            'path' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
                            'color' => 'text-gray-400',
                            'title' => 'No Role Assigned'
                        ]
                    ];
                    
                    $iconConfig = $roleIcons[$roleName] ?? $roleIcons['no-role'];
                @endphp
                
                <svg class="w-16 h-16 {{ $iconConfig['color'] }} mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" title="{{ $iconConfig['title'] }}">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconConfig['path'] }}"></path>
                </svg>
                <p class="text-gray-500 text-lg mb-4">{{ $primaryRole ? 'Role: ' . $primaryRole->name : 'No roles assigned' }}</p>
                <a href="{{ route('users.edit', $user) }}#roles" 
                   class="action-btn bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    {{ $primaryRole ? 'Manage Roles' : 'Assign First Role' }}
                </a>
            </div>
        @endif
    </div>

    <!-- Activity Summary (if needed) -->
    <div class="info-card bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center mb-4">
            <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <h3 class="text-xl font-semibold text-gray-800">Account Summary</h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-2xl font-bold text-gray-800">{{ $user->roles->count() }}</div>
                <div class="text-sm text-gray-600">Assigned Roles</div>
            </div>
            
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-2xl font-bold text-gray-800">{{ $user->email_verified_at ? '1' : '0' }}</div>
                <div class="text-sm text-gray-600">Verified Accounts</div>
            </div>
            
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-2xl font-bold text-gray-800">{{ $user->created_at->diffInDays(now()) }}</div>
                <div class="text-sm text-gray-600">Days Active</div>
            </div>
        </div>
    </div>
</div>


@endsection
