@extends('layouts.app')

@section('title', 'Assign Users to Role')

@section('content')
<!-- Professional Page Header -->
<div class="rounded-2xl bg-gradient-to-r from-yellow-100 via-yellow-50 to-white shadow flex items-center px-8 py-6 mb-6 border border-yellow-200">
    <div class="flex items-center justify-center w-16 h-16 bg-yellow-200 rounded-full mr-6 shadow">
        <svg class="w-8 h-8 text-yellow-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
        </svg>
    </div>
    <div>
        <h1 class="text-3xl font-extrabold text-yellow-800 tracking-tight mb-1">Assign Users to Role</h1>
        <div class="text-gray-600 text-lg font-medium">Manage user assignments for {{ ucfirst($role->name) }} role</div>
    </div>
</div>

<div class="bg-white rounded-xl shadow p-6">
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-2">Role Information</h2>
        <div class="flex items-center space-x-4">
            <div class="flex items-center">
                <div class="h-8 w-8 rounded-full {{ in_array($role->name, ['superadmin', 'admin']) ? 'bg-red-100' : 'bg-blue-100' }} flex items-center justify-center mr-3">
                    @if(in_array($role->name, ['superadmin', 'admin']))
                        <svg class="w-4 h-4 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    @else
                        <svg class="w-4 h-4 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    @endif
                </div>
                <span class="text-lg font-medium text-gray-900">{{ ucfirst($role->name) }}</span>
            </div>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ in_array($role->name, ['superadmin', 'admin']) ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                {{ in_array($role->name, ['superadmin', 'admin']) ? 'System' : 'User' }} Role
            </span>
            <span class="text-sm text-gray-500">{{ $role->users->count() }} users assigned</span>
        </div>
    </div>

    <form action="{{ route('roles.update-user-assignments', $role) }}" method="POST">
        @csrf
        
        <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Select Users to Assign</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($users as $user)
                    @php
                        $isAssigned = $role->users->contains($user->id);
                        $hasOtherRoles = $user->roles->where('id', '!=', $role->id)->count() > 0;
                    @endphp
                    
                    <div class="border rounded-lg p-4 {{ $isAssigned ? 'border-yellow-300 bg-yellow-50' : 'border-gray-200 bg-white' }} hover:border-yellow-400 transition-colors">
                        <label class="flex items-start space-x-3 cursor-pointer">
                            <input type="checkbox" 
                                   name="users[]" 
                                   value="{{ $user->id }}" 
                                   {{ $isAssigned ? 'checked' : '' }}
                                   class="mt-1 h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300 rounded">
                            
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        @if($user->profile_picture)
                                            <img class="h-8 w-8 rounded-full" src="{{ asset('storage/' . $user->profile_picture) }}" alt="{{ $user->first_name }}">
                                        @else
                                            <div class="h-8 w-8 rounded-full bg-yellow-100 flex items-center justify-center">
                                                <span class="text-yellow-700 font-bold text-sm">{{ strtoupper(substr($user->first_name, 0, 1)) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $user->first_name }} {{ $user->last_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                                
                                @if($hasOtherRoles)
                                    <div class="mt-2">
                                        <p class="text-xs text-gray-500">Also has:</p>
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            @foreach($user->roles->where('id', '!=', $role->id)->take(2) as $otherRole)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    {{ $otherRole->name }}
                                                </span>
                                            @endforeach
                                            @if($user->roles->where('id', '!=', $role->id)->count() > 2)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                    +{{ $user->roles->where('id', '!=', $role->id)->count() - 2 }} more
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                
                                @if($isAssigned)
                                    <div class="mt-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Currently Assigned
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </label>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-between items-center">
            <a href="{{ route('roles.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Roles
            </a>
            
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Update Assignments
            </button>
        </div>
    </form>
</div>

<script>
// Add some interactivity
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('input[name="users[]"]');
    const updateButton = document.querySelector('button[type="submit"]');
    
    function updateButtonState() {
        const checkedCount = document.querySelectorAll('input[name="users[]"]:checked').length;
        if (checkedCount > 0) {
            updateButton.textContent = `Update Assignments (${checkedCount} selected)`;
        } else {
            updateButton.textContent = 'Update Assignments';
        }
    }
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateButtonState);
    });
    
    updateButtonState();
});
</script>
@endsection 