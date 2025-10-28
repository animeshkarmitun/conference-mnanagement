@extends('layouts.app')

@section('title', 'Create Notification Template')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
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
                            <span class="ml-4 text-sm font-medium text-gray-500">Create Template</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="mt-2 text-3xl font-bold text-gray-900">Create Notification Template</h1>
            <p class="mt-2 text-gray-600">Create a new notification message template for your conference management system</p>
        </div>

        <form action="{{ route('admin.notification-templates.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="bg-white shadow-sm rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Template Details</h3>
                </div>
                <div class="px-6 py-4 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="notification_type" class="block text-sm font-medium text-gray-700">
                                Notification Type <span class="text-red-500">*</span>
                            </label>
                            <select name="notification_type" id="notification_type" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('notification_type') border-red-300 @enderror" 
                                    required>
                                <option value="">Select notification type</option>
                                @foreach($notificationTypes as $key => $label)
                                    <option value="{{ $key }}" {{ old('notification_type') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('notification_type')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="flex items-center">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="is_active" id="is_active" value="1"
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" 
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                            </div>
                            <div class="ml-3">
                                <label for="is_active" class="text-sm font-medium text-gray-700">Active</label>
                                <p class="text-sm text-gray-500">Enable this template for use</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">
                            Description
                        </label>
                        <textarea name="description" id="description" rows="2"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-300 @enderror"
                                  placeholder="Optional description for this template">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="message_template" class="block text-sm font-medium text-gray-700">
                            Message Template <span class="text-red-500">*</span>
                        </label>
                        <textarea name="message_template" id="message_template" rows="4"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('message_template') border-red-300 @enderror"
                                  placeholder="Enter the notification message template with variables like {user_name}, {task_title}, etc."
                                  required>{{ old('message_template') }}</textarea>
                        @error('message_template')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-gray-500">
                            Use variables like {user_name}, {task_title}, {conference_name}, etc. in your template.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Available Variables -->
            <div class="bg-white shadow-sm rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Available Variables</h3>
                    <p class="mt-1 text-sm text-gray-500">Click on a variable to insert it into your template</p>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="variables-list">
                        @foreach($variables as $variable)
                            <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 cursor-pointer variable-item"
                                 data-variable="{ {{ $variable['variable_name'] }} }">
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

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-3">
                <a href="{{ route('admin.notification-templates.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Cancel
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Create Template
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const notificationTypeSelect = document.getElementById('notification_type');
    const variablesList = document.getElementById('variables-list');
    const messageTemplate = document.getElementById('message_template');
    
    // Filter variables based on selected notification type
    function filterVariables() {
        const selectedType = notificationTypeSelect.value;
        const allVariables = @json($variables);
        
        let filteredVariables = allVariables;
        if (selectedType && selectedType !== '*') {
            filteredVariables = allVariables.filter(variable => 
                variable.notification_type === selectedType || variable.notification_type === '*'
            );
        }
        
        variablesList.innerHTML = '';
        filteredVariables.forEach(variable => {
            const div = document.createElement('div');
            div.className = 'border border-gray-200 rounded-lg p-3 hover:bg-gray-50 cursor-pointer variable-item';
            div.setAttribute('data-variable', `{ ${variable.variable_name} }`);
            div.innerHTML = `
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <code class="text-sm font-mono text-blue-600">{ ${variable.variable_name} }</code>
                        <p class="mt-1 text-xs text-gray-600">${variable.variable_description}</p>
                        <p class="mt-1 text-xs text-gray-500">Example: ${variable.example_value}</p>
                    </div>
                </div>
            `;
            variablesList.appendChild(div);
        });
        
        // Add click handlers to new variable items
        addVariableClickHandlers();
    }
    
    // Add click handlers for variable insertion
    function addVariableClickHandlers() {
        document.querySelectorAll('.variable-item').forEach(item => {
            item.addEventListener('click', function() {
                const variable = this.getAttribute('data-variable');
                const textarea = messageTemplate;
                const start = textarea.selectionStart;
                const end = textarea.selectionEnd;
                const text = textarea.value;
                const before = text.substring(0, start);
                const after = text.substring(end, text.length);
                
                textarea.value = before + variable + after;
                textarea.focus();
                textarea.setSelectionRange(start + variable.length, start + variable.length);
                
                // Trigger input event for any listeners
                textarea.dispatchEvent(new Event('input', { bubbles: true }));
            });
        });
    }
    
    notificationTypeSelect.addEventListener('change', filterVariables);
    
    // Initial load
    filterVariables();
});
</script>
@endsection
