@extends('layouts.app')

@section('title', 'Roles')

@section('content')
<!-- Professional Page Header -->
<div class="rounded-2xl bg-gradient-to-r from-yellow-100 via-yellow-50 to-white shadow flex items-center px-8 py-6 mb-10 border border-yellow-200">
    <div class="flex items-center justify-center w-16 h-16 bg-yellow-200 rounded-full mr-6 shadow">
        <svg class="w-8 h-8 text-yellow-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
    </div>
    <div>
        <h1 class="text-3xl font-extrabold text-yellow-800 tracking-tight mb-1">Roles</h1>
        <div class="text-gray-600 text-lg font-medium">Manage all roles and their permissions</div>
    </div>
</div>
<hr class="mb-8 border-yellow-200">
<div class="bg-white rounded-xl shadow p-6 mt-8">
    <table class="min-w-full divide-y divide-gray-200">
        <thead>
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permissions</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <!-- Dummy roles for static view -->
            <tr>
                <td class="px-6 py-4 whitespace-nowrap font-semibold">superadmin</td>
                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-700">*</td>
                <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                    <a href="#" class="text-blue-600 hover:underline">View</a>
                    <a href="#" class="text-yellow-600 hover:underline">Edit</a>
                    <a href="#" class="text-purple-600 hover:underline">Assign Users</a>
                    <a href="#" class="text-red-600 hover:underline">Delete</a>
                </td>
            </tr>
            <tr>
                <td class="px-6 py-4 whitespace-nowrap font-semibold">tasker</td>
                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-700">tasks.view, tasks.update, notifications.view</td>
                <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                    <a href="#" class="text-blue-600 hover:underline">View</a>
                    <a href="#" class="text-yellow-600 hover:underline">Edit</a>
                    <a href="#" class="text-purple-600 hover:underline">Assign Users</a>
                    <a href="#" class="text-red-600 hover:underline">Delete</a>
                </td>
            </tr>
            <!-- Add more roles as needed -->
        </tbody>
    </table>
</div>
 
@endsection 