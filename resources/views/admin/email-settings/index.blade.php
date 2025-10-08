@extends('layouts.app')

@section('title', 'Email Settings')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Email Settings</h1>
                    <p class="mt-2 text-gray-600">Manage email templates and notifications for your conference management system</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.email-settings.preview', 'general') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Preview All
                    </a>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Email Settings Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($emailTypes as $type => $name)
                @php
                    $setting = $emailSettings->where('email_type', $type)->first();
                    $isConfigured = $setting !== null;
                @endphp
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $name }}</h3>
                                    <p class="text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $type)) }}</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                @if($isConfigured)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Configured
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.726-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        Default
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <p class="text-sm text-gray-600">
                                @switch($type)
                                    @case('task_notification')
                                        Notifications for task assignments, updates, and completions
                                        @break
                                    @case('session_notification')
                                        Notifications for session assignments and updates
                                        @break
                                    @case('travel_notification')
                                        Notifications for travel arrangements and updates
                                        @break
                                    @case('conference_update')
                                        General conference updates and announcements
                                        @break
                                    @case('profile_update')
                                        Notifications for profile changes and updates
                                        @break
                                    @case('passwordless_login')
                                        Authentication emails for passwordless login
                                        @break
                                    @default
                                        General purpose email notifications
                                @endswitch
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.email-settings.edit', $type) }}" 
                               class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit
                            </a>
                            <a href="{{ route('admin.email-settings.preview', $type) }}" 
                               class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                        </div>

                        <!-- Quick Stats -->
                        @if($isConfigured)
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="text-xs text-gray-500">
                                    <div class="flex justify-between">
                                        <span>Subject:</span>
                                        <span class="font-medium">{{ Str::limit($setting->subject_template, 30) }}</span>
                                    </div>
                                    <div class="flex justify-between mt-1">
                                        <span>Last Updated:</span>
                                        <span class="font-medium">{{ $setting->updated_at->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Help Section -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Need Help?</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>Use the available variables in your templates to personalize emails. You can preview templates and send test emails to verify your changes.</p>
                        <ul class="mt-2 list-disc list-inside">
                            <li>Use <code class="bg-blue-100 px-1 rounded">{first_name}</code> for recipient's first name</li>
                            <li>Use <code class="bg-blue-100 px-1 rounded">{conference_name}</code> for conference name</li>
                            <li>Use <code class="bg-blue-100 px-1 rounded">{system_name}</code> for system name</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

