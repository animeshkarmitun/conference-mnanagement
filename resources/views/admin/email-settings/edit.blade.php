@extends('layouts.app')

@section('title', 'Edit Email Settings - ' . $emailSetting->type_name)

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
                        </ol>
                    </nav>
                    <h1 class="mt-2 text-3xl font-bold text-gray-900">Edit Email Settings</h1>
                    <p class="mt-2 text-gray-600">Customize the email template for {{ strtolower($emailSetting->type_name) }} notifications</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.email-settings.preview', $emailSetting->email_type) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Preview
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Form -->
            <div class="lg:col-span-2">
                <form method="POST" action="{{ route('admin.email-settings.update', $emailSetting->email_type) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Subject Template -->
                    <div>
                        <label for="subject_template" class="block text-sm font-medium text-gray-700 mb-2">
                            Subject Template
                        </label>
                        <input type="text" 
                               name="subject_template" 
                               id="subject_template"
                               value="{{ old('subject_template', $emailSetting->subject_template) }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('subject_template') border-red-300 @enderror"
                               placeholder="Enter email subject template">
                        @error('subject_template')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if(is_array($errors->get('subject_template')))
                            @foreach($errors->get('subject_template') as $error)
                                <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
                            @endforeach
                        @endif
                    </div>

                    <!-- Greeting Template -->
                    <div>
                        <label for="greeting_template" class="block text-sm font-medium text-gray-700 mb-2">
                            Greeting Template
                        </label>
                        <input type="text" 
                               name="greeting_template" 
                               id="greeting_template"
                               value="{{ old('greeting_template', $emailSetting->greeting_template) }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('greeting_template') border-red-300 @enderror"
                               placeholder="Enter greeting template">
                        @error('greeting_template')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if(is_array($errors->get('greeting_template')))
                            @foreach($errors->get('greeting_template') as $error)
                                <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
                            @endforeach
                        @endif
                    </div>

                    <!-- Body Template -->
                    <div>
                        <label for="body_template" class="block text-sm font-medium text-gray-700 mb-2">
                            Body Template
                        </label>
                        <textarea name="body_template" 
                                  id="body_template"
                                  rows="8"
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('body_template') border-red-300 @enderror"
                                  placeholder="Enter email body template">{{ old('body_template', $emailSetting->body_template) }}</textarea>
                        @error('body_template')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if(is_array($errors->get('body_template')))
                            @foreach($errors->get('body_template') as $error)
                                <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
                            @endforeach
                        @endif
                    </div>

                    <!-- Closing Template -->
                    <div>
                        <label for="closing_template" class="block text-sm font-medium text-gray-700 mb-2">
                            Closing Template
                        </label>
                        <input type="text" 
                               name="closing_template" 
                               id="closing_template"
                               value="{{ old('closing_template', $emailSetting->closing_template) }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('closing_template') border-red-300 @enderror"
                               placeholder="Enter closing template">
                        @error('closing_template')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if(is_array($errors->get('closing_template')))
                            @foreach($errors->get('closing_template') as $error)
                                <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
                            @endforeach
                        @endif
                    </div>

                    <!-- System Signature -->
                    <div>
                        <label for="system_signature" class="block text-sm font-medium text-gray-700 mb-2">
                            System Signature
                        </label>
                        <input type="text" 
                               name="system_signature" 
                               id="system_signature"
                               value="{{ old('system_signature', $emailSetting->system_signature) }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('system_signature') border-red-300 @enderror"
                               placeholder="Enter system signature">
                        @error('system_signature')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if(is_array($errors->get('system_signature')))
                            @foreach($errors->get('system_signature') as $error)
                                <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
                            @endforeach
                        @endif
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <div class="flex space-x-3">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Save Changes
                            </button>
                            <a href="{{ route('admin.email-settings.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                Cancel
                            </a>
                        </div>
                        <form method="POST" action="{{ route('admin.email-settings.reset', $emailSetting->email_type) }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="inline-flex items-center px-3 py-2 text-sm text-red-600 hover:text-red-800 focus:outline-none"
                                    onclick="return confirm('Are you sure you want to reset this template to default? This action cannot be undone.')">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Reset to Default
                            </button>
                        </form>
                    </div>
                </form>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Available Variables -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Available Variables</h3>
                    <div class="space-y-3">
                        @foreach($availableVariables as $variable)
                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                <div>
                                    <code class="text-sm font-mono text-blue-600">{ {{ $variable['variable_name'] }} }</code>
                                    @if($variable['variable_description'])
                                        <p class="text-xs text-gray-500 mt-1">{{ $variable['variable_description'] }}</p>
                                    @endif
                                </div>
                                <button type="button" 
                                        onclick="insertVariable('{{ $variable['variable_name'] }}')"
                                        class="text-xs text-blue-600 hover:text-blue-800">
                                    Insert
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Test Email -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Test Email</h3>
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
            </div>
        </div>
    </div>
</div>

<script>
function insertVariable(variableName) {
    const textarea = document.getElementById('body_template');
    const variable = '{' + variableName + '}';
    
    // Insert at cursor position
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = textarea.value;
    const before = text.substring(0, start);
    const after = text.substring(end, text.length);
    
    textarea.value = before + variable + after;
    
    // Set cursor position after the inserted variable
    const newPos = start + variable.length;
    textarea.setSelectionRange(newPos, newPos);
    textarea.focus();
}
</script>
@endsection


