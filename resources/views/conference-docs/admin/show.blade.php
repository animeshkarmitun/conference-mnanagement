@extends('layouts.app')

@section('title', 'Conference Doc Details')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Conference Doc Details</h1>
                    <p class="text-gray-600 mt-1">{{ $conferenceDoc->conference->name }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('conference-docs.edit', $conferenceDoc) }}" 
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
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

    <!-- Conference Info -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Conference Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Conference Name</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $conferenceDoc->conference->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Start Date</label>
                    <p class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($conferenceDoc->conference->start_date)->format('M d, Y') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Created</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $conferenceDoc->created_at->format('M d, Y H:i') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Total Items</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $conferenceDoc->conferenceDocItems->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Session Links -->
    @if($sessionLinks->count() > 0)
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Session Links ({{ $sessionLinks->count() }})</h2>
                <div class="space-y-4">
                    @foreach($sessionLinks as $sessionLink)
                        @php $content = json_decode($sessionLink->content, true); @endphp
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $content['title'] ?? 'Session' }}</h3>
                                    <p class="text-sm text-gray-600">{{ $content['time'] ?? 'Time TBD' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600"><strong>Room:</strong> {{ $content['room'] ?? 'Room TBD' }}</p>
                                    <p class="text-sm text-gray-600"><strong>Speaker:</strong> {{ $content['speaker'] ?? 'TBD' }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <p class="text-sm text-gray-700">{{ $content['description'] ?? 'No description available.' }}</p>
                                    @if(isset($content['zoom_link']) && $content['zoom_link'])
                                        <p class="text-sm text-blue-600 mt-2">
                                            <strong>Zoom Link:</strong> 
                                            <a href="{{ $content['zoom_link'] }}" target="_blank" class="underline">{{ $content['zoom_link'] }}</a>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Contacts -->
    @if($contacts->count() > 0)
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Important Contacts ({{ $contacts->count() }})</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($contacts as $contact)
                        @php $content = json_decode($contact->content, true); @endphp
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="font-medium text-gray-900">{{ $content['name'] ?? 'Contact' }}</h3>
                            <p class="text-sm text-gray-600">{{ $content['role'] ?? '' }}</p>
                            <div class="mt-2 space-y-1">
                                @if(isset($content['email']) && $content['email'])
                                    <p class="text-sm text-gray-600">
                                        <strong>Email:</strong> 
                                        <a href="mailto:{{ $content['email'] }}" class="text-blue-600">{{ $content['email'] }}</a>
                                    </p>
                                @endif
                                @if(isset($content['phone']) && $content['phone'])
                                    <p class="text-sm text-gray-600">
                                        <strong>Phone:</strong> 
                                        <a href="tel:{{ $content['phone'] }}" class="text-blue-600">{{ $content['phone'] }}</a>
                                    </p>
                                @endif
                                @if(isset($content['availability']) && $content['availability'])
                                    <p class="text-sm text-gray-600">
                                        <strong>Availability:</strong> {{ $content['availability'] }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- City Guide -->
    @if($cityGuide->count() > 0)
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">City Guide</h2>
                @foreach($cityGuide as $guide)
                    @php $content = json_decode($guide->content, true); @endphp
                    <div class="space-y-4">
                        <div>
                            <h3 class="font-medium text-gray-900">{{ $content['title'] ?? 'City Guide' }}</h3>
                            <p class="text-sm text-gray-700 mt-2">{{ $content['description'] ?? 'No description available.' }}</p>
                        </div>
                        
                        @if(isset($content['transportation']) && $content['transportation'])
                            <div>
                                <h4 class="font-medium text-gray-900">Transportation</h4>
                                <p class="text-sm text-gray-700">{{ $content['transportation'] }}</p>
                            </div>
                        @endif

                        @if(isset($content['restaurants']) && $content['restaurants'])
                            <div>
                                <h4 class="font-medium text-gray-900">Dining Options</h4>
                                <p class="text-sm text-gray-700">{{ $content['restaurants'] }}</p>
                            </div>
                        @endif

                        @if(isset($content['attractions']) && $content['attractions'])
                            <div>
                                <h4 class="font-medium text-gray-900">Local Attractions</h4>
                                <p class="text-sm text-gray-700">{{ $content['attractions'] }}</p>
                            </div>
                        @endif

                        @if(isset($content['emergency_contacts']) && $content['emergency_contacts'])
                            <div>
                                <h4 class="font-medium text-gray-900">Emergency Contacts</h4>
                                <p class="text-sm text-gray-700">{{ $content['emergency_contacts'] }}</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Upload Media Files -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Upload Media Files</h2>
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
                    <textarea name="description" id="description" rows="3"
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

    <!-- Media Files -->
    @if($mediaFiles->count() > 0)
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Media Files ({{ $mediaFiles->count() }})</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($mediaFiles as $mediaFile)
                        @php $content = json_decode($mediaFile->content, true); @endphp
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1">
                                    <h3 class="font-medium text-gray-900">{{ $mediaFile->file_name }}</h3>
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
                            <p class="text-xs text-gray-500">
                                Uploaded by {{ $mediaFile->uploader->name ?? 'Admin' }} • {{ $mediaFile->created_at->format('M d, Y') }}
                            </p>
                            <div class="mt-3 flex items-center justify-between">
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
        </div>
    @endif

    <!-- Actions -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Actions</h3>
                    <p class="text-sm text-gray-600">Manage this conference doc</p>
                </div>
                <div class="flex items-center space-x-3">
                    <form action="{{ route('conference-docs.destroy', $conferenceDoc) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200"
                                onclick="return confirm('Are you sure you want to delete this conference doc? This action cannot be undone.')">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
