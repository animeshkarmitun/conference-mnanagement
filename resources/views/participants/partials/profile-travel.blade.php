<!-- Travel & Accommodation Information -->
<div class="bg-gray-50 p-6 rounded-lg">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Travel & Accommodation Details</h3>
    
    @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin'))
    <!-- Admin Edit Form -->
    <form method="POST" action="{{ route('participants.travel.update', $participant) }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Arrival Date</label>
                <input type="datetime-local" name="arrival_date" id="arrival_date" value="{{ old('arrival_date', optional($travelDetail)->arrival_date ? \Carbon\Carbon::parse($travelDetail->arrival_date)->format('Y-m-d\TH:i') : '' ) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Departure Date</label>
                <input type="datetime-local" name="departure_date" id="departure_date" value="{{ old('departure_date', optional($travelDetail)->departure_date ? \Carbon\Carbon::parse($travelDetail->departure_date)->format('Y-m-d\TH:i') : '' ) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
            </div>
        </div>
    @else
    <!-- Read-only view for participants -->
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Arrival Date</label>
                <div class="text-gray-900">
                    @if(optional($travelDetail)->arrival_date)
                        {{ \Carbon\Carbon::parse($travelDetail->arrival_date)->format('M d, Y \a\t g:i A') }}
                    @else
                        <span class="text-gray-400">Not specified</span>
                    @endif
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Departure Date</label>
                <div class="text-gray-900">
                    @if(optional($travelDetail)->departure_date)
                        {{ \Carbon\Carbon::parse($travelDetail->departure_date)->format('M d, Y \a\t g:i A') }}
                    @else
                        <span class="text-gray-400">Not specified</span>
                    @endif
                </div>
            </div>
        </div>
    @endif
    
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Travel Intent</label>
                <div class="text-gray-900">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $participant->travel_intent === 'international' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                        {{ ucfirst($participant->travel_intent ?? 'National') }}
                    </span>
                    <p class="text-xs text-gray-500 mt-1">This is set in your Personal Information</p>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Extra Nights</label>
                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin'))
                <input type="number" name="extra_nights" min="0" value="{{ old('extra_nights', optional($travelDetail)->extra_nights ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                @else
                <div class="text-gray-900">{{ optional($travelDetail)->extra_nights ?? 0 }} night(s)</div>
                @endif
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Flight Info</label>
            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin'))
            <textarea name="flight_info" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">{{ old('flight_info', optional($travelDetail)->flight_info) }}</textarea>
            @else
            <div class="text-gray-900">{{ optional($travelDetail)->flight_info ?: 'Not provided' }}</div>
            @endif
        </div>
        <!-- Hotel and Room Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Hotel</label>
                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin'))
                <div class="flex gap-2 items-center">
                    <select name="hotel_id" id="hotel-select" class="mt-1 block flex-1 select2-searchable">
                        <option value="">Search and select hotel...</option>
                        @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}" {{ old('hotel_id', optional($travelDetail)->hotel_id) == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                        @endforeach
                    </select>
                    <button type="button" id="add-hotel-btn" class="mt-1 px-3 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-md text-sm font-medium border border-yellow-700 shadow-sm" title="Add New Hotel">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                @else
                <div class="text-gray-900">
                    @if(optional($travelDetail)->hotel)
                        {{ $travelDetail->hotel->name }}
                    @else
                        <span class="text-gray-400">Not assigned</span>
                    @endif
                </div>
                @endif
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Room</label>
                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin'))
                <div class="flex gap-2 items-center">
                    <select name="room_id" id="room-select" class="mt-1 block flex-1 select2-searchable">
                        <option value="">Search and select room...</option>
                        @if(optional($travelDetail)->hotel_id)
                            @php
                                $selectedHotel = $hotels->firstWhere('id', $travelDetail->hotel_id);
                                $rooms = $selectedHotel ? $selectedHotel->rooms ?? [] : [];
                            @endphp
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" {{ old('room_id', optional($travelDetail)->room_id) == $room->id ? 'selected' : '' }}>
                                    {{ $room->room_number }} - {{ $room->roomType->name ?? ($room->room_type ?? 'Standard') }} ({{ $room->beds ?? 1 }} bed{{ $room->beds > 1 ? 's' : '' }})
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <button type="button" id="room-details-btn" class="mt-1 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium border border-blue-700 shadow-sm" title="Room Details">
                        <i class="fas fa-cog"></i>
                    </button>
                </div>
                @else
                <div class="text-gray-900">
                    @if(optional($travelDetail)->room)
                        {{ $travelDetail->room->room_number }} - {{ $travelDetail->room->roomType->name ?? ($travelDetail->room->room_type ?? 'Standard') }} ({{ $travelDetail->room->beds ?? 1 }} bed{{ $travelDetail->room->beds > 1 ? 's' : '' }})
                    @else
                        <span class="text-gray-400">Not assigned</span>
                    @endif
                </div>
                @endif
            </div>
        </div>
    
        <!-- Check-in/Check-out Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Room Check-in DateTime</label>
                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin'))
                <div id="room-dates-section" style="display: none;">
                    <input type="datetime-local" name="room_check_in" id="room_check_in" value="{{ old('room_check_in', optional($participant->roomAllocation)->check_in ? \Carbon\Carbon::parse($participant->roomAllocation->check_in)->format('Y-m-d\TH:i') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 check-in-input auto-save-input" data-participant-id="{{ $participant->id }}">
                    <p class="text-xs text-gray-500 mt-1">Must be between arrival and departure dates</p>
                </div>
                @else
                <div class="text-gray-900">
                    @if(optional($participant->roomAllocation)->check_in)
                        {{ \Carbon\Carbon::parse($participant->roomAllocation->check_in)->format('M d, Y \a\t g:i A') }}
                    @else
                        <span class="text-gray-400">Not specified</span>
                    @endif
                </div>
                @endif
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Room Check-out DateTime</label>
                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin'))
                <div id="room-dates-section" style="display: none;">
                    <input type="datetime-local" name="room_check_out" id="room_check_out" value="{{ old('room_check_out', optional($participant->roomAllocation)->check_out ? \Carbon\Carbon::parse($participant->roomAllocation->check_out)->format('Y-m-d\TH:i') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 check-out-input auto-save-input" data-participant-id="{{ $participant->id }}">
                    <p class="text-xs text-gray-500 mt-1">Must be between arrival and departure dates</p>
                </div>
                @else
                <div class="text-gray-900">
                    @if(optional($participant->roomAllocation)->check_out)
                        {{ \Carbon\Carbon::parse($participant->roomAllocation->check_out)->format('M d, Y \a\t g:i A') }}
                    @else
                        <span class="text-gray-400">Not specified</span>
                    @endif
                </div>
                @endif
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Travel Documents</label>
            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin'))
            <input type="file" name="travel_documents" class="mt-1 block w-full text-sm text-gray-500">
            @if(optional($travelDetail)->travel_documents)
                <a href="{{ asset('storage/' . $travelDetail->travel_documents) }}" target="_blank" class="text-blue-600 hover:underline mt-2 block">View Current Document</a>
            @endif
            @else
            <div class="text-gray-900">
                @if(optional($travelDetail)->travel_documents)
                    <a href="{{ asset('storage/' . $travelDetail->travel_documents) }}" target="_blank" class="text-blue-600 hover:underline inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        View Uploaded Document
                    </a>
                @else
                    <span class="text-gray-400">No documents uploaded</span>
                @endif
            </div>
            @endif
        </div>
    
    <!-- Conflict Message Container -->
    <div id="message-container" class="hidden mt-4">
        <div id="success-message" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded"></div>
        <div id="error-message" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded"></div>
        <div id="info-message" class="hidden bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded"></div>
    </div>
    
    <!-- Default Check-in/Check-out Times Message -->
    <div id="hotel-times-message" class="hidden bg-blue-50 border border-blue-200 rounded-md p-3">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Hotel Check-in/Check-out Times</h3>
                <div class="mt-1 text-sm text-blue-700">
                    <p id="hotel-times-text">Default check-in and check-out times will be displayed here when a room is selected.</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Room Conflict Warning -->
    <div id="room-conflict-warning" class="hidden bg-red-50 border border-red-200 rounded-md p-3">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-red-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Room Conflict Detected</h3>
                <div class="mt-2 text-sm text-red-700">
                    <p id="conflict-message">This room is already allocated to another participant during the selected dates.</p>
                </div>
            </div>
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Travel Documents</label>
        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin'))
        <input type="file" name="travel_documents" class="mt-1 block w-full text-sm text-gray-500">
        @else
        <div class="mt-1 p-3 bg-gray-50 rounded-md border">
            @if(optional($travelDetail)->travel_documents)
                <a href="{{ asset('storage/' . $travelDetail->travel_documents) }}" target="_blank" class="text-blue-600 hover:underline inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    View Uploaded Document
                </a>
            @else
                <span class="text-gray-400">No documents uploaded</span>
            @endif
        </div>
        @endif
        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin'))
        @if(optional($travelDetail)->travel_documents)
            <a href="{{ asset('storage/' . $travelDetail->travel_documents) }}" target="_blank" class="text-blue-600 hover:underline mt-2 block">View Current Document</a>
        @endif
        @endif
        </div>
        
        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin'))
        <div class="flex justify-end">
            <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold">Save Travel Details</button>
        </div>
        @endif
    @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin'))
    </form>
    @else
    </div>
    @endif

@if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin'))
<!-- Hotel Creation Modal -->
<div id="hotel-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-4/5 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Add New Hotel</h3>
                <button type="button" id="close-hotel-modal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="hotel-form" class="space-y-4">
                @csrf
                <input type="hidden" name="conference_id" value="{{ $participant->conference_id }}">
                
                <!-- Hotel Name -->
                <div>
                    <label for="hotel_name" class="block text-sm font-medium text-gray-700">Hotel Name <span class="text-red-500">*</span></label>
                    <input type="text" id="hotel_name" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                </div>
                
                <!-- Address -->
                <div>
                    <label for="hotel_address" class="block text-sm font-medium text-gray-700">Address <span class="text-red-500">*</span></label>
                    <textarea id="hotel_address" name="address" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500"></textarea>
                </div>
                
                <!-- Room Capacity, Contact Email, Contact Phone -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="hotel_room_capacity" class="block text-sm font-medium text-gray-700">Room Capacity <span class="text-red-500">*</span></label>
                        <input type="number" id="hotel_room_capacity" name="room_capacity" min="1" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    </div>
                    
                    <div>
                        <label for="hotel_contact_email" class="block text-sm font-medium text-gray-700">Contact Email</label>
                        <input type="email" id="hotel_contact_email" name="contact_email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    </div>
                    
                    <div>
                        <label for="hotel_contact_phone" class="block text-sm font-medium text-gray-700">Contact Phone</label>
                        <input type="text" id="hotel_contact_phone" name="contact_phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    </div>
                </div>
                
                <!-- Website, Check-in Time, Check-out Time -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="hotel_website" class="block text-sm font-medium text-gray-700">Website</label>
                        <input type="url" id="hotel_website" name="website" placeholder="https://example.com" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    </div>
                    
                    <div>
                        <label for="hotel_check_in_time" class="block text-sm font-medium text-gray-700">Check-in Time</label>
                        <input type="time" id="hotel_check_in_time" name="check_in_time" value="15:00" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    </div>
                    
                    <div>
                        <label for="hotel_check_out_time" class="block text-sm font-medium text-gray-700">Check-out Time</label>
                        <input type="time" id="hotel_check_out_time" name="check_out_time" value="11:00" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    </div>
                </div>
                
                <!-- Description -->
                <div>
                    <label for="hotel_description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="hotel_description" name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500"></textarea>
                </div>
                
                <!-- Amenities -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amenities</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        @php
                            $commonAmenities = ['WiFi', 'Parking', 'Pool', 'Gym', 'Restaurant', 'Bar', 'Spa', 'Business Center', 'Conference Room', 'Laundry Service'];
                        @endphp
                        @foreach($commonAmenities as $amenity)
                            <div class="flex items-center">
                                <input type="checkbox" name="amenities[]" value="{{ $amenity }}" id="amenity_{{ $loop->index }}" class="rounded border-gray-300 text-yellow-600 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                                <label for="amenity_{{ $loop->index }}" class="ml-2 text-sm text-gray-700">{{ $amenity }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Active Hotel -->
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="hotel_is_active" checked class="rounded border-gray-300 text-yellow-600 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                    <label for="hotel_is_active" class="ml-2 text-sm text-gray-700">Active Hotel</label>
                </div>
                
                <div id="hotel-form-errors" class="text-red-600 text-sm hidden"></div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancel-hotel" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-md text-sm font-medium">Cancel</button>
                    <button type="submit" id="save-hotel" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-md text-sm font-medium">Save Hotel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Room Details Modal -->
<div id="room-details-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-4/5 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Room Details</h3>
                <button type="button" id="close-room-modal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="room-details-form" class="space-y-4">
                @csrf
                <input type="hidden" name="participant_id" value="{{ $participant->id }}">
                
                <!-- Room Number and Room Type -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="room_number" class="block text-sm font-medium text-gray-700">Room Number <span class="text-red-500">*</span></label>
                        <input type="text" id="room_number" name="room_number" value="{{ old('room_number', optional($participant->roomAllocation)->room_number) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="e.g., 101, A-205" required>
                    </div>
                    
                    <div>
                        <label for="room_type_id" class="block text-sm font-medium text-gray-700">Room Type</label>
                        <select id="room_type_id" name="room_type_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Room Type</option>
                            @foreach($roomTypes as $roomType)
                                <option value="{{ $roomType->id }}">{{ $roomType->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <!-- Number of Beds and Price per Night -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="number_of_beds" class="block text-sm font-medium text-gray-700">Number of Beds</label>
                        <input type="number" id="number_of_beds" name="number_of_beds" min="1" max="10" value="{{ old('number_of_beds', optional($participant->roomAllocation)->number_of_beds) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="1">
                    </div>
                    
                    <div>
                        <label for="price_per_night" class="block text-sm font-medium text-gray-700">Price per Night</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">$</span>
                            <input type="number" id="price_per_night" name="price_per_night" min="0" step="0.01" class="flex-1 rounded-none rounded-r-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="0.00">
                        </div>
                    </div>
                </div>
                
                <!-- Description -->
                <div>
                    <label for="room_description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="room_description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Room description..."></textarea>
                </div>
                
                <!-- Available Checkbox -->
                <div class="flex items-center">
                    <input type="checkbox" name="is_available" id="room_is_available" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <label for="room_is_available" class="ml-2 text-sm text-gray-700">Available</label>
                </div>
                
                <div id="room-form-errors" class="text-red-600 text-sm hidden"></div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancel-room" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-md text-sm font-medium">Cancel</button>
                    <button type="submit" id="save-room" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium">Save Room Details</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hotel modal elements
    const modal = document.getElementById('hotel-modal');
    const addBtn = document.getElementById('add-hotel-btn');
    const closeBtn = document.getElementById('close-hotel-modal');
    const cancelBtn = document.getElementById('cancel-hotel');
    const hotelForm = document.getElementById('hotel-form');
    const hotelSelect = document.getElementById('hotel-select');
    const errorDiv = document.getElementById('hotel-form-errors');
    
    // Room details modal elements
    const roomModal = document.getElementById('room-details-modal');
    const roomDetailsBtn = document.getElementById('room-details-btn');
    const closeRoomBtn = document.getElementById('close-room-modal');
    const cancelRoomBtn = document.getElementById('cancel-room');
    const roomForm = document.getElementById('room-details-form');
    const roomErrorDiv = document.getElementById('room-form-errors');
    
    // Open modal
    addBtn.addEventListener('click', function() {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    });
    
    // Close modal functions
    function closeModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        hotelForm.reset();
        errorDiv.classList.add('hidden');
        errorDiv.innerHTML = '';
    }
    
    closeBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);
    
    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });
    
    // Handle form submission
    hotelForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(hotelForm);
        const saveBtn = document.getElementById('save-hotel');
        
        // Disable save button and show loading state
        saveBtn.disabled = true;
        saveBtn.textContent = 'Saving...';
        
        fetch('{{ route("hotels.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Add new hotel to select dropdown
                const option = new Option(data.hotel.name, data.hotel.id, true, true);
                hotelSelect.add(option);
                
                // Close modal
                closeModal();
                
                // Show success message
                showNotification('Hotel created successfully!', 'success');
            } else {
                // Show error message
                errorDiv.innerHTML = data.message || 'An error occurred while creating the hotel.';
                errorDiv.classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            errorDiv.innerHTML = 'An error occurred while creating the hotel.';
            errorDiv.classList.remove('hidden');
        })
        .finally(() => {
            // Re-enable save button
            saveBtn.disabled = false;
            saveBtn.textContent = 'Save Hotel';
        });
    });
    
    // Room Details Modal functionality
    // Open room modal
    roomDetailsBtn.addEventListener('click', function() {
        roomModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Initialize Select2 for modal elements
        $('#room_hotel_id').select2({
            theme: 'bootstrap-5',
            placeholder: 'Search and select hotel...',
            allowClear: true,
            minimumInputLength: 0
        });
    });
    
    // Close room modal functions
    function closeRoomModal() {
        roomModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        roomForm.reset();
        roomErrorDiv.classList.add('hidden');
        roomErrorDiv.innerHTML = '';
    }
    
    closeRoomBtn.addEventListener('click', closeRoomModal);
    cancelRoomBtn.addEventListener('click', closeRoomModal);
    
    // Close room modal when clicking outside
    roomModal.addEventListener('click', function(e) {
        if (e.target === roomModal) {
            closeRoomModal();
        }
    });
    
    // Handle room form submission
    roomForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(roomForm);
        const saveBtn = document.getElementById('save-room');
        
        // Disable save button and show loading state
        saveBtn.disabled = true;
        saveBtn.textContent = 'Saving...';
        
        fetch('{{ route("room.allocation.update", $participant) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Close modal
                closeRoomModal();
                
                // Show success message
                showNotification('Room details saved successfully!', 'success');
            } else {
                // Show error message
                let errorMessage = data.message || 'An error occurred while saving room details.';
                
                // If there are validation errors, show them
                if (data.errors) {
                    errorMessage = Object.values(data.errors).flat().join('<br>');
                }
                
                roomErrorDiv.innerHTML = errorMessage;
                roomErrorDiv.classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            roomErrorDiv.innerHTML = 'An error occurred while saving room details.';
            roomErrorDiv.classList.remove('hidden');
        })
        .finally(() => {
            // Re-enable save button
            saveBtn.disabled = false;
            saveBtn.textContent = 'Save Room Details';
        });
    });
    
    // Handle room selection to show/hide room dates and hotel times
    function toggleRoomDatesSection() {
        const roomSelect = document.getElementById('room-select');
        const roomDatesSection = document.getElementById('room-dates-section');
        const hotelTimesMessage = document.getElementById('hotel-times-message');
        
        if (roomSelect.value) {
            roomDatesSection.style.display = 'block';
            showHotelTimes(roomSelect.value);
        } else {
            roomDatesSection.style.display = 'none';
            hotelTimesMessage.classList.add('hidden');
        }
    }
    
    // Show hotel check-in/check-out times
    function showHotelTimes(roomId) {
        const hotelTimesMessage = document.getElementById('hotel-times-message');
        const hotelTimesText = document.getElementById('hotel-times-text');
        
        // Get the selected hotel ID
        const hotelSelect = document.getElementById('hotel-select');
        const hotelId = hotelSelect.value;
        
        if (hotelId) {
            // Fetch hotel times from API
            fetch(`/api/hotels/${hotelId}/times`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const checkInTime = data.hotel.check_in_time || '15:00';
                        const checkOutTime = data.hotel.check_out_time || '11:00';
                        
                        // Format times for display
                        const formatTime = (time) => {
                            const [hours, minutes] = time.split(':');
                            const hour12 = hours % 12 || 12;
                            const ampm = hours >= 12 ? 'PM' : 'AM';
                            return `${hour12}:${minutes} ${ampm}`;
                        };
                        
                        hotelTimesText.textContent = `Hotel "${data.hotel.name}" - Check-in: ${formatTime(checkInTime)}, Check-out: ${formatTime(checkOutTime)}`;
                        hotelTimesMessage.classList.remove('hidden');
                    } else {
                        hotelTimesText.textContent = 'Unable to load hotel times.';
                        hotelTimesMessage.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error loading hotel times:', error);
                    hotelTimesText.textContent = 'Unable to load hotel times.';
                    hotelTimesMessage.classList.remove('hidden');
                });
        } else {
            hotelTimesMessage.classList.add('hidden');
        }
    }
    
    // Handle hotel selection change in main form to load rooms
    $('#hotel-select').on('change', function() {
        const hotelId = $(this).val();
        const roomSelect = $('#room-select');
        
        // Clear existing options and hide room dates
        roomSelect.empty().append('<option value="">Search and select room...</option>');
        document.getElementById('room-dates-section').style.display = 'none';
        document.getElementById('hotel-times-message').classList.add('hidden');
        
        if (hotelId) {
            // Fetch rooms for selected hotel
            fetch(`/api/hotels/${hotelId}/rooms`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.rooms) {
                        data.rooms.forEach(room => {
                            const option = new Option(
                                `${room.room_number} - ${room.room_type || 'Standard'} (${room.beds || 1} bed${room.beds > 1 ? 's' : ''})`,
                                room.id,
                                false,
                                false
                            );
                            roomSelect.append(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error loading rooms:', error);
                });
        }
    });
    
    // Handle room selection change to check for conflicts and show dates
    $('#room-select').on('change', function() {
        toggleRoomDatesSection();
        checkRoomConflicts();
    });
    
    // Enhanced conflict detection for Travel & Accommodation
    let saveTimeouts = {};
    let isSaving = {};
    
    // Real-time validation for check-in/check-out times
    document.querySelectorAll('.check-in-input, .check-out-input').forEach(function(input) {
        input.addEventListener('change', function() {
            const participantId = this.dataset.participantId;
            validateTimeInputs(participantId);
            
            // Check for conflicts immediately when check-in time is selected
            if (this.classList.contains('check-in-input') && this.value) {
                console.log('Check-in time selected for participant:', participantId, 'Value:', this.value);
                const checkinHotelSelect = document.getElementById('hotel-select');
                const checkinRoomSelect = document.getElementById('room-select');
                const checkinCheckOutInput = document.getElementById('room_check_out');
                
                console.log('Check-in conflict check - Hotel:', checkinHotelSelect?.value, 'Room:', checkinRoomSelect?.value);
                
                if (checkinHotelSelect && checkinRoomSelect && checkinHotelSelect.value && checkinRoomSelect.value) {
                    // Use check-out time if available, otherwise use check-in + 1 day as temporary end time
                    const checkOutTime = checkinCheckOutInput?.value || new Date(new Date(this.value).getTime() + 24 * 60 * 60 * 1000).toISOString().slice(0, 16);
                    console.log('Checking conflicts immediately for check-in time selection - CheckOut:', checkOutTime);
                    validateRoomConflicts(participantId, checkinHotelSelect.value, checkinRoomSelect.value, this.value, checkOutTime);
                } else {
                    console.log('Cannot check conflicts - missing hotel or room selection');
                }
            }
            
            // Check for conflicts immediately when check-out time is selected
            if (this.classList.contains('check-out-input') && this.value) {
                console.log('Check-out time selected for participant:', participantId, 'Value:', this.value);
                const checkoutHotelSelect = document.getElementById('hotel-select');
                const checkoutRoomSelect = document.getElementById('room-select');
                const checkoutCheckInInput = document.getElementById('room_check_in');
                
                console.log('Check-out conflict check - Hotel:', checkoutHotelSelect?.value, 'Room:', checkoutRoomSelect?.value, 'CheckIn:', checkoutCheckInInput?.value);
                
                if (checkoutHotelSelect && checkoutRoomSelect && checkoutHotelSelect.value && checkoutRoomSelect.value && checkoutCheckInInput && checkoutCheckInInput.value) {
                    console.log('Checking conflicts immediately for check-out time selection');
                    validateRoomConflicts(participantId, checkoutHotelSelect.value, checkoutRoomSelect.value, checkoutCheckInInput.value, this.value);
                } else {
                    console.log('Cannot check conflicts - missing hotel, room, or check-in selection');
                }
            }
        });
        
        // Add real-time validation on input
        input.addEventListener('input', function() {
            const participantId = this.dataset.participantId;
            // Only validate if both fields have values
            const checkInInput = document.getElementById('room_check_in');
            const checkOutInput = document.getElementById('room_check_out');
            
            if (checkInInput && checkOutInput && checkInInput.value && checkOutInput.value) {
                validateTimeInputs(participantId);
                
                // Also check for room conflicts in real-time
                const realtimeHotelSelect = document.getElementById('hotel-select');
                const realtimeRoomSelect = document.getElementById('room-select');
                
                if (realtimeHotelSelect && realtimeRoomSelect && realtimeHotelSelect.value && realtimeRoomSelect.value) {
                    validateRoomConflicts(participantId, realtimeHotelSelect.value, realtimeRoomSelect.value, checkInInput.value, checkOutInput.value);
                }
            }
        });
    });
    
    // Handle arrival/departure date changes
    $('#arrival_date, #departure_date').on('change', function() {
        validateRoomDates();
    });
    
    // Enhanced validation functions
    function validateTimeInputs(participantId) {
        const checkInInput = document.getElementById('room_check_in');
        const checkOutInput = document.getElementById('room_check_out');
        
        if (!checkInInput || !checkOutInput) {
            return true;
        }
        
        const checkInValue = checkInInput.value;
        const checkOutValue = checkOutInput.value;
        
        // Reset border colors
        checkInInput.style.borderColor = '';
        checkOutInput.style.borderColor = '';
        
        // Basic time validation
        if (checkInValue && checkOutValue) {
            const checkInDate = new Date(checkInValue);
            const checkOutDate = new Date(checkOutValue);
            
            if (checkInDate >= checkOutDate) {
                checkInInput.style.borderColor = '#ef4444';
                checkOutInput.style.borderColor = '#ef4444';
                showError('Check-out time must be after check-in time.');
                return false;
            }
            
            // Check if stay is too long (more than 30 days)
            const timeDiff = checkOutDate - checkInDate;
            const daysDiff = timeDiff / (1000 * 60 * 60 * 24);
            if (daysDiff > 30) {
                showError('Room stay cannot exceed 30 days.');
                return false;
            }
        }
        
        // Check if check-in is in the past
        if (checkInValue) {
            const checkInDate = new Date(checkInValue);
            const now = new Date();
            
            if (checkInDate < now) {
                checkInInput.style.borderColor = '#ef4444';
                showError('Check-in time cannot be in the past.');
                return false;
            }
        }
        
        return true;
    }
    
    function validateRoomConflicts(participantId, hotelId, roomNumber, checkIn, checkOut) {
        console.log('Checking room conflicts for participant:', participantId, 'Hotel:', hotelId, 'Room:', roomNumber, 'CheckIn:', checkIn, 'CheckOut:', checkOut);
        
        if (!checkIn) {
            console.log('Skipping room conflict check - no check-in time provided');
            return true;
        }
        
        // If no check-out time provided, use check-in + 1 day as temporary end time
        if (!checkOut) {
            checkOut = new Date(new Date(checkIn).getTime() + 24 * 60 * 60 * 1000).toISOString().slice(0, 16);
            console.log('Using temporary check-out time:', checkOut);
        }
        
        // Validate that check-in is before check-out
        const checkInDate = new Date(checkIn);
        const checkOutDate = new Date(checkOut);
        
        if (checkInDate >= checkOutDate) {
            console.log('Invalid time range - check-in must be before check-out');
            showConflictError('Check-in time must be before check-out time.');
            return false;
        }
        
        // For now, we'll use the existing API-based conflict checking
        // This could be enhanced to do client-side checking like in Room Allocations
        checkRoomConflicts();
        
        return true;
    }
    
    // Message display functions
    function showSuccess(message) {
        hideMessages();
        const successDiv = document.getElementById('success-message');
        const messageContainer = document.getElementById('message-container');
        
        if (successDiv && messageContainer) {
            successDiv.innerHTML = message + ' <button type="button" class="ml-2 text-green-600 hover:text-green-800" onclick="hideMessages()">&times;</button>';
            successDiv.classList.remove('hidden');
            messageContainer.classList.remove('hidden');
            
            setTimeout(hideMessages, 4000);
        }
    }
    
    function showError(message) {
        hideMessages();
        const errorDiv = document.getElementById('error-message');
        const messageContainer = document.getElementById('message-container');
        
        if (errorDiv && messageContainer) {
            errorDiv.innerHTML = message + ' <button type="button" class="ml-2 text-red-600 hover:text-red-800" onclick="hideMessages()">&times;</button>';
            errorDiv.classList.remove('hidden');
            messageContainer.classList.remove('hidden');
        }
    }
    
    function showConflictError(message) {
        hideMessages();
        const errorDiv = document.getElementById('error-message');
        const messageContainer = document.getElementById('message-container');
        
        if (errorDiv && messageContainer) {
            errorDiv.innerHTML = '<strong>⚠️ Room Conflict:</strong> ' + message + ' <button type="button" class="ml-2 text-red-600 hover:text-red-800 font-bold" onclick="hideMessages()">&times;</button>';
            errorDiv.classList.remove('hidden');
            messageContainer.classList.remove('hidden');
            
            errorDiv.style.backgroundColor = '#fef2f2';
            errorDiv.style.borderLeft = '4px solid #ef4444';
            errorDiv.style.padding = '12px';
        }
    }
    
    function hideMessages() {
        const successDiv = document.getElementById('success-message');
        const errorDiv = document.getElementById('error-message');
        const infoDiv = document.getElementById('info-message');
        const messageContainer = document.getElementById('message-container');
        
        if (successDiv) successDiv.classList.add('hidden');
        if (errorDiv) errorDiv.classList.add('hidden');
        if (infoDiv) infoDiv.classList.add('hidden');
        if (messageContainer) messageContainer.classList.add('hidden');
    }
    
    // Make functions globally accessible
    window.hideMessages = hideMessages;
    window.showConflictError = showConflictError;
    
    // Show conflict message
    function showConflictMessage() {
        const conflictWarning = document.getElementById('room-conflict-warning');
        const conflictMessage = document.getElementById('conflict-message');
        
        // Check if there are any conflicts first
        const roomId = document.getElementById('room-select').value;
        const checkIn = document.getElementById('room_check_in').value;
        const checkOut = document.getElementById('room_check_out').value;
        
        if (roomId && checkIn && checkOut) {
            // The actual conflict checking will be done by checkRoomConflicts()
            // This just shows the message area
            conflictMessage.textContent = 'Checking for room conflicts...';
            conflictWarning.classList.remove('hidden');
        }
    }
    
    // Hide conflict message
    function hideConflictMessage() {
        const conflictWarning = document.getElementById('room-conflict-warning');
        conflictWarning.classList.add('hidden');
    }
    
    // Function to validate room dates are between arrival and departure
    function validateRoomDates() {
        const arrivalDate = document.getElementById('arrival_date').value;
        const departureDate = document.getElementById('departure_date').value;
        const roomCheckIn = document.getElementById('room_check_in').value;
        const roomCheckOut = document.getElementById('room_check_out').value;
        
        if (arrivalDate && departureDate) {
            const arrival = new Date(arrivalDate);
            const departure = new Date(departureDate);
            
            // Set min/max for room dates
            document.getElementById('room_check_in').min = arrivalDate.split('T')[0];
            document.getElementById('room_check_in').max = departureDate.split('T')[0];
            document.getElementById('room_check_out').min = arrivalDate.split('T')[0];
            document.getElementById('room_check_out').max = departureDate.split('T')[0];
            
            // Validate current room dates
            if (roomCheckIn && new Date(roomCheckIn) < arrival) {
                document.getElementById('room_check_in').setCustomValidity('Check-in date must be on or after arrival date');
            } else if (roomCheckIn && new Date(roomCheckIn) > departure) {
                document.getElementById('room_check_in').setCustomValidity('Check-in date must be on or before departure date');
            } else {
                document.getElementById('room_check_in').setCustomValidity('');
            }
            
            if (roomCheckOut && new Date(roomCheckOut) < arrival) {
                document.getElementById('room_check_out').setCustomValidity('Check-out date must be on or after arrival date');
            } else if (roomCheckOut && new Date(roomCheckOut) > departure) {
                document.getElementById('room_check_out').setCustomValidity('Check-out date must be on or before departure date');
            } else {
                document.getElementById('room_check_out').setCustomValidity('');
            }
        }
    }
    
    // Function to check for room conflicts
    function checkRoomConflicts() {
        const roomId = $('#room-select').val();
        const checkIn = document.getElementById('room_check_in').value;
        const checkOut = document.getElementById('room_check_out').value;
        const participantId = {{ $participant->id }};
        
        if (roomId && checkIn && checkOut) {
            const warningDiv = document.getElementById('room-conflict-warning');
            const conflictMessage = document.getElementById('conflict-message');
            
            // Show loading message
            conflictMessage.textContent = 'Checking for room conflicts...';
            warningDiv.classList.remove('hidden');
            
            fetch('/api/rooms/check-conflicts', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    room_id: roomId,
                    check_in: checkIn,
                    check_out: checkOut,
                    participant_id: participantId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.has_conflict) {
                    warningDiv.classList.remove('hidden');
                    conflictMessage.innerHTML = `
                        <strong>Room Conflict Detected!</strong><br>
                        ${data.message || 'This room is already allocated to another participant during the selected dates.'}
                    `;
                } else {
                    warningDiv.classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('Error checking room conflicts:', error);
                conflictMessage.innerHTML = `
                    <strong>Error checking conflicts:</strong><br>
                    Unable to verify room availability. Please try again.
                `;
            });
        } else {
            document.getElementById('room-conflict-warning').classList.add('hidden');
        }
    }
    
    // Initialize on page load
    toggleRoomDatesSection();

    // Simple notification function
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-6 py-3 rounded-md text-white z-50 ${
            type === 'success' ? 'bg-green-600' : 'bg-red-600'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
});
</script>
@endif 