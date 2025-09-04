@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Create ID Card Template</h2>
                    <a href="{{ route('id-cards.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Back to Templates
                    </a>
                </div>

                <form action="{{ route('id-cards.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Information -->
                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Template Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">Card Type</label>
                                <select name="type" id="type" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Type</option>
                                    <option value="participant" {{ old('type') === 'participant' ? 'selected' : '' }}>Participant</option>
                                    <option value="company_worker" {{ old('type') === 'company_worker' ? 'selected' : '' }}>Company Worker</option>
                                </select>
                                @error('type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div id="conference-field" class="hidden">
                                <label for="conference_id" class="block text-sm font-medium text-gray-700">Conference</label>
                                <select name="conference_id" id="conference_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Conference</option>
                                    @foreach($conferences as $conference)
                                        <option value="{{ $conference->id }}" {{ old('conference_id') == $conference->id ? 'selected' : '' }}>
                                            {{ $conference->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('conference_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Color Configuration -->
                        <div class="space-y-4">
                            <div>
                                <label for="background_color" class="block text-sm font-medium text-gray-700">Background Color</label>
                                <div class="mt-1 flex items-center space-x-2">
                                    <input type="color" name="background_color" id="background_color" value="{{ old('background_color', '#ffffff') }}"
                                        class="h-10 w-20 border border-gray-300 rounded">
                                    <input type="text" value="{{ old('background_color', '#ffffff') }}" 
                                        class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="#ffffff">
                                </div>
                                @error('background_color')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="text_color" class="block text-sm font-medium text-gray-700">Text Color</label>
                                <div class="mt-1 flex items-center space-x-2">
                                    <input type="color" name="text_color" id="text_color" value="{{ old('text_color', '#000000') }}"
                                        class="h-10 w-20 border border-gray-300 rounded">
                                    <input type="text" value="{{ old('text_color', '#000000') }}"
                                        class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="#000000">
                                </div>
                                @error('text_color')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="accent_color" class="block text-sm font-medium text-gray-700">Accent Color</label>
                                <div class="mt-1 flex items-center space-x-2">
                                    <input type="color" name="accent_color" id="accent_color" value="{{ old('accent_color', '#007bff') }}"
                                        class="h-10 w-20 border border-gray-300 rounded">
                                    <input type="text" value="{{ old('accent_color', '#007bff') }}"
                                        class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="#007bff">
                                </div>
                                @error('accent_color')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Features -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Card Features</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center">
                                <input type="checkbox" name="include_photo" id="include_photo" value="1" 
                                    {{ old('include_photo', true) ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="include_photo" class="ml-2 block text-sm text-gray-900">
                                    Include Photo
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" name="include_qr_code" id="include_qr_code" value="1"
                                    {{ old('include_qr_code', true) ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="include_qr_code" class="ml-2 block text-sm text-gray-900">
                                    Include QR Code
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Preview -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Preview</h3>
                        <div class="bg-gray-100 p-4 rounded-lg">
                            <div id="card-preview" class="w-80 h-48 mx-auto bg-white border-2 border-gray-300 rounded-lg shadow-lg relative overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-br from-blue-500 to-blue-600 opacity-10"></div>
                                <div class="relative p-4 h-full flex flex-col justify-between">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="text-lg font-bold text-gray-800">John Doe</h4>
                                            <p class="text-sm text-gray-600">Speaker</p>
                                            <p class="text-xs text-gray-500">Tech Conference 2025</p>
                                        </div>
                                        <div class="w-16 h-16 bg-gray-300 rounded-full flex items-center justify-center">
                                            <span class="text-gray-500 text-xs">Photo</span>
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-end">
                                        <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
                                            <span class="text-gray-500 text-xs">QR</span>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs text-gray-500">ID: 12345</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('id-cards.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Cancel
                        </a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Create Template
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const conferenceField = document.getElementById('conference-field');
    const conferenceSelect = document.getElementById('conference_id');

    // Color picker synchronization
    const colorInputs = document.querySelectorAll('input[type="color"]');
    colorInputs.forEach(input => {
        const textInput = input.parentElement.querySelector('input[type="text"]');
        input.addEventListener('change', function() {
            textInput.value = this.value;
            updatePreview();
        });
        textInput.addEventListener('input', function() {
            input.value = this.value;
            updatePreview();
        });
    });

    // Type change handler
    typeSelect.addEventListener('change', function() {
        if (this.value === 'participant') {
            conferenceField.classList.remove('hidden');
            conferenceSelect.required = true;
        } else {
            conferenceField.classList.add('hidden');
            conferenceSelect.required = false;
            conferenceSelect.value = '';
        }
    });

    // Initial setup
    if (typeSelect.value === 'participant') {
        conferenceField.classList.remove('hidden');
        conferenceSelect.required = true;
    }

    function updatePreview() {
        const backgroundColor = document.getElementById('background_color').value;
        const textColor = document.getElementById('text_color').value;
        const accentColor = document.getElementById('accent_color').value;
        
        const preview = document.getElementById('card-preview');
        preview.style.backgroundColor = backgroundColor;
        
        // Update text colors
        const textElements = preview.querySelectorAll('h4, p');
        textElements.forEach(element => {
            element.style.color = textColor;
        });
    }

    // Initial preview update
    updatePreview();
});
</script>
@endsection
