@extends('layouts.app')

@section('title', 'Send Bulk Emails')

@section('content')
<div class="max-w-3xl mx-auto mt-8 bg-white rounded-2xl shadow p-8">
    <h1 class="text-2xl font-bold text-yellow-700 mb-6 flex items-center gap-2">
        <svg class="w-7 h-7 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        Send Bulk Emails
    </h1>
    <!-- Recipient Selection -->
    <div class="mb-6">
        <label class="block text-sm font-semibold text-gray-700 mb-2">Select Recipients</label>
        <div class="flex flex-wrap gap-4">
            <select class="rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                <option>All Roles</option>
                <option>Participants</option>
                <option>Speakers</option>
                <option>Admins</option>
            </select>
            <select class="rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                <option>All Statuses</option>
                <option>Pending</option>
                <option>Approved</option>
                <option>Declined</option>
            </select>
        </div>
    </div>
    <!-- Rich Text Editor (Static) -->
    <div class="mb-6">
        <label class="block text-sm font-semibold text-gray-700 mb-2">Message</label>
        <div class="border border-gray-300 rounded-md bg-gray-50 p-4 min-h-[120px] text-gray-700" contenteditable="true" style="outline:none;">
            <p>Type your message here...</p>
        </div>
        <div class="text-xs text-gray-400 mt-1">Rich text editing enabled (static demo)</div>
    </div>
    <!-- Tracking Section (Static) -->
    <div class="mb-6">
        <label class="block text-sm font-semibold text-gray-700 mb-2">Tracking</label>
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex items-center gap-2 bg-blue-50 rounded-lg px-4 py-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span class="text-sm text-blue-700">Delivery: <span class="font-bold">--</span></span>
            </div>
            <div class="flex items-center gap-2 bg-green-50 rounded-lg px-4 py-2">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a4 4 0 018 0v2m-4-4V7a4 4 0 10-8 0v6m0 4h8"/></svg>
                <span class="text-sm text-green-700">Responses: <span class="font-bold">--</span></span>
            </div>
        </div>
        <div class="text-xs text-gray-400 mt-1">Tracking will show delivery and responses (static demo)</div>
    </div>
    <!-- Send Button -->
    <div class="flex justify-end">
        <button class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-6 rounded-lg shadow transition">Send Email</button>
    </div>
</div>
@endsection 