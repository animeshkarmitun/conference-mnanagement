@extends('layouts.app')

@section('title', 'Roles')

@push('styles')
<style>
    .table-row-hover:hover {
        background-color: #f8fafc;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .quick-action-btn {
        transition: all 0.2s ease;
    }
    
    .quick-action-btn:hover {
        transform: scale(1.05);
    }
    
    .permission-badge {
        transition: all 0.2s ease;
    }
    
    .permission-badge:hover {
        transform: scale(1.05);
    }
    
    .sortable-header {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .sortable-header:hover {
        background-color: #f1f5f9;
    }
    
    .tab-link {
        transition: all 0.2s ease;
    }
    
    .tab-link:hover {
        transform: translateY(-1px);
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
    .animate-delay-5 { animation-delay: 0.5s; }
    
    /* Modern color scheme overrides */
    .modern-primary {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: white;
    }
    
    .modern-primary:hover {
        background: linear-gradient(135deg, #5855eb, #7c3aed);
    }
    
    .modern-secondary {
        background: linear-gradient(135deg, #64748b, #475569);
        color: white;
    }
    
    .modern-secondary:hover {
        background: linear-gradient(135deg, #475569, #334155);
    }
    
    .modern-success {
        background: linear-gradient(135deg, #059669, #047857);
        color: white;
    }
    
    .modern-success:hover {
        background: linear-gradient(135deg, #047857, #065f46);
    }
    
    .modern-warning {
        background: linear-gradient(135deg, #e11d48, #be123c);
        color: white;
    }
    
    .modern-warning:hover {
        background: linear-gradient(135deg, #be123c, #9f1239);
    }
    
    .modern-info {
        background: linear-gradient(135deg, #0891b2, #0e7490);
        color: white;
    }
    
    .modern-info:hover {
        background: linear-gradient(135deg, #0e7490, #155e75);
    }
    
    .modern-admin {
        background: linear-gradient(135deg, #7c3aed, #6d28d9);
        color: white;
    }
    
    .modern-admin:hover {
        background: linear-gradient(135deg, #6d28d9, #5b21b6);
    }
</style>
@endpush

@section('content')
<!-- Enhanced Header Section -->
<div class="rounded-2xl bg-gradient-to-r from-indigo-100 via-indigo-50 to-white shadow flex items-center px-8 py-6 mb-6 border border-indigo-200">
    <div class="flex items-center justify-center w-16 h-16 bg-indigo-200 rounded-full mr-6 shadow-lg">
        <svg class="w-8 h-8 text-indigo-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
        </svg>
    </div>
    <div class="flex-1">
        <h1 class="text-3xl font-extrabold text-indigo-800 tracking-tight mb-1">Roles</h1>
        <div class="text-slate-600 text-lg font-medium">Manage user roles and permissions</div>
    </div>
    
    <!-- Quick Actions -->
    <div class="flex space-x-3">
        <a href="{{ route('roles.create') }}" class="modern-primary px-6 py-3 rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-xl flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add Role
        </a>
    </div>
</div>

<!-- Status Tabs -->
<div class="bg-white rounded-2xl shadow-lg mb-6 border border-slate-100 animate-fade-in-up animate-delay-1">
    <div class="border-b border-slate-200">
        <nav class="flex space-x-8 px-6" aria-label="Tabs">
            <a href="{{ route('roles.index', ['status' => 'system']) }}" 
               class="tab-link py-4 px-1 border-b-2 font-medium text-sm {{ $status === 'system' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    System Roles
                    <span class="ml-2 bg-slate-100 text-slate-900 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $roleCounts['system'] }}</span>
                </div>
            </a>
            
            <a href="{{ route('roles.index', ['status' => 'user']) }}" 
               class="tab-link py-4 px-1 border-b-2 font-medium text-sm {{ $status === 'user' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    User Roles
                    <span class="ml-2 bg-slate-100 text-slate-900 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $roleCounts['user'] }}</span>
                </div>
            </a>
            
            <a href="{{ route('roles.index', ['status' => 'all']) }}" 
               class="tab-link py-4 px-1 border-b-2 font-medium text-sm {{ $status === 'all' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    All Roles
                    <span class="ml-2 bg-slate-100 text-slate-900 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $roleCounts['all'] }}</span>
                </div>
            </a>
        </nav>
    </div>
</div>

<!-- Roles Table -->
<div class="bg-white rounded-2xl shadow-lg p-6 border border-slate-100 animate-fade-in-up animate-delay-2">
    @if($roles->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Permissions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Users</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @foreach($roles as $role)
                        @php
                            $isSystemRole = in_array($role->name, ['superadmin', 'admin']);
                            $roleClass = $isSystemRole ? 'bg-rose-100 text-rose-800 border-rose-200' : 'bg-indigo-100 text-indigo-800 border-indigo-200';
                        @endphp
                        <tr class="table-row-hover hover:bg-slate-50 transition-all duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full {{ $isSystemRole ? 'bg-rose-100' : 'bg-indigo-100' }} flex items-center justify-center">
                                        <svg class="w-5 h-5 {{ $isSystemRole ? 'text-rose-600' : 'text-indigo-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-slate-900">{{ ucfirst($role->name) }}</div>
                                        <div class="text-sm text-slate-500">{{ $role->description ?? 'No description' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $permissions = $role->permissions ?? [];
                                @endphp
                                @if(empty($permissions))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                        </svg>
                                        All Permissions
                                    </span>
                                @else
                                    <div class="flex flex-wrap gap-1">
                                        @foreach(array_slice($permissions, 0, 3) as $permission)
                                            @php
                                                $permissionColors = [
                                                    'system' => 'bg-rose-100 text-rose-800',
                                                    'user' => 'bg-indigo-100 text-indigo-800',
                                                    'content' => 'bg-emerald-100 text-emerald-800',
                                                    'report' => 'bg-rose-100 text-rose-800'
                                                ];
                                                $permissionType = str_contains($permission, 'admin') ? 'system' : 
                                                                (str_contains($permission, 'user') ? 'user' : 
                                                                (str_contains($permission, 'view') ? 'content' : 'report'));
                                                $permissionColor = $permissionColors[$permissionType] ?? 'bg-slate-100 text-slate-800';
                                            @endphp
                                            <span class="permission-badge inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $permissionColor }}">
                                                {{ $permission }}
                                            </span>
                                        @endforeach
                                        @if(count($permissions) > 3)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                                                +{{ count($permissions) - 3 }} more
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                    {{ $role->users->count() }} users
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('roles.show', $role) }}" 
                                       class="quick-action-btn inline-flex items-center p-2 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded-lg transition-colors duration-200"
                                       title="View Role"
                                       aria-label="View role">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    
                                    <a href="{{ route('roles.edit', $role) }}" 
                                       class="quick-action-btn inline-flex items-center p-2 text-slate-600 hover:text-slate-800 hover:bg-slate-50 rounded-lg transition-colors duration-200"
                                       title="Edit Role"
                                       aria-label="Edit role">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    
                                    <a href="{{ route('roles.assign-users', $role) }}" 
                                       class="quick-action-btn inline-flex items-center p-2 text-violet-600 hover:text-violet-800 hover:bg-violet-50 rounded-lg transition-colors duration-200"
                                       title="Assign Users"
                                       aria-label="Assign users to role">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </a>
                                    
                                    @if(!$isSystemRole)
                                        <form action="{{ route('roles.destroy', $role) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this role?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="quick-action-btn inline-flex items-center p-2 text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded-lg transition-colors duration-200"
                                                    title="Delete Role"
                                                    aria-label="Delete role">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="mt-6">
            {{ $roles->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <div class="mx-auto h-12 w-12 text-slate-400">
                <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </div>
            <h3 class="mt-2 text-sm font-medium text-slate-900">No roles found</h3>
            <p class="mt-1 text-sm text-slate-500">Get started by creating a new role.</p>
            <div class="mt-6">
                <a href="{{ route('roles.create') }}" class="text-indigo-600 hover:text-indigo-700 font-medium">Create your first role</a>
            </div>
        </div>
    @endif
</div>
@endsection 