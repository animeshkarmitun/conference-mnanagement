@extends('layouts.app')

@section('title', 'Tasker Dashboard')

@section('content')
<!-- Professional Page Header -->
<div class="rounded-2xl bg-gradient-to-r from-yellow-100 via-yellow-50 to-white shadow flex items-center px-8 py-6 mb-10 border border-yellow-200">
    <div class="flex items-center justify-center w-16 h-16 bg-yellow-200 rounded-full mr-6 shadow">
        <svg class="w-8 h-8 text-yellow-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a4 4 0 018 0v2m-4-4V7a4 4 0 10-8 0v6m0 4h8"/></svg>
    </div>
    <div>
        <h1 class="text-3xl font-extrabold text-yellow-800 tracking-tight mb-1">Tasker Dashboard</h1>
        <div class="text-gray-600 text-lg font-medium">Your assigned tasks and quick actions</div>
    </div>
</div>
<hr class="mb-8 border-yellow-200">
<div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
    <!-- My Tasks Card -->
    <div class="bg-white rounded-2xl shadow-lg p-6" aria-label="My Tasks">
        <div class="flex items-center mb-2">
            <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 rounded-full mr-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 018 0v2m-4-4V7a4 4 0 10-8 0v6m0 4h8"/></svg>
            </span>
            <h2 class="text-lg font-semibold text-gray-900">My Tasks</h2>
        </div>
        <ul class="mt-4 space-y-2">
            <li class="flex items-center justify-between">
                <span class="text-gray-700">Prepare conference materials</span>
                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">In Progress</span>
            </li>
            <li class="flex items-center justify-between">
                <span class="text-gray-700">Check venue setup</span>
                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Completed</span>
            </li>
            <li class="flex items-center justify-between">
                <span class="text-gray-700">Send reminder emails</span>
                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Pending</span>
            </li>
        </ul>
    </div>
    <!-- Quick Links Card -->
    <div class="bg-white rounded-2xl shadow-lg p-6" aria-label="Quick Links">
        <div class="flex items-center mb-2">
            <span class="inline-flex items-center justify-center w-8 h-8 bg-green-100 rounded-full mr-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </span>
            <h2 class="text-lg font-semibold text-gray-900">Quick Links</h2>
        </div>
        <ul class="mt-4 space-y-2">
            <li><a href="#" class="text-yellow-700 hover:underline">View All Tasks</a></li>
            <li><a href="#" class="text-yellow-700 hover:underline">Submit Task Update</a></li>
            <li><a href="#" class="text-yellow-700 hover:underline">Contact Supervisor</a></li>
        </ul>
    </div>
</div>
<div class="bg-white rounded-2xl shadow-lg p-6 md:col-span-2" aria-label="Task Status Overview">
    <div class="flex items-center mb-2">
        <span class="inline-flex items-center justify-center w-8 h-8 bg-purple-100 rounded-full mr-2">
            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m4 0h-1v-4h-1m4 0h-1v-4h-1"/></svg>
        </span>
        <h2 class="text-lg font-semibold text-gray-900">Task Status Overview</h2>
    </div>
    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-yellow-100 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-yellow-700 mb-1">2</div>
            <div class="text-sm text-gray-700">Pending</div>
        </div>
        <div class="bg-blue-100 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-blue-700 mb-1">1</div>
            <div class="text-sm text-gray-700">In Progress</div>
        </div>
        <div class="bg-green-100 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-green-700 mb-1">1</div>
            <div class="text-sm text-gray-700">Completed</div>
        </div>
    </div>
</div>
@endsection 