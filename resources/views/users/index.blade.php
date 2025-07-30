@extends('layouts.app')

@section('title', 'Users')

@section('content')
<!-- Professional Page Header -->
<div class="rounded-2xl bg-gradient-to-r from-yellow-100 via-yellow-50 to-white shadow flex items-center px-8 py-6 mb-10 border border-yellow-200">
    <div class="flex items-center justify-center w-16 h-16 bg-yellow-200 rounded-full mr-6 shadow">
        <svg class="w-8 h-8 text-yellow-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
    </div>
    <div>
        <h1 class="text-3xl font-extrabold text-yellow-800 tracking-tight mb-1">Users</h1>
        <div class="text-gray-600 text-lg font-medium">Manage all users and their roles</div>
    </div>
</div>
<hr class="mb-8 border-yellow-200">
<div class="bg-white rounded-xl shadow p-6 mt-8">
    
    <table class="min-w-full divide-y divide-gray-200" id="myTable">
        <thead>
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roles</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <!-- Dummy users for static view -->
            <tr>
                <td class="px-6 py-4 whitespace-nowrap font-semibold">Super Admin</td>
                <td class="px-6 py-4 whitespace-nowrap">superadmin@example.com</td>
                <td class="px-6 py-4 whitespace-nowrap"><span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">superadmin</span></td>
                <td class="px-6 py-4 whitespace-nowrap"><span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Active</span></td>
                <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                    <a href="#" class="text-blue-600 hover:underline">View</a>
                    <a href="#" class="text-yellow-600 hover:underline">Edit</a>
                    <a href="#" class="text-purple-600 hover:underline">Roles</a>
                    <a href="#" class="text-red-600 hover:underline">Delete</a>
                </td>
            </tr>
            <tr>
                <td class="px-6 py-4 whitespace-nowrap font-semibold">Tasker User</td>
                <td class="px-6 py-4 whitespace-nowrap">tasker@example.com</td>
                <td class="px-6 py-4 whitespace-nowrap"><span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">tasker</span></td>
                <td class="px-6 py-4 whitespace-nowrap"><span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Active</span></td>
                <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                    <a href="#" class="text-blue-600 hover:underline">View</a>
                    <a href="#" class="text-yellow-600 hover:underline">Edit</a>
                    <a href="#" class="text-purple-600 hover:underline">Roles</a>
                    <a href="#" class="text-red-600 hover:underline">Delete</a>
                </td>
            </tr>
            <!-- Add more users as needed -->
        </tbody>
    </table>
    
   
</div>
@endsection 