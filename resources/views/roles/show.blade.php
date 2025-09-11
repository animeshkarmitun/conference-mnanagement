@extends('layouts.app')

@section('title', 'Role Details')

@push('styles')
<style>
    .role-header-card {
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
    
    .user-badge {
        transition: all 0.2s ease;
    }
    
    .user-badge:hover {
        transform: scale(1.05);
    }
    
    .action-btn {
        transition: all 0.2s ease;
    }
    
    .action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .role-icon {
        background: linear-gradient(135deg, #f59e0b, #fbbf24);
        color: white;
        font-weight: bold;
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

    <!-- Role Header -->
    <div class="role-header-card rounded-2xl shadow-xl p-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-6">
                <div class="w-24 h-24 rounded-full border-4 border-white shadow-lg role-icon flex items-center justify-center">
                    <span class="text-3xl font-bold">
                        {{ strtoupper(substr($role->name, 0, 2)) }}
                    </span>
                </div>
                
                <div>
                    <h1 class="text-3xl font-bold">{{ $role->name }}</h1>
                    @if($role->description)
                        <p class="text-xl opacity-90 mt-1">{{ $role->description }}</p>
                    @endif
                    <div class="flex items-center mt-3">
                        <span class="text-lg font-medium">
                            {{ $role->users->count() }} {{ Str::plural('User', $role->users->count()) }} Assigned
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="flex space-x-3">
                <a href="{{ route('roles.edit', $role) }}" 
                   class="action-btn bg-white bg-opacity-90 hover:bg-opacity-100 text-gray-800 px-6 py-3 rounded-lg font-semibold transition-all duration-200 flex items-center shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Role
                </a>
                
                <a href="{{ route('roles.assign-users', $role) }}" 
                   class="action-btn bg-white bg-opacity-90 hover:bg-opacity-100 text-gray-800 px-6 py-3 rounded-lg font-semibold transition-all duration-200 flex items-center shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    Manage Users
                </a>
                
                <a href="{{ route('roles.index') }}" 
                   class="action-btn bg-white bg-opacity-90 hover:bg-opacity-100 text-gray-800 px-6 py-3 rounded-lg font-semibold transition-all duration-200 flex items-center shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Roles
                </a>
            </div>
        </div>
    </div>

    <!-- Role Information Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Basic Information -->
        <div class="info-card bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center mb-4">
                <svg class="w-6 h-6 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-800">Role Information</h3>
            </div>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600 font-medium">Role Name:</span>
                    <span class="text-gray-900 font-semibold">{{ $role->name }}</span>
                </div>
                
                @if($role->description)
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600 font-medium">Description:</span>
                    <span class="text-gray-900 font-semibold">{{ $role->description }}</span>
                </div>
                @endif
                
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600 font-medium">Role ID:</span>
                    <span class="text-gray-900 font-semibold">#{{ $role->id }}</span>
                </div>
                
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600 font-medium">Created:</span>
                    <span class="text-gray-900 font-semibold">{{ $role->created_at->format('M d, Y') }}</span>
                </div>
                
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600 font-medium">Last Updated:</span>
                    <span class="text-gray-900 font-semibold">{{ $role->updated_at->format('M d, Y H:i') }}</span>
                </div>
            </div>
        </div>

        <!-- Role Statistics -->
        <div class="info-card bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center mb-4">
                <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-800">Role Statistics</h3>
            </div>
            
            <div class="space-y-4">
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <div class="text-3xl font-bold text-gray-800">{{ $role->users->count() }}</div>
                    <div class="text-sm text-gray-600">Total Users</div>
                </div>
                
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <div class="text-3xl font-bold text-gray-800">{{ $role->users->where('email_verified_at', '!=', null)->count() }}</div>
                    <div class="text-sm text-gray-600">Active Users</div>
                </div>
                
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <div class="text-3xl font-bold text-gray-800">{{ $role->users->where('email_verified_at', null)->count() }}</div>
                    <div class="text-sm text-gray-600">Inactive Users</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assigned Users -->
    <div class="info-card bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-800">Assigned Users</h3>
            </div>
            
            <a href="{{ route('roles.assign-users', $role) }}" 
               class="action-btn bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Manage Users
            </a>
        </div>
        
        @if($role->users->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($role->users as $user)
                    <div class="user-badge bg-gray-50 border border-gray-200 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                        <div class="flex items-center space-x-3">
                            @if($user->profile_picture)
                                <img class="w-10 h-10 rounded-full" src="{{ asset('storage/' . $user->profile_picture) }}" alt="{{ $user->first_name }}">
                            @else
                                <div class="w-10 h-10 bg-indigo-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-semibold text-sm">
                                        {{ strtoupper(substr($user->first_name, 0, 1)) }}
                                    </span>
                                </div>
                            @endif
                            
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ $user->first_name }} {{ $user->last_name }}
                                </p>
                                <p class="text-sm text-gray-500 truncate">
                                    {{ $user->email }}
                                </p>
                                <div class="flex items-center mt-1">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $user->email_verified_at ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $user->email_verified_at ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
                <p class="text-gray-500 text-lg mb-4">No users assigned to this role</p>
                <a href="{{ route('roles.assign-users', $role) }}" 
                   class="action-btn bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Assign First User
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
