@extends('layouts.app')

@section('title', 'Users')

@push('styles')
<style>
    .user-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .user-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    .status-badge {
        transition: all 0.2s ease;
    }
    
    .status-badge:hover {
        transform: scale(1.05);
    }
    
    .quick-action-btn {
        transition: all 0.2s ease;
    }
    
    .quick-action-btn:hover {
        transform: scale(1.05);
    }
    
    .tab-link {
        transition: all 0.2s ease-in-out;
    }
    
    .tab-link:hover {
        transform: translateY(-1px);
    }
    
    .table-row-hover {
        transition: all 0.2s ease;
    }
    
    .table-row-hover:hover {
        background-color: #fefce8;
        transform: scale(1.01);
    }
    
    .user-icon {
        background: linear-gradient(135deg, #f59e0b, #fbbf24);
        color: white;
        font-weight: bold;
    }
    
    .role-badge {
        transition: all 0.2s ease;
    }
    
    .role-badge:hover {
        transform: scale(1.05);
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fade-in-up {
        animation: fadeInUp 0.6s ease-out;
    }
    
    .animate-delay-1 { animation-delay: 0.1s; }
    .animate-delay-2 { animation-delay: 0.2s; }
    .animate-delay-3 { animation-delay: 0.3s; }
    .animate-delay-4 { animation-delay: 0.4s; }
</style>
@endpush

@section('content')
<!-- Enhanced Header with Quick Actions -->
<div class="bg-white rounded-2xl shadow-lg p-6 mb-6 border border-gray-100 animate-fade-in-up">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">Users</h2>
            <p class="text-gray-600 mt-1">Manage all users and their roles</p>
        </div>
        <div class="flex items-center space-x-4">
            <!-- Quick Actions -->
            <div class="flex space-x-3">
                <button class="quick-action-btn bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-full shadow-lg transition-all duration-200" title="Import Users">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                    </svg>
                </button>
                <button class="quick-action-btn bg-green-600 hover:bg-green-700 text-white p-3 rounded-full shadow-lg transition-all duration-200" title="Generate Reports">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 018 0v2m-4-4V7a4 4 0 10-8 0v6m0 4h8"></path>
                    </svg>
                </button>
                <button class="quick-action-btn bg-purple-600 hover:bg-purple-700 text-white p-3 rounded-full shadow-lg transition-all duration-200" title="Export Data">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </button>
                <button class="quick-action-btn bg-orange-600 hover:bg-orange-700 text-white p-3 rounded-full shadow-lg transition-all duration-200" title="Bulk Role Assignment">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </button>
            </div>
            <a href="{{ route('users.create') }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-xl flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add User
            </a>
        </div>
    </div>
</div>

<!-- Enhanced User Status Tabs -->
<div class="bg-white rounded-2xl shadow-lg mb-6 border border-gray-100 animate-fade-in-up animate-delay-1">
    <div class="border-b border-gray-200">
        <nav class="flex space-x-8 px-6" aria-label="Tabs">
            <a href="{{ route('users.index', ['status' => 'active']) }}" 
               class="tab-link py-4 px-3 border-b-2 font-medium text-sm rounded-t-lg transition-all duration-200 {{ $status === 'active' ? 'border-yellow-500 text-yellow-600 bg-yellow-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Active Users
                    <span class="ml-2 bg-yellow-100 text-yellow-800 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $userCounts['active'] }}</span>
                </div>
            </a>
            
            <a href="{{ route('users.index', ['status' => 'inactive']) }}" 
               class="tab-link py-4 px-3 border-b-2 font-medium text-sm rounded-t-lg transition-all duration-200 {{ $status === 'inactive' ? 'border-yellow-500 text-yellow-600 bg-yellow-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    Inactive Users
                    <span class="ml-2 bg-yellow-100 text-yellow-800 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $userCounts['inactive'] }}</span>
                </div>
            </a>
            
            <a href="{{ route('users.index', ['status' => 'all']) }}" 
               class="tab-link py-4 px-3 border-b-2 font-medium text-sm rounded-t-lg transition-all duration-200 {{ $status === 'all' ? 'border-yellow-500 text-yellow-600 bg-yellow-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }}">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    All Users
                    <span class="ml-2 bg-yellow-100 text-yellow-800 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $userCounts['all'] }}</span>
                </div>
            </a>
        </nav>
    </div>
</div>

<!-- Enhanced User Table -->
<div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 animate-fade-in-up animate-delay-2">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roles</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                    @php
                        $isActive = $user->email_verified_at;
                        $statusClass = $isActive ? 'bg-green-100 text-green-800 border-green-200' : 'bg-gray-100 text-gray-600 border-gray-200';
                        $statusText = $isActive ? 'Active' : 'Inactive';
                        $lastLogin = $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() : 'Never';
                    @endphp
                    
                    <tr class="table-row-hover hover:bg-yellow-50 transition-all duration-200 border-b border-gray-100">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($user->profile_picture)
                                    <img class="w-10 h-10 rounded-full shadow-lg mr-3" src="{{ asset('storage/' . $user->profile_picture) }}" alt="{{ $user->first_name }}">
                                @else
                                    <div class="w-10 h-10 user-icon rounded-full flex items-center justify-center mr-3 shadow-lg">
                                        <span class="text-sm font-bold">
                                            {{ strtoupper(substr($user->first_name, 0, 1)) }}
                                        </span>
                                    </div>
                                @endif
                                <div>
                                    <div class="font-medium text-gray-900">{{ $user->first_name }} {{ $user->last_name }}</div>
                                    <div class="text-sm text-gray-500 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Joined {{ $user->created_at->format('M Y') }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                {{ $user->email }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-wrap gap-1">
                                @forelse($user->roles as $role)
                                    @php
                                        $roleColors = [
                                            'superadmin' => 'bg-red-100 text-red-800 border-red-200',
                                            'admin' => 'bg-orange-100 text-orange-800 border-orange-200',
                                            'event coordinator' => 'bg-blue-100 text-blue-800 border-blue-200',
                                            'tasker' => 'bg-green-100 text-green-800 border-green-200',
                                            'user' => 'bg-gray-100 text-gray-800 border-gray-200'
                                        ];
                                        $roleColor = $roleColors[strtolower($role->name)] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                                    @endphp
                                    <span class="role-badge inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold shadow-sm border {{ $roleColor }}">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                        </svg>
                                        {{ $role->name }}
                                    </span>
                                @empty
                                    <span class="role-badge inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold shadow-sm border bg-gray-100 text-gray-800 border-gray-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                        </svg>
                                        No Role
                                    </span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="status-badge inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold shadow-sm border {{ $statusClass }}">
                                    @if($isActive)
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                    {{ $statusText }}
                                </span>
                                <span class="text-xs text-gray-500 mt-1 flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Last login: {{ $lastLogin }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('users.show', $user) }}" 
                                   class="quick-action-btn inline-flex items-center p-2 bg-blue-100 text-blue-700 hover:bg-blue-200 hover:text-blue-800 rounded-lg transition-all duration-200 border border-blue-200 shadow-sm"
                                   title="View User Details"
                                   aria-label="View user details">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                
                                <a href="{{ route('users.edit', $user) }}" 
                                   class="quick-action-btn inline-flex items-center p-2 bg-yellow-100 text-yellow-700 hover:bg-yellow-200 hover:text-yellow-800 rounded-lg transition-all duration-200 border border-yellow-200 shadow-sm"
                                   title="Edit User"
                                   aria-label="Edit user">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                
                                <a href="{{ route('users.edit', $user) }}#roles" 
                                   class="quick-action-btn inline-flex items-center p-2 bg-purple-100 text-purple-700 hover:bg-purple-200 hover:text-purple-800 rounded-lg transition-all duration-200 border border-purple-200 shadow-sm"
                                   title="Manage User Roles"
                                   aria-label="Manage user roles">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                </a>
                                
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="quick-action-btn inline-flex items-center p-2 bg-red-100 text-red-700 hover:bg-red-200 hover:text-red-800 rounded-lg transition-all duration-200 border border-red-200 shadow-sm"
                                                title="Delete User"
                                                aria-label="Delete user">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                                <p class="text-gray-500 mb-2">
                                    @if($status === 'active')
                                        No active users found.
                                    @elseif($status === 'inactive')
                                        No inactive users found.
                                    @else
                                        No users found.
                                    @endif
                                </p>
                                <a href="{{ route('users.create') }}" class="text-yellow-600 hover:text-yellow-700 font-medium">Create your first user</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-6">
        {{ $users->appends(['status' => $status])->links() }}
    </div>
</div>
@endsection 