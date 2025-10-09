@extends('layouts.app')

@section('title', 'Preview Notification Template')

@section('content')
<div class="py-6">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-4">
                    <li>
                        <a href="{{ route('admin.notification-templates.index') }}" class="text-gray-400 hover:text-gray-500">
                            <svg class="flex-shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                            </svg>
                            <span class="sr-only">Notification Templates</span>
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-4 text-sm font-medium text-gray-500">{{ $notificationTemplate->type_name }}</span>
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
            <h1 class="mt-2 text-3xl font-bold text-gray-900">Preview: {{ $notificationTemplate->type_name }}</h1>
            <p class="mt-2 text-gray-600">See how this notification template will appear to users</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Preview -->
            <div>
                <h2 class="text-lg font-medium text-gray-900 mb-4">Template Preview</h2>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-sm text-gray-900 leading-relaxed">{{ $preview }}</p>
                                </div>
                                <p class="mt-2 text-xs text-gray-500">{{ now()->format('M d, Y g:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Template Details -->
            <div>
                <h2 class="text-lg font-medium text-gray-900 mb-4">Template Details</h2>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6 space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Type</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $notificationTemplate->type_name }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                @if($notificationTemplate->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Inactive
                                    </span>
                                @endif
                            </dd>
                        </div>
                        
                        @if($notificationTemplate->description)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Description</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $notificationTemplate->description }}</dd>
                            </div>
                        @endif
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Template Source</dt>
                            <dd class="mt-1">
                                <div class="bg-gray-50 rounded-md p-3">
                                    <code class="text-sm text-gray-800 font-mono">{{ $notificationTemplate->message_template }}</code>
                                </div>
                            </dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sample Data Used -->
        <div class="mt-8">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Sample Data Used</h2>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <p class="text-sm text-gray-500">The following sample data was used to generate the preview above</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($variables as $variable)
                            <div class="border border-gray-200 rounded-lg p-3">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <code class="text-sm font-mono text-blue-600">{ {{ $variable['variable_name'] }} }</code>
                                        <p class="mt-1 text-xs text-gray-600">{{ $variable['variable_description'] }}</p>
                                        <p class="mt-1 text-xs text-gray-500">Example: {{ $variable['example_value'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-8 flex items-center justify-end space-x-3">
            <a href="{{ route('admin.notification-templates.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Templates
            </a>
            <a href="{{ route('admin.notification-templates.edit', $notificationTemplate) }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Template
            </a>
        </div>
    </div>
</div>
@endsection