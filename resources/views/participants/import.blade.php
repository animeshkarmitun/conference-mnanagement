@extends('layouts.app')

@section('title', 'Bulk Import Participants')

@section('content')
<!-- Professional Page Header -->
<div class="rounded-2xl bg-gradient-to-r from-purple-100 via-purple-50 to-white shadow flex items-center px-8 py-6 mb-6 border border-purple-200">
    <div class="flex items-center justify-center w-16 h-16 bg-purple-200 rounded-full mr-6 shadow">
        <svg class="w-8 h-8 text-purple-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
        </svg>
    </div>
    <div>
        <h1 class="text-3xl font-extrabold text-purple-800 tracking-tight mb-1">Bulk Import Participants</h1>
        <div class="text-gray-600 text-lg font-medium">Upload a CSV file to add multiple participants at once</div>
    </div>
</div>

<!-- Display success/error messages -->
@if(session('success'))
<div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-green-800">Success</h3>
            <div class="mt-2 text-sm text-green-700">
                <p>{{ session('success') }}</p>
            </div>
        </div>
    </div>
</div>
@endif

@if(session('error'))
<div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-red-800">Error</h3>
            <div class="mt-2 text-sm text-red-700">
                <p style="white-space: pre-line;">{{ session('error') }}</p>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Instructions Card -->
<div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-6">
    <h3 class="text-lg font-semibold mb-4 text-blue-800 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        How to Use Bulk Import
    </h3>
    <div class="space-y-3 text-sm text-blue-900">
        <div class="flex items-start">
            <span class="flex-shrink-0 w-6 h-6 bg-blue-200 rounded-full flex items-center justify-center text-blue-800 font-bold mr-3">1</span>
            <div>
                <strong>Download the sample template:</strong> Click the "Download Sample CSV" button below to get the correctly formatted template with example data and instructions.
            </div>
        </div>
        <div class="flex items-start">
            <span class="flex-shrink-0 w-6 h-6 bg-blue-200 rounded-full flex items-center justify-center text-blue-800 font-bold mr-3">2</span>
            <div>
                <strong>Fill in your data:</strong> Open the CSV file in Excel or Google Sheets, remove the sample data, and add your participant information. Keep the header row intact.
            </div>
        </div>
        <div class="flex items-start">
            <span class="flex-shrink-0 w-6 h-6 bg-blue-200 rounded-full flex items-center justify-center text-blue-800 font-bold mr-3">3</span>
            <div>
                <strong>Select a conference:</strong> Choose which conference these participants are registering for.
            </div>
        </div>
        <div class="flex items-start">
            <span class="flex-shrink-0 w-6 h-6 bg-blue-200 rounded-full flex items-center justify-center text-blue-800 font-bold mr-3">4</span>
            <div>
                <strong>Upload your file:</strong> Select your completed CSV file and click "Import Participants".
            </div>
        </div>
    </div>
</div>

<!-- Main Import Form -->
<div class="bg-white rounded-xl shadow p-6">
    <!-- Download Sample Section -->
    <div class="mb-8 p-6 bg-gradient-to-r from-yellow-50 to-orange-50 rounded-lg border border-yellow-200">
        <h3 class="text-lg font-semibold mb-3 text-yellow-800">Step 1: Download Sample Template</h3>
        <p class="text-sm text-gray-700 mb-4">
            Download the CSV template file to see the required format and field descriptions. The template includes sample data and detailed instructions.
        </p>
        <a href="{{ route('participants.import.sample') }}" 
           class="inline-flex items-center px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Download Sample CSV Template
        </a>
    </div>

    <!-- Upload Form -->
    <form method="POST" action="{{ route('participants.import.process') }}" enctype="multipart/form-data">
        @csrf
        
        <!-- Conference Selection -->
        <div class="mb-8 p-6 bg-purple-50 rounded-lg border border-purple-200">
            <h3 class="text-lg font-semibold mb-4 text-purple-800">Step 2: Select Conference</h3>
            <div class="mb-4">
                <label for="conference_id" class="block text-sm font-medium text-gray-700 mb-2">Conference *</label>
                <select name="conference_id" id="conference_id" required 
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                    <option value="">Select a conference</option>
                    @foreach($conferences as $conference)
                        <option value="{{ $conference->id }}" {{ old('conference_id') == $conference->id ? 'selected' : '' }}>
                            {{ $conference->name }} 
                            @if($conference->start_date && $conference->end_date)
                                ({{ \Carbon\Carbon::parse($conference->start_date)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($conference->end_date)->format('M d, Y') }})
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('conference_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- File Upload -->
        <div class="mb-8 p-6 bg-green-50 rounded-lg border border-green-200">
            <h3 class="text-lg font-semibold mb-4 text-green-800">Step 3: Upload Your CSV File</h3>
            <div class="mb-4">
                <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-2">CSV File *</label>
                <input type="file" 
                       name="csv_file" 
                       id="csv_file" 
                       accept=".csv" 
                       required
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-6 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-500 file:text-white hover:file:bg-green-600 cursor-pointer">
                <p class="text-xs text-gray-500 mt-2">
                    <strong>Supported format:</strong> CSV (.csv) files only, maximum size 10MB
                </p>
                @error('csv_file')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Required Fields Info -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <h4 class="text-sm font-semibold text-gray-700 mb-2">Required Fields in CSV:</h4>
            <ul class="text-sm text-gray-600 space-y-1 ml-5 list-disc">
                <li><strong>first_name:</strong> Participant's first name</li>
                <li><strong>last_name:</strong> Participant's last name</li>
                <li><strong>email:</strong> Unique email address (used to identify or create user account)</li>
                <li><strong>participant_type:</strong> Must match one of: 
                    @foreach($participantTypes as $type)
                        {{ strtolower($type->name) }}{{ !$loop->last ? ', ' : '' }}
                    @endforeach
                </li>
            </ul>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
            <a href="{{ route('participants.index') }}" 
               class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200 font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Cancel
            </a>
            <button type="submit" 
                    class="inline-flex items-center px-8 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-bold shadow-lg hover:shadow-xl transition-all duration-200"
                    style="background-color: #9333ea !important; color: white !important; font-weight: bold !important; min-width: 200px;">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: white !important;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                <span style="color: white !important; font-weight: bold !important; font-size: 16px !important;">Import Participants</span>
            </button>
        </div>
    </form>
</div>

<!-- Important Notes -->
<div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-6">
    <h3 class="text-lg font-semibold mb-4 text-yellow-800 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
        Important Notes
    </h3>
    <ul class="text-sm text-yellow-900 space-y-2 ml-5 list-disc">
        <li><strong>Email Uniqueness:</strong> If an email already exists in the system, a new participant profile will be created for that user for the selected conference.</li>
        <li><strong>Validation:</strong> All rows must pass validation. If any row fails, the entire import will be rolled back to maintain data integrity.</li>
        <li><strong>Passwords:</strong> Auto-generated passwords will be created for new user accounts. Users can reset their password via email.</li>
        <li><strong>Data Format:</strong> Ensure dates are in YYYY-MM-DD format and datetime fields are in YYYY-MM-DD HH:MM format.</li>
        <li><strong>Participant Types:</strong> Type names must match exactly (case-insensitive) with existing participant types in the system.</li>
        <li><strong>Travel Details:</strong> If travel_intent is set to "national" or "international", both arrival_date and departure_date are required.</li>
    </ul>
</div>

@endsection


