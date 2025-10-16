@extends('layouts.app')

@section('title', 'Preview Email - ' . $emailSetting->type_name)

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="flex items-center space-x-4">
                            <li>
                                <a href="{{ route('admin.email-settings.index') }}" class="text-gray-400 hover:text-gray-500">
                                    <svg class="flex-shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                    </svg>
                                    <span class="sr-only">Email Settings</span>
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="ml-4 text-sm font-medium text-gray-500">{{ $emailSetting->type_name }}</span>
                                </div>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="ml-4 text-sm font-medium text-gray-500">Preview</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h1 class="mt-2 text-3xl font-bold text-gray-900">Email Preview</h1>
                    <p class="mt-2 text-gray-600">Preview how the {{ strtolower($emailSetting->type_name) }} email will look to recipients</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.email-settings.edit', $emailSetting->email_type) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Template
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Email Preview -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <!-- Email Header -->
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Email Preview</h3>
                                <p class="text-sm text-gray-500">This is how the email will appear to recipients</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $emailSetting->type_name }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Email Content -->
                    <div class="p-6">
                        <!-- Email Subject -->
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Subject:</label>
                            <div class="text-lg font-semibold text-gray-900">{{ $preview['subject'] }}</div>
                        </div>

                        <!-- Email Body -->
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <div class="bg-gray-50 px-4 py-2 border-b border-gray-200">
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 bg-red-400 rounded-full"></div>
                                    <div class="w-3 h-3 bg-yellow-400 rounded-full"></div>
                                    <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                                    <span class="ml-2 text-sm text-gray-500">Email Preview</span>
                                </div>
                            </div>
                            <div class="p-6 bg-white">
                                {!! $preview['body'] !!}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Template Details -->
                <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Template Details</h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Greeting</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $preview['greeting'] }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Closing</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $preview['closing'] }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Signature</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $preview['signature'] }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $emailSetting->updated_at->format('M d, Y \a\t g:i A') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Test Email -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Send Test Email</h3>
                    <form method="POST" action="{{ route('admin.email-settings.test', $emailSetting->email_type) }}">
                        @csrf
                        <div class="mb-4">
                            <label for="test_email" class="block text-sm font-medium text-gray-700 mb-2">
                                Test Email Address
                            </label>
                            <input type="email" 
                                   name="test_email" 
                                   id="test_email"
                                   value="{{ auth()->user()->email }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="Enter test email address"
                                   required>
                        </div>
                        <button type="submit" 
                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Send Test Email
                        </button>
                    </form>
                </div>

                <!-- Sample Data Used -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Sample Data Used</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">First Name:</span>
                            <span class="text-sm font-medium text-gray-900">John</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Last Name:</span>
                            <span class="text-sm font-medium text-gray-900">Doe</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Conference Name:</span>
                            <span class="text-sm font-medium text-gray-900">Sample Conference 2024</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">System Name:</span>
                            <span class="text-sm font-medium text-gray-900">{{ config('app.name') }}</span>
                        </div>
                        @if($emailSetting->email_type === 'session_notification')
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Session Title:</span>
                                <span class="text-sm font-medium text-gray-900">Introduction to Technology</span>
                            </div>
                        @endif
                        @if($emailSetting->email_type === 'task_notification')
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Task Title:</span>
                                <span class="text-sm font-medium text-gray-900">Prepare Presentation</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('admin.email-settings.edit', $emailSetting->email_type) }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit Template
                        </a>
                        <a href="{{ route('admin.email-settings.index') }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection















