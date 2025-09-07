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
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Session Links</h3>
                    <div id="session-links-container">
                        @forelse($sessionLinks as $index => $sessionLink)
                            @php $content = json_decode($sessionLink->content, true); @endphp
                            <div class="session-link-item border border-gray-200 rounded-lg p-4 mb-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Session Title</label>
                                        <input type="text" name="session_links[{{ $index }}][title]" 
                                               value="{{ old('session_links.'.$index.'.title', $content['title'] ?? '') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Enter session title">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Time</label>
                                        <input type="text" name="session_links[{{ $index }}][time]" 
                                               value="{{ old('session_links.'.$index.'.time', $content['time'] ?? '') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="e.g., 9:00 AM - 10:00 AM">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Room</label>
                                        <input type="text" name="session_links[{{ $index }}][room]" 
                                               value="{{ old('session_links.'.$index.'.room', $content['room'] ?? '') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Enter room name">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Speaker</label>
                                        <input type="text" name="session_links[{{ $index }}][speaker]" 
                                               value="{{ old('session_links.'.$index.'.speaker', $content['speaker'] ?? '') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Enter speaker name">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                        <textarea name="session_links[{{ $index }}][description]" rows="3"
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                                  placeholder="Enter session description">{{ old('session_links.'.$index.'.description', $content['description'] ?? '') }}</textarea>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Zoom Link (Optional)</label>
                                        <input type="url" name="session_links[{{ $index }}][zoom_link]" 
                                               value="{{ old('session_links.'.$index.'.zoom_link', $content['zoom_link'] ?? '') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="https://zoom.us/j/...">
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
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Session Title</label>
                                        <input type="text" name="session_links[0][title]" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Enter session title">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Time</label>
                                        <input type="text" name="session_links[0][time]" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="e.g., 9:00 AM - 10:00 AM">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Room</label>
                                        <input type="text" name="session_links[0][room]" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Enter room name">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Speaker</label>
                                        <input type="text" name="session_links[0][speaker]" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Enter speaker name">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                        <textarea name="session_links[0][description]" rows="3"
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                                  placeholder="Enter session description"></textarea>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Zoom Link (Optional)</label>
                                        <input type="url" name="session_links[0][zoom_link]" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="https://zoom.us/j/...">
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
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Important Contacts</h3>
                    <div id="contacts-container">
                        @forelse($contacts as $index => $contact)
                            @php $content = json_decode($contact->content, true); @endphp
                            <div class="contact-item border border-gray-200 rounded-lg p-4 mb-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                        <input type="text" name="contacts[{{ $index }}][name]" 
                                               value="{{ old('contacts.'.$index.'.name', $content['name'] ?? '') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Enter contact name">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                                        <input type="text" name="contacts[{{ $index }}][role]" 
                                               value="{{ old('contacts.'.$index.'.role', $content['role'] ?? '') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="e.g., Conference Coordinator">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                        <input type="email" name="contacts[{{ $index }}][email]" 
                                               value="{{ old('contacts.'.$index.'.email', $content['email'] ?? '') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Enter email address">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                        <input type="tel" name="contacts[{{ $index }}][phone]" 
                                               value="{{ old('contacts.'.$index.'.phone', $content['phone'] ?? '') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Enter phone number">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Availability</label>
                                        <input type="text" name="contacts[{{ $index }}][availability]" 
                                               value="{{ old('contacts.'.$index.'.availability', $content['availability'] ?? '') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="e.g., 9 AM - 5 PM, Monday to Friday">
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
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                        <input type="text" name="contacts[0][name]" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Enter contact name">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                                        <input type="text" name="contacts[0][role]" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="e.g., Conference Coordinator">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                        <input type="email" name="contacts[0][email]" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Enter email address">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                        <input type="tel" name="contacts[0][phone]" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Enter phone number">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Availability</label>
                                        <input type="text" name="contacts[0][availability]" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="e.g., 9 AM - 5 PM, Monday to Friday">
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
                    <h3 class="text-lg font-medium text-gray-900 mb-4">City Guide</h3>
                    <div class="border border-gray-200 rounded-lg p-4">
                        @php 
                            $cityGuideContent = $cityGuide ? json_decode($cityGuide->content, true) : [];
                        @endphp
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                                <input type="text" name="city_guide[title]" 
                                       value="{{ old('city_guide.title', $cityGuideContent['title'] ?? '') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="e.g., Welcome to New York City">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea name="city_guide[description]" rows="4"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="Enter city guide description">{{ old('city_guide.description', $cityGuideContent['description'] ?? '') }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Transportation</label>
                                <textarea name="city_guide[transportation]" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="Enter transportation information">{{ old('city_guide.transportation', $cityGuideContent['transportation'] ?? '') }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Restaurants</label>
                                <textarea name="city_guide[restaurants]" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="Enter restaurant recommendations">{{ old('city_guide.restaurants', $cityGuideContent['restaurants'] ?? '') }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Attractions</label>
                                <textarea name="city_guide[attractions]" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="Enter local attractions">{{ old('city_guide.attractions', $cityGuideContent['attractions'] ?? '') }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Contacts</label>
                                <textarea name="city_guide[emergency_contacts]" rows="2"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="Enter emergency contact information">{{ old('city_guide.emergency_contacts', $cityGuideContent['emergency_contacts'] ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upload Media Files Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Upload Media Files</h3>
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <form action="{{ route('conference-docs.media.upload', $conferenceDoc) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div>
                                <label for="files" class="block text-sm font-medium text-gray-700 mb-2">
                                    Select Files <span class="text-red-500">*</span>
                                </label>
                                <input type="file" name="files[]" id="files" multiple 
                                       accept=".pdf,.doc,.docx,.txt,.rtf,.jpg,.jpeg,.png,.gif,.mp4,.avi,.mov,.mp3,.wav"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <p class="mt-1 text-sm text-gray-500">Supported formats: PDF, DOC, DOCX, TXT, RTF, JPG, PNG, GIF, MP4, AVI, MOV, MP3, WAV (Max 10MB per file)</p>
                            </div>
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description (Optional)</label>
                                <textarea name="description" id="description" rows="2"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="Enter a description for the uploaded files"></textarea>
                            </div>
                            <div>
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    Upload Files
                                </button>
                            </div>
                        </form>
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
                                        <form action="{{ route('conference-docs.media.delete', [$conferenceDoc, $mediaFile]) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="inline-flex items-center px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition-colors duration-200"
                                                    onclick="return confirm('Are you sure you want to delete this file? This action cannot be undone.')">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Delete
                                            </button>
                                        </form>
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

    // Remove Session Link
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
