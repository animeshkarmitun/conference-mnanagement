<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Participant Profile - {{ $participant->user->first_name ?? $participant->user->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6366f1',
                        secondary: '#8b5cf6',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Top Navigation Bar -->
    <nav class="bg-white shadow-lg border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo/Brand -->
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-xl font-bold text-gray-900">CGS Events</h1>
                    </div>
                </div>

                <!-- Profile Switcher Dropdown -->
                <div class="relative">
                    <button id="profileDropdownBtn" class="flex items-center space-x-2 bg-blue-50 hover:bg-blue-100 px-4 py-2 rounded-lg transition-colors duration-200">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-white text-sm"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-700">{{ $participant->user->first_name ?? $participant->user->name }}</span>
                        <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                        @if(isset($allParticipantProfiles) && count($allParticipantProfiles) > 1)
                            @foreach($allParticipantProfiles as $profile)
                                <a href="{{ route('my-profile.switch', $profile) }}" 
                                   class="flex items-center px-4 py-3 hover:bg-gray-50 {{ $profile->id === $participant->id ? 'bg-blue-50 border-r-2 border-blue-500' : '' }}">
                                    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-gray-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $profile->user->first_name ?? $profile->user->name }} {{ $profile->user->last_name ?? '' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ ucwords(str_replace('_', ' ', $profile->participantType->name ?? '')) }} - {{ $profile->conference->name ?? 'No Conference' }}
                                        </div>
                                    </div>
                                    @if($profile->id === $participant->id)
                                        <i class="fas fa-check text-blue-500"></i>
                                    @endif
                                </a>
                            @endforeach
                        @else
                            <div class="px-4 py-3 text-sm text-gray-500">No other profiles available</div>
                        @endif
                    </div>
                </div>

                <!-- Notifications -->
                <div class="flex items-center space-x-4">
                    <button onclick="openNotifications()" class="relative p-2 text-gray-400 hover:text-gray-600 transition-colors duration-200">
                        <i class="fas fa-bell text-xl"></i>
                        @if(isset($unreadNotifications) && $unreadNotifications > 0)
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">{{ $unreadNotifications }}</span>
                        @endif
                    </button>
                    
                    <!-- Mobile Menu Button -->
                    <button id="mobileMenuBtn" class="md:hidden p-2 text-gray-400 hover:text-gray-600">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        
        @if(session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif
        <!-- Profile Header -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 px-6 py-8">
                <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-4 sm:space-y-0 sm:space-x-6">
                    <!-- Profile Picture -->
                    <div class="relative">
                        @if($participant->user->profile_picture)
                            <img src="{{ asset('storage/' . $participant->user->profile_picture) }}" 
                                 alt="Profile Picture" 
                                 class="w-24 h-24 sm:w-32 sm:h-32 rounded-full object-cover border-4 border-white shadow-lg">
                        @else
                            <div class="w-24 h-24 sm:w-32 sm:h-32 bg-white rounded-full flex items-center justify-center border-4 border-white shadow-lg">
                                <i class="fas fa-user text-3xl sm:text-4xl text-gray-400"></i>
                            </div>
                        @endif
                        <!-- Status Badge -->
                        <div class="absolute -bottom-2 -right-2 bg-green-500 text-white rounded-full p-2">
                            <i class="fas fa-check text-sm"></i>
                        </div>
                    </div>
                    
                    <!-- Profile Info -->
                    <div class="flex-1 text-white">
                        <h1 class="text-2xl sm:text-3xl font-bold mb-2">
                            {{ $participant->user->first_name ?? $participant->user->name }} {{ $participant->user->last_name ?? '' }}
                        </h1>
                        <p class="text-blue-100 text-lg mb-2">
                            {{ ucwords(str_replace('_', ' ', $participant->participantType->name ?? '')) }}
                        </p>
                        <p class="text-blue-100 text-sm">
                            {{ $participant->conference->name ?? 'No Conference' }}
                        </p>
                        
                        <!-- Quick Stats -->
                        <div class="flex flex-wrap gap-4 mt-4">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-calendar text-blue-200"></i>
                                <span class="text-sm text-blue-100">Joined {{ $participant->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-map-marker-alt text-blue-200"></i>
                                <span class="text-sm text-blue-100">{{ $participant->user->country ?? 'Not specified' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Profile Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Personal Information -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-user-circle text-blue-500 mr-3"></i>
                        Personal Information
                    </h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <p class="text-gray-900">{{ $participant->user->first_name ?? $participant->user->name }} {{ $participant->user->last_name ?? '' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <p class="text-gray-900">{{ $participant->user->email }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <p class="text-gray-900">{{ $participant->user->contact_no ?? 'Not provided' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                            <p class="text-gray-900">{{ $participant->user->country ?? 'Not specified' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Organization</label>
                            <p class="text-gray-900">{{ $participant->user->organization_institution ?? 'Not specified' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Designation</label>
                            <p class="text-gray-900">{{ $participant->user->designation ?? 'Not specified' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Conference Sessions -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-calendar-alt text-green-500 mr-3"></i>
                        Conference Sessions
                    </h2>
                    
                    @if(isset($sessions) && count($sessions) > 0)
                        <div class="space-y-4">
                            @foreach($sessions as $session)
                                <div class="rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                                    <div class="flex flex-col">
                                        <div class="flex-1">
                                            <!-- Session Information in Readable Format -->
                                            <div class="space-y-3">
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                        <div>
                                                            <span class="text-sm font-semibold text-gray-700">Title:</span>
                                                            <p class="text-sm text-gray-900 mt-1">{{ $session->title }}</p>
                                                        </div>
                                                        <div>
                                                            <span class="text-sm font-semibold text-gray-700">Status:</span>
                                                            <p class="text-sm text-gray-900 mt-1 capitalize">{{ $session->status ?? 'Published' }}</p>
                                                        </div>
                                                    </div>
                                                    
                                                    <div>
                                                        <span class="text-sm font-semibold text-gray-700">Description:</span>
                                                        <p class="text-sm text-gray-900 mt-1">{{ $session->description ?? 'No description available' }}</p>
                                                    </div>
                                                    
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                        <div>
                                                            <span class="text-sm font-semibold text-gray-700">Start Date & Time:</span>
                                                            <p class="text-sm text-gray-900 mt-1">{{ \Carbon\Carbon::parse($session->start_time)->format('F j, Y g:i A') }}</p>
                                                        </div>
                                                        <div>
                                                            <span class="text-sm font-semibold text-gray-700">End Date & Time:</span>
                                                            <p class="text-sm text-gray-900 mt-1">{{ \Carbon\Carbon::parse($session->end_time)->format('F j, Y g:i A') }}</p>
                                                        </div>
                                                    </div>
                                                    
                                                    <div>
                                                        <span class="text-sm font-semibold text-gray-700">Duration:</span>
                                                        <p class="text-sm text-gray-900 mt-1">{{ \Carbon\Carbon::parse($session->start_time)->diffInMinutes(\Carbon\Carbon::parse($session->end_time)) }} minutes</p>
                                                    </div>
                                                    
                                                    <div>
                                                        <span class="text-sm font-semibold text-gray-700">Location Details:</span>
                                                        <div class="mt-2 space-y-1">
                                                            <p class="text-sm text-gray-900">
                                                                <span class="font-medium">Venue:</span> {{ $session->venue->name ?? ($session->room ?? 'TBD') }}
                                                            </p>
                                                            @if($session->venue && $session->venue->address)
                                                                <p class="text-sm text-gray-900">
                                                                    <span class="font-medium">Address:</span> {{ $session->venue->address }}
                                                                </p>
                                                            @endif
                                                            @if($session->room && $session->venue)
                                                                <p class="text-sm text-gray-900">
                                                                    <span class="font-medium">Room:</span> {{ $session->room }}
                                                                </p>
                                                            @endif
                                                            @if($session->venue && $session->venue->capacity)
                                                                <p class="text-sm text-gray-900">
                                                                    <span class="font-medium">Capacity:</span> {{ $session->venue->capacity }} people
                                                                </p>
                                                            @endif
                                                        </div>
                                                    </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-calendar-times text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-lg">No sessions available for this conference</p>
                        </div>
                    @endif
                </div>

                <!-- Hotel Information Section -->
                @if($travelDetail && $travelDetail->hotel)
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-hotel text-orange-500 mr-3"></i>
                        Hotel Information
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Hotel Details -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Hotel Name</label>
                                <p class="text-gray-900 text-lg font-semibold">{{ $travelDetail->hotel->name }}</p>
                            </div>
                            
                            @if($travelDetail->hotel->address)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Address</label>
                                <p class="text-gray-900">{{ $travelDetail->hotel->address }}</p>
                            </div>
                            @endif
                            
                            @if($travelDetail->hotel->phone)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Contact Number</label>
                                <p class="text-gray-900">
                                    <a href="tel:{{ $travelDetail->hotel->phone }}" class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-phone mr-2"></i>{{ $travelDetail->hotel->phone }}
                                    </a>
                                </p>
                            </div>
                            @endif
                            
                            @if($travelDetail->hotel->email)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <p class="text-gray-900">
                                    <a href="mailto:{{ $travelDetail->hotel->email }}" class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-envelope mr-2"></i>{{ $travelDetail->hotel->email }}
                                    </a>
                                </p>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Room & Check-in Details -->
                        <div class="space-y-4">
                            @if($travelDetail->room)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Room Assignment</label>
                                <p class="text-gray-900">{{ $travelDetail->room }}</p>
                            </div>
                            @endif
                            
                            @if($travelDetail->room_check_in)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Check-in Date</label>
                                <p class="text-gray-900">
                                    <i class="fas fa-calendar-check text-green-500 mr-2"></i>
                                    {{ \Carbon\Carbon::parse($travelDetail->room_check_in)->format('F j, Y g:i A') }}
                                </p>
                            </div>
                            @endif
                            
                            @if($travelDetail->room_check_out)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Check-out Date</label>
                                <p class="text-gray-900">
                                    <i class="fas fa-calendar-times text-red-500 mr-2"></i>
                                    {{ \Carbon\Carbon::parse($travelDetail->room_check_out)->format('F j, Y g:i A') }}
                                </p>
                            </div>
                            @endif
                            
                            @if($travelDetail->extra_nights > 0)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Extra Nights</label>
                                <p class="text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $travelDetail->extra_nights }} night(s)
                                    </span>
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    @if($travelDetail->hotel->description)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hotel Description</label>
                        <p class="text-gray-900">{{ $travelDetail->hotel->description }}</p>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Conference Documents Section -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-file-alt text-indigo-500 mr-3"></i>
                        Conference Documents
                    </h2>
                    
                    @if(isset($conferenceDocs) && count($conferenceDocs) > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($conferenceDocs as $doc)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-file-pdf text-red-500 text-2xl"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-sm font-medium text-gray-900 truncate">{{ $doc->title }}</h3>
                                            <p class="text-xs text-gray-500 mt-1">{{ $doc->description ?? 'No description' }}</p>
                                            <div class="mt-2 flex items-center space-x-2">
                                                <span class="text-xs text-gray-500">{{ $doc->file_size ?? 'Unknown size' }}</span>
                                                <span class="text-xs text-gray-400">•</span>
                                                <span class="text-xs text-gray-500">{{ $doc->created_at->format('M d, Y') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3 flex space-x-2">
                                        <a href="{{ route('conference-docs.download', $doc) }}" 
                                           class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <i class="fas fa-download mr-1"></i>
                                            Download
                                        </a>
                                        <a href="{{ route('conference-docs.view', $doc) }}" 
                                           class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <i class="fas fa-eye mr-1"></i>
                                            View
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-file-slash text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-lg">No conference documents available</p>
                        </div>
                    @endif
                </div>

                <!-- Travel Details Section -->
                @if($participant->travel_intent && in_array($participant->travel_intent, ['international', '2']))
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-plane text-green-500 mr-3"></i>
                        Travel Details
                    </h2>
                    
                    @if($travelDetail)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Flight Information -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2">Flight Information</h3>
                                
                                @if($travelDetail->arrival_date)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Arrival Date & Time</label>
                                    <p class="text-gray-900">{{ \Carbon\Carbon::parse($travelDetail->arrival_date)->format('M d, Y g:i A') }}</p>
                                </div>
                                @endif
                                
                                @if($travelDetail->departure_date)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Departure Date & Time</label>
                                    <p class="text-gray-900">{{ \Carbon\Carbon::parse($travelDetail->departure_date)->format('M d, Y g:i A') }}</p>
                                </div>
                                @endif
                                
                                @if($travelDetail->flight_info)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Flight Details</label>
                                    <p class="text-gray-900">{{ $travelDetail->flight_info }}</p>
                                </div>
                                @endif
                            </div>
                            
                            <!-- Hotel Information -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2">Hotel Information</h3>
                                
                                @if($travelDetail->hotel)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Hotel Name</label>
                                    <p class="text-gray-900">{{ $travelDetail->hotel->name }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Hotel Address</label>
                                    <p class="text-gray-900">{{ $travelDetail->hotel->address }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Contact</label>
                                    <p class="text-gray-900">{{ $travelDetail->hotel->phone ?? 'Not provided' }}</p>
                                </div>
                                @endif
                                
                                @if($travelDetail->room)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Room Type</label>
                                    <p class="text-gray-900">{{ $travelDetail->room->type ?? 'Not specified' }}</p>
                                </div>
                                @endif
                                
                                @if($travelDetail->room_check_in)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Check-in Date</label>
                                    <p class="text-gray-900">{{ \Carbon\Carbon::parse($travelDetail->room_check_in)->format('M d, Y') }}</p>
                                </div>
                                @endif
                                
                                @if($travelDetail->room_check_out)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Check-out Date</label>
                                    <p class="text-gray-900">{{ \Carbon\Carbon::parse($travelDetail->room_check_out)->format('M d, Y') }}</p>
                                </div>
                                @endif
                                
                                @if($travelDetail->extra_nights > 0)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Extra Nights</label>
                                    <p class="text-gray-900">{{ $travelDetail->extra_nights }} night(s)</p>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        @if($travelDetail->travel_documents)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Travel Documents</h3>
                            <a href="{{ asset('storage/' . $travelDetail->travel_documents) }}" 
                               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <i class="fas fa-download mr-2"></i>
                                Download Travel Documents
                            </a>
                        </div>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-plane-slash text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-lg">No travel details available</p>
                        </div>
                    @endif
                </div>
                @endif

                <!-- Comments Section -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-comments text-purple-500 mr-3"></i>
                        Comments & Notes
                    </h2>
                    
                    <!-- Add Comment Form -->
                    <div class="mb-6">
                        <form id="commentForm" class="space-y-4">
                            @csrf
                            <div>
                                <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Add a comment</label>
                                <textarea id="comment" name="content" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="Write your comment here..." required></textarea>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-paper-plane mr-2"></i>
                                    Post Comment
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Comments List -->
                    <div id="commentsList" class="space-y-4">
                        @if(isset($comments) && count($comments) > 0)
                            @foreach($comments as $comment)
                                <div class="border-l-4 border-blue-500 pl-4 py-2 comment-item" data-comment-id="{{ $comment->id }}">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 mb-1">
                                                <span class="text-sm font-medium text-gray-900">
                                                    @php
                                                        $userName = trim(($comment->user->first_name ?? '') . ' ' . ($comment->user->last_name ?? ''));
                                                        $displayName = $userName ?: ($comment->user->email ?? 'Unknown User');
                                                    @endphp
                                                    {{ $displayName }}
                                                </span>
                                                <span class="text-xs text-gray-500">{{ $comment->created_at->format('M d, Y H:i') }}</span>
                                            </div>
                                            <p class="text-gray-700">{{ $comment->content }}</p>
                                        </div>
                                        @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('superadmin'))
                                        <div class="ml-4 flex-shrink-0">
                                            <button onclick="deleteComment({{ $comment->id }})" 
                                                    class="text-red-600 hover:text-red-800 hover:bg-red-50 p-2 rounded-full transition-colors duration-200"
                                                    title="Delete comment">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-comment-slash text-4xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500">No comments yet</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column - Quick Info & Actions -->
            <div class="space-y-6">
                <!-- Conference Info Card -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        Conference Details
                    </h3>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Conference</label>
                            <p class="text-gray-900">{{ $participant->conference->name ?? 'Not specified' }}</p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-700">Participant Type</label>
                            <p class="text-gray-900">{{ ucwords(str_replace('_', ' ', $participant->participantType->name ?? 'Not specified')) }}</p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-700">Registration Status</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if(($participant->registration_status ?? 'pending') === 'approved') bg-green-100 text-green-800
                                @elseif(($participant->registration_status ?? 'pending') === 'rejected') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($participant->registration_status ?? 'pending') }}
                            </span>
                        </div>
                    </div>
                </div>


                <!-- Contact Information -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-address-book text-green-500 mr-2"></i>
                        Contact Info
                    </h3>
                    
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <i class="fas fa-envelope text-gray-400 mr-3"></i>
                            <span class="text-sm text-gray-900">{{ $participant->user->email }}</span>
                        </div>
                        
                        @if($participant->user->contact_no)
                            <div class="flex items-center">
                                <i class="fas fa-phone text-gray-400 mr-3"></i>
                                <span class="text-sm text-gray-900">{{ $participant->user->contact_no }}</span>
                            </div>
                        @endif
                        
                        @if($participant->user->country)
                            <div class="flex items-center">
                                <i class="fas fa-map-marker-alt text-gray-400 mr-3"></i>
                                <span class="text-sm text-gray-900">{{ $participant->user->country }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Profile dropdown toggle
        document.getElementById('profileDropdownBtn').addEventListener('click', function() {
            const dropdown = document.getElementById('profileDropdown');
            dropdown.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('profileDropdown');
            const button = document.getElementById('profileDropdownBtn');
            
            if (!button.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });

        // Notification function
        function openNotifications() {
            // Redirect to notifications page
            window.location.href = '{{ route("notifications.index") }}';
        }


        // Mobile menu toggle
        document.getElementById('mobileMenuBtn').addEventListener('click', function() {
            // Implement mobile menu functionality if needed
            console.log('Mobile menu clicked');
        });

        // Comments form submission
        document.getElementById('commentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const commentText = formData.get('content');
            
            if (!commentText.trim()) {
                alert('Please enter a comment');
                return;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Posting...';
            submitBtn.disabled = true;
            
            // Submit comment via AJAX
            fetch('{{ route("participant-profiles.comments.store", $participant->id) }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || formData.get('_token'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Add comment to the list
                    addCommentToList(data.comment);
                    // Clear form
                    this.reset();
                } else {
                    alert('Error posting comment: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error posting comment. Please try again.');
            })
            .finally(() => {
                // Reset button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
        
        // Function to add comment to the list
        function addCommentToList(comment) {
            const commentsList = document.getElementById('commentsList');
            const emptyState = commentsList.querySelector('.text-center');
            
            // Remove empty state if it exists
            if (emptyState) {
                emptyState.remove();
            }
            
            // Check if user is admin (you might need to pass this from the server)
            const isAdmin = {{ Auth::user()->hasRole('admin') || Auth::user()->hasRole('superadmin') ? 'true' : 'false' }};
            
            // Create new comment element
            const commentElement = document.createElement('div');
            commentElement.className = 'border-l-4 border-blue-500 pl-4 py-2 comment-item';
            commentElement.setAttribute('data-comment-id', comment.id);
            
            let deleteButton = '';
            if (isAdmin) {
                deleteButton = `
                    <div class="ml-4 flex-shrink-0">
                        <button onclick="deleteComment(${comment.id})" 
                                class="text-red-600 hover:text-red-800 hover:bg-red-50 p-2 rounded-full transition-colors duration-200"
                                title="Delete comment">
                            <i class="fas fa-trash text-sm"></i>
                        </button>
                    </div>
                `;
            }
            
            commentElement.innerHTML = `
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-1">
                            <span class="text-sm font-medium text-gray-900">${comment.user_name || 'Unknown User'}</span>
                            <span class="text-xs text-gray-500">${comment.created_at}</span>
                        </div>
                        <p class="text-gray-700">${comment.content}</p>
                    </div>
                    ${deleteButton}
                </div>
            `;
            
            // Add to the top of the comments list
            commentsList.insertBefore(commentElement, commentsList.firstChild);
        }
        
        // Function to delete a comment
        window.deleteComment = function(commentId) {
            if (!confirm('Are you sure you want to delete this comment? This action cannot be undone.')) {
                return;
            }
            
            // Show loading state on the delete button
            const commentElement = document.querySelector(`[data-comment-id="${commentId}"]`);
            const deleteBtn = commentElement.querySelector('button');
            const originalContent = deleteBtn.innerHTML;
            deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin text-sm"></i>';
            deleteBtn.disabled = true;
            
            // Send delete request
            fetch(`{{ route('participant-profiles.comments.destroy', [$participant->id, 'COMMENT_ID']) }}`.replace('COMMENT_ID', commentId), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the comment element from the DOM
                    commentElement.remove();
                    
                    // Check if there are no more comments and show empty state
                    const commentsList = document.getElementById('commentsList');
                    const remainingComments = commentsList.querySelectorAll('.comment-item');
                    if (remainingComments.length === 0) {
                        commentsList.innerHTML = `
                            <div class="text-center py-8">
                                <i class="fas fa-comment-slash text-4xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500">No comments yet</p>
                            </div>
                        `;
                    }
                } else {
                    alert('Error deleting comment: ' + (data.message || 'Unknown error'));
                    // Reset button state
                    deleteBtn.innerHTML = originalContent;
                    deleteBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting comment. Please try again.');
                // Reset button state
                deleteBtn.innerHTML = originalContent;
                deleteBtn.disabled = false;
            });
        };
    </script>
</body>
</html>