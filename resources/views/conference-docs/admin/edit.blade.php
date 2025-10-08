@extends('layouts.app')

@section('title', 'Edit Conference Doc')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Conference Doc</h1>
                    <p class="text-gray-600 mt-1">{{ $conferenceDoc->conference->name }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('conference-docs.show', $conferenceDoc) }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        View
                    </a>
                    <a href="{{ route('conference-docs.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <form action="{{ route('conference-docs.update', $conferenceDoc) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="_method" value="PUT">
                
                <!-- Display general errors -->
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    There were some problems with your submission:
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- Conference Selection -->
                <div class="mb-6">
                    <label for="conference_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Conference <span class="text-red-500">*</span>
                    </label>
                    <select name="conference_id" id="conference_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select a conference</option>
                        @foreach($conferences as $conference)
                            <option value="{{ $conference->id }}" {{ (old('conference_id', $conferenceDoc->conference_id) == $conference->id) ? 'selected' : '' }}>
                                {{ $conference->name }} ({{ \Carbon\Carbon::parse($conference->start_date)->format('M d, Y') }})
                            </option>
                        @endforeach
                    </select>
                    @error('conference_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Session Links Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Session Links <span class="text-gray-500 text-sm font-normal">(Optional)</span></h3>
                    <div id="session-links-container">
                        @forelse($sessionLinks as $index => $sessionLink)
                            @php $content = json_decode($sessionLink->content, true); @endphp
                            <div class="session-link-item border border-gray-200 rounded-lg p-4 mb-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Session Title <span class="text-gray-500 text-sm font-normal">(Optional)</span></label>
                                        <input type="text" name="session_links[{{ $index }}][title]" 
                                               value="{{ old('session_links.'.$index.'.title', $content['title'] ?? '') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Enter session title">
                                        @error('session_links.'.$index.'.title')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Time <span class="text-gray-500 text-sm font-normal">(Optional)</span></label>
                                        <input type="text" name="session_links[{{ $index }}][time]" 
                                               value="{{ old('session_links.'.$index.'.time', $content['time'] ?? '') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="e.g., 9:00 AM - 10:00 AM">
                                        @error('session_links.'.$index.'.time')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Room <span class="text-gray-500 text-sm font-normal">(Optional)</span></label>
                                        <input type="text" name="session_links[{{ $index }}][room]" 
                                               value="{{ old('session_links.'.$index.'.room', $content['room'] ?? '') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Enter room name">
                                        @error('session_links.'.$index.'.room')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Speaker <span class="text-gray-500 text-sm font-normal">(Optional)</span></label>
                                        <input type="text" name="session_links[{{ $index }}][speaker]" 
                                               value="{{ old('session_links.'.$index.'.speaker', $content['speaker'] ?? '') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Enter speaker name">
                                        @error('session_links.'.$index.'.speaker')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-gray-500 text-sm font-normal">(Optional)</span></label>
                                        <textarea name="session_links[{{ $index }}][description]" rows="3"
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                                  placeholder="Enter session description">{{ old('session_links.'.$index.'.description', $content['description'] ?? '') }}</textarea>
                                        @error('session_links.'.$index.'.description')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Zoom Link (Optional)</label>
                                        <input type="url" name="session_links[{{ $index }}][zoom_link]" 
                                               value="{{ old('session_links.'.$index.'.zoom_link', $content['zoom_link'] ?? '') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="https://zoom.us/j/...">
                                        @error('session_links.'.$index.'.zoom_link')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <button type="button" class="mt-2 text-red-600 hover:text-red-800 remove-session-link">
                                    Remove Session Link
                                </button>
                            </div>
                        @empty
                            <div class="session-link-item border border-gray-200 rounded-lg p-4 mb-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Session Title <span class="text-gray-500 text-sm font-normal">(Optional)</span></label>
                                        <input type="text" name="session_links[0][title]" 
                                               value="{{ old('session_links.0.title') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Enter session title">
                                        @error('session_links.0.title')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Time <span class="text-gray-500 text-sm font-normal">(Optional)</span></label>
                                        <input type="text" name="session_links[0][time]" 
                                               value="{{ old('session_links.0.time') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="e.g., 9:00 AM - 10:00 AM">
                                        @error('session_links.0.time')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Room <span class="text-gray-500 text-sm font-normal">(Optional)</span></label>
                                        <input type="text" name="session_links[0][room]" 
                                               value="{{ old('session_links.0.room') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Enter room name">
                                        @error('session_links.0.room')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Speaker <span class="text-gray-500 text-sm font-normal">(Optional)</span></label>
                                        <input type="text" name="session_links[0][speaker]" 
                                               value="{{ old('session_links.0.speaker') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Enter speaker name">
                                        @error('session_links.0.speaker')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-gray-500 text-sm font-normal">(Optional)</span></label>
                                        <textarea name="session_links[0][description]" rows="3"
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                                  placeholder="Enter session description">{{ old('session_links.0.description') }}</textarea>
                                        @error('session_links.0.description')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Zoom Link (Optional)</label>
                                        <input type="url" name="session_links[0][zoom_link]" 
                                               value="{{ old('session_links.0.zoom_link') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="https://zoom.us/j/...">
                                        @error('session_links.0.zoom_link')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>
                    <button type="button" id="add-session-link" 
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Session Link
                    </button>
                </div>

                <!-- Contacts Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Important Contacts <span class="text-gray-500 text-sm font-normal">(Optional)</span></h3>
                    <div id="contacts-container">
                        @forelse($contacts as $index => $contact)
                            @php $content = json_decode($contact->content, true); @endphp
                            <div class="contact-item border border-gray-200 rounded-lg p-4 mb-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-gray-500 text-sm font-normal">(Optional)</span></label>
                                        <input type="text" name="contacts[{{ $index }}][name]" 
                                               value="{{ old('contacts.'.$index.'.name', $content['name'] ?? '') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Enter contact name">
                                        @error('contacts.'.$index.'.name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-gray-500 text-sm font-normal">(Optional)</span></label>
                                        <input type="text" name="contacts[{{ $index }}][role]" 
                                               value="{{ old('contacts.'.$index.'.role', $content['role'] ?? '') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="e.g., Conference Coordinator">
                                        @error('contacts.'.$index.'.role')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-gray-500 text-sm font-normal">(Optional)</span></label>
                                        <input type="email" name="contacts[{{ $index }}][email]" 
                                               value="{{ old('contacts.'.$index.'.email', $content['email'] ?? '') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Enter email address">
                                        @error('contacts.'.$index.'.email')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone <span class="text-gray-500 text-sm font-normal">(Optional)</span></label>
                                        <input type="tel" name="contacts[{{ $index }}][phone]" 
                                               value="{{ old('contacts.'.$index.'.phone', $content['phone'] ?? '') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Enter phone number">
                                        @error('contacts.'.$index.'.phone')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Availability <span class="text-gray-500 text-sm font-normal">(Optional)</span></label>
                                        <input type="text" name="contacts[{{ $index }}][availability]" 
                                               value="{{ old('contacts.'.$index.'.availability', $content['availability'] ?? '') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="e.g., 9 AM - 5 PM, Monday to Friday">
                                        @error('contacts.'.$index.'.availability')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <button type="button" class="mt-2 text-red-600 hover:text-red-800 remove-contact">
                                    Remove Contact
                                </button>
                            </div>
                        @empty
                            <div class="contact-item border border-gray-200 rounded-lg p-4 mb-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-gray-500 text-sm font-normal">(Optional)</span></label>
                                        <input type="text" name="contacts[0][name]" 
                                               value="{{ old('contacts.0.name') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Enter contact name">
                                        @error('contacts.0.name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-gray-500 text-sm font-normal">(Optional)</span></label>
                                        <input type="text" name="contacts[0][role]" 
                                               value="{{ old('contacts.0.role') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="e.g., Conference Coordinator">
                                        @error('contacts.0.role')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-gray-500 text-sm font-normal">(Optional)</span></label>
                                        <input type="email" name="contacts[0][email]" 
                                               value="{{ old('contacts.0.email') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Enter email address">
                                        @error('contacts.0.email')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone <span class="text-gray-500 text-sm font-normal">(Optional)</span></label>
                                        <input type="tel" name="contacts[0][phone]" 
                                               value="{{ old('contacts.0.phone') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Enter phone number">
                                        @error('contacts.0.phone')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Availability <span class="text-gray-500 text-sm font-normal">(Optional)</span></label>
                                        <input type="text" name="contacts[0][availability]" 
                                               value="{{ old('contacts.0.availability') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="e.g., 9 AM - 5 PM, Monday to Friday">
                                        @error('contacts.0.availability')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>
                    <button type="button" id="add-contact" 
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Contact
                    </button>
                </div>

                <!-- City Guide Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">City Guide <span class="text-gray-500 text-sm font-normal">(Optional)</span></h3>
                    <div class="border border-gray-200 rounded-lg p-4">
                        @php 
                            $cityGuideContent = $cityGuide ? json_decode($cityGuide->content, true) : [];
                        @endphp
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-gray-500 text-sm font-normal">(Optional)</span></label>
                                <input type="text" name="city_guide[title]" 
                                       value="{{ old('city_guide.title', $cityGuideContent['title'] ?? '') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="e.g., Welcome to New York City">
                                @error('city_guide.title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-gray-500 text-sm font-normal">(Optional)</span></label>
                                <textarea name="city_guide[description]" rows="4"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="Enter city guide description">{{ old('city_guide.description', $cityGuideContent['description'] ?? '') }}</textarea>
                                @error('city_guide.description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Transportation <span class="text-gray-500 text-sm font-normal">(Optional)</span></label>
                                <textarea name="city_guide[transportation]" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="Enter transportation information">{{ old('city_guide.transportation', $cityGuideContent['transportation'] ?? '') }}</textarea>
                                @error('city_guide.transportation')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Restaurants <span class="text-gray-500 text-sm font-normal">(Optional)</span></label>
                                <textarea name="city_guide[restaurants]" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="Enter restaurant recommendations">{{ old('city_guide.restaurants', $cityGuideContent['restaurants'] ?? '') }}</textarea>
                                @error('city_guide.restaurants')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Attractions <span class="text-gray-500 text-sm font-normal">(Optional)</span></label>
                                <textarea name="city_guide[attractions]" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="Enter local attractions">{{ old('city_guide.attractions', $cityGuideContent['attractions'] ?? '') }}</textarea>
                                @error('city_guide.attractions')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Contacts <span class="text-gray-500 text-sm font-normal">(Optional)</span></label>
                                <textarea name="city_guide[emergency_contacts]" rows="2"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="Enter emergency contact information">{{ old('city_guide.emergency_contacts', $cityGuideContent['emergency_contacts'] ?? '') }}</textarea>
                                @error('city_guide.emergency_contacts')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upload Media Files Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Upload Media Files <span class="text-gray-500 text-sm font-normal">(Optional)</span></h3>
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <div class="space-y-4">
                            <div>
                                <label for="files" class="block text-sm font-medium text-gray-700 mb-2">
                                    Select Files <span class="text-gray-500 text-sm font-normal">(Optional)</span>
                                </label>
                                <input type="file" name="files[]" id="files" multiple 
                                       accept=".pdf,.doc,.docx,.txt,.rtf,.jpg,.jpeg,.png,.gif,.mp4,.avi,.mov,.mp3,.wav"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <p class="mt-1 text-sm text-gray-500">Supported formats: PDF, DOC, DOCX, TXT, RTF, JPG, PNG, GIF, MP4, AVI, MOV, MP3, WAV (Max 10MB per file)</p>
                            </div>
                            <div>
                                <label for="file_description" class="block text-sm font-medium text-gray-700 mb-2">Description (Optional)</label>
                                <textarea name="file_description" id="file_description" rows="2"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="Enter a description for the uploaded files">{{ old('file_description') }}</textarea>
                                @error('file_description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Current Media Files -->
                @if($mediaFiles->count() > 0)
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Current Media Files ({{ $mediaFiles->count() }})</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($mediaFiles as $mediaFile)
                                @php $content = json_decode($mediaFile->content, true); @endphp
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900">{{ $mediaFile->file_name }}</h4>
                                            <p class="text-sm text-gray-600">{{ $mediaFile->getFileSizeFormatted() }}</p>
                                        </div>
                                        <div class="ml-2">
                                            @if($mediaFile->isImage())
                                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            @elseif($mediaFile->isDocument())
                                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            @elseif($mediaFile->isVideo())
                                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                </svg>
                                            @else
                                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                </svg>
                                            @endif
                                        </div>
                                    </div>
                                    @if(isset($content['description']) && $content['description'])
                                        <p class="text-sm text-gray-700 mb-2">{{ $content['description'] }}</p>
                                    @endif
                                    <p class="text-xs text-gray-500 mb-3">
                                        Uploaded by {{ $mediaFile->uploader->name ?? 'Admin' }} • {{ $mediaFile->created_at->format('M d, Y') }}
                                    </p>
                                    <div class="flex items-center justify-between">
                                        <a href="{{ route('conference-docs.media.download', [$conferenceDoc, $mediaFile]) }}" 
                                           class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors duration-200">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Download
                                        </a>
                                        <button type="button" 
                                                class="inline-flex items-center px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition-colors duration-200 remove-media-btn"
                                                data-file-id="{{ $mediaFile->id }}"
                                                data-file-name="{{ $mediaFile->file_name }}"
                                                onclick="removeMediaFile({{ $mediaFile->id }}, '{{ $mediaFile->file_name }}')">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Submit Button -->
                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('conference-docs.show', $conferenceDoc) }}" 
                       class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200">
                        Update Conference Doc
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// AJAX function to remove media file
function removeMediaFile(fileId, fileName) {
    if (confirm('Are you sure you want to remove this file? This action cannot be undone.')) {
        // Show loading state
        const button = event.target.closest('.remove-media-btn');
        const originalText = button.innerHTML;
        button.innerHTML = '<svg class="w-4 h-4 mr-1 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Removing...';
        button.disabled = true;
        
        // Make AJAX request
        fetch(`/conference-docs/{{ $conferenceDoc->id }}/media/${fileId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the file element from the page
                button.closest('.border').remove();
                
                // Update the count
                const countElement = document.querySelector('h3');
                if (countElement) {
                    const currentCount = parseInt(countElement.textContent.match(/\d+/)[0]);
                    const newCount = currentCount - 1;
                    countElement.textContent = countElement.textContent.replace(/\d+/, newCount);
                }
            } else {
                alert('Error removing file: ' + (data.message || 'Unknown error'));
                button.innerHTML = originalText;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error removing file. Please try again.');
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    let sessionLinkIndex = {{ $sessionLinks->count() }};
    let contactIndex = {{ $contacts->count() }};

    // Add Session Link
    document.getElementById('add-session-link').addEventListener('click', function() {
        const container = document.getElementById('session-links-container');
        const newItem = document.createElement('div');
        newItem.className = 'session-link-item border border-gray-200 rounded-lg p-4 mb-4';
        newItem.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Session Title</label>
                    <input type="text" name="session_links[${sessionLinkIndex}][title]" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Enter session title">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Time</label>
                    <input type="text" name="session_links[${sessionLinkIndex}][time]" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g., 9:00 AM - 10:00 AM">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Room</label>
                    <input type="text" name="session_links[${sessionLinkIndex}][room]" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Enter room name">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Speaker</label>
                    <input type="text" name="session_links[${sessionLinkIndex}][speaker]" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Enter speaker name">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="session_links[${sessionLinkIndex}][description]" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Enter session description"></textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Zoom Link (Optional)</label>
                    <input type="url" name="session_links[${sessionLinkIndex}][zoom_link]" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="https://zoom.us/j/...">
                </div>
            </div>
            <button type="button" class="mt-2 text-red-600 hover:text-red-800 remove-session-link">
                Remove Session Link
            </button>
        `;
        container.appendChild(newItem);
        sessionLinkIndex++;
    });

    // Add Contact
    document.getElementById('add-contact').addEventListener('click', function() {
        const container = document.getElementById('contacts-container');
        const newItem = document.createElement('div');
        newItem.className = 'contact-item border border-gray-200 rounded-lg p-4 mb-4';
        newItem.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" name="contacts[${contactIndex}][name]" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Enter contact name">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <input type="text" name="contacts[${contactIndex}][role]" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g., Conference Coordinator">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="contacts[${contactIndex}][email]" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Enter email address">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="tel" name="contacts[${contactIndex}][phone]" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Enter phone number">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Availability</label>
                    <input type="text" name="contacts[${contactIndex}][availability]" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="e.g., 9 AM - 5 PM, Monday to Friday">
                </div>
            </div>
            <button type="button" class="mt-2 text-red-600 hover:text-red-800 remove-contact">
                Remove Contact
            </button>
        `;
        container.appendChild(newItem);
        contactIndex++;
    });

    // Remove Session Link and Contact
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-session-link')) {
            e.target.closest('.session-link-item').remove();
        }
        if (e.target.classList.contains('remove-contact')) {
            e.target.closest('.contact-item').remove();
        }
    });
});
</script>
@endsection
