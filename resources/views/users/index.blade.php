@extends('layouts.app')

@section('title', 'Users')

@section('content')
<!-- Professional Page Header -->
<div class="rounded-2xl bg-gradient-to-r from-yellow-100 via-yellow-50 to-white shadow flex items-center px-8 py-6 mb-6 border border-yellow-200">
    <div class="flex items-center justify-center w-16 h-16 bg-yellow-200 rounded-full mr-6 shadow">
        <svg class="w-8 h-8 text-yellow-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
    </div>
    <div>
        <h1 class="text-3xl font-extrabold text-yellow-800 tracking-tight mb-1">Users</h1>
        <div class="text-gray-600 text-lg font-medium">Manage all users and their roles</div>
    </div>
</div>

<!-- User Status Tabs -->
<div class="bg-white rounded-xl shadow mb-6">
    <div class="border-b border-gray-200">
        <nav class="flex space-x-8 px-6" aria-label="Tabs">
            <a href="{{ route('users.index', ['status' => 'active']) }}" 
               class="tab-link py-4 px-1 border-b-2 font-medium text-sm {{ $status === 'active' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Active Users
                <span class="ml-2 bg-gray-100 text-gray-900 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $userCounts['active'] }}</span>
            </a>
            
            <a href="{{ route('users.index', ['status' => 'inactive']) }}" 
               class="tab-link py-4 px-1 border-b-2 font-medium text-sm {{ $status === 'inactive' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Inactive Users
                <span class="ml-2 bg-gray-100 text-gray-900 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $userCounts['inactive'] }}</span>
            </a>
            
            <a href="{{ route('users.index', ['status' => 'all']) }}" 
               class="tab-link py-4 px-1 border-b-2 font-medium text-sm {{ $status === 'all' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                All Users
                <span class="ml-2 bg-gray-100 text-gray-900 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $userCounts['all'] }}</span>
            </a>
        </nav>
    </div>
</div>

<div class="bg-white rounded-xl shadow p-6">
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
                
                <tr class="hover:bg-gray-50 transition-colors duration-200">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                @if($user->profile_picture)
                                    <img class="h-10 w-10 rounded-full" src="{{ asset('storage/' . $user->profile_picture) }}" alt="{{ $user->first_name }}">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center">
                                        <span class="text-yellow-700 font-bold text-sm">{{ strtoupper(substr($user->first_name, 0, 1)) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $user->first_name }} {{ $user->last_name }}</div>
                                <div class="text-sm text-gray-500">Joined {{ $user->created_at->format('M Y') }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-wrap gap-1">
                            @forelse($user->roles as $role)
                                @php
                                    $roleColors = [
                                        'superadmin' => 'bg-red-100 text-red-800',
                                        'admin' => 'bg-orange-100 text-orange-800',
                                        'event coordinator' => 'bg-blue-100 text-blue-800',
                                        'tasker' => 'bg-green-100 text-green-800',
                                        'user' => 'bg-gray-100 text-gray-800'
                                    ];
                                    $roleColor = $roleColors[strtolower($role->name)] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $roleColor }}">
                                    {{ $role->name }}
                                </span>
                            @empty
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    No Role
                                </span>
                            @endforelse
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-col">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                            <span class="text-xs text-gray-500 mt-1">Last login: {{ $lastLogin }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                        <a href="{{ route('users.show', $user) }}" 
                           class="inline-flex items-center p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                           title="View User Details"
                           aria-label="View user details">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </a>
                        
                        <a href="{{ route('users.edit', $user) }}" 
                           class="inline-flex items-center p-2 text-yellow-600 hover:text-yellow-800 hover:bg-yellow-50 rounded-lg transition-colors duration-200"
                           title="Edit User"
                           aria-label="Edit user">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                        
                        <a href="{{ route('users.edit', $user) }}#roles" 
                           class="inline-flex items-center p-2 text-purple-600 hover:text-purple-800 hover:bg-purple-50 rounded-lg transition-colors duration-200"
                           title="Manage User Roles"
                           aria-label="Manage user roles">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </a>
                        
                        @if($user->id !== auth()->id())
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                        title="Delete User"
                                        aria-label="Delete user">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                        @if($status === 'active')
                            No active users found.
                        @elseif($status === 'inactive')
                            No inactive users found.
                        @else
                            No users found.
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="mt-4">
        {{ $users->appends(['status' => $status])->links() }}
    </div>
</div>

<style>
.tab-link {
    transition: all 0.2s ease-in-out;
}

.tab-link:hover {
    transform: translateY(-1px);
}
</style>
@endsection 