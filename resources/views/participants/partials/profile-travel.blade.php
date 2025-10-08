<form method="POST" action="{{ route('participants.travel.update', $participant) }}" enctype="multipart/form-data" class="space-y-4">
    @csrf
    @method('PUT')
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Arrival Date</label>
            <input type="datetime-local" name="arrival_date" value="{{ old('arrival_date', optional($travelDetail)->arrival_date ? \Carbon\Carbon::parse($travelDetail->arrival_date)->format('Y-m-d\TH:i') : '' ) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Departure Date</label>
            <input type="datetime-local" name="departure_date" value="{{ old('departure_date', optional($travelDetail)->departure_date ? \Carbon\Carbon::parse($travelDetail->departure_date)->format('Y-m-d\TH:i') : '' ) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
        </div>
    </div>
            <div>
            <label class="block text-sm font-medium text-gray-700">Flight Info</label>
            <textarea name="flight_info" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">{{ old('flight_info', optional($travelDetail)->flight_info) }}</textarea>
        </div>
    <!-- Hotel and Room Selection Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Hotel Selection (Left) -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Hotel</label>
            <div class="flex gap-2 items-center">
                <select name="hotel_id" id="hotel-select" class="mt-1 block flex-1 select2-searchable">
                    <option value="">Search and select hotel...</option>
                    @foreach($hotels as $hotel)
                        <option value="{{ $hotel->id }}" {{ old('hotel_id', optional($travelDetail)->hotel_id) == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                    @endforeach
                </select>
                <button type="button" id="add-hotel-btn" class="mt-1 px-3 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-md text-sm font-medium border border-yellow-700 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    
                </button>
            </div>
        </div>
        
        <!-- Room Selection (Right) -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Room</label>
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
                                {{ $room->room_number }} - {{ $room->room_type ?? 'Standard' }} ({{ $room->beds ?? 1 }} bed{{ $room->beds > 1 ? 's' : '' }})
                            </option>
                        @endforeach
                    @endif
                </select>
                <button type="button" id="room-details-btn" class="mt-1 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium border border-blue-700 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Extra Nights</label>
        <input type="number" name="extra_nights" min="0" value="{{ old('extra_nights', optional($travelDetail)->extra_nights ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Travel Documents</label>
        <input type="file" name="travel_documents" class="mt-1 block w-full text-sm text-gray-500">
        @if(optional($travelDetail)->travel_documents)
            <a href="{{ asset('storage/' . $travelDetail->travel_documents) }}" target="_blank" class="text-blue-600 hover:underline mt-2 block">View Uploaded Document</a>
        @endif
    </div>
    <div class="flex justify-end">
        <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold">Save Travel Details</button>
    </div>
</form>

<!-- Hotel Creation Modal -->
<div id="hotel-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
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
                
                <div>
                    <label for="hotel_name" class="block text-sm font-medium text-gray-700">Hotel Name</label>
                    <input type="text" id="hotel_name" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                </div>
                
                <div>
                    <label for="hotel_address" class="block text-sm font-medium text-gray-700">Address</label>
                    <textarea id="hotel_address" name="address" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500"></textarea>
                </div>
                
                <div>
                    <label for="hotel_room_capacity" class="block text-sm font-medium text-gray-700">Room Capacity</label>
                    <input type="number" id="hotel_room_capacity" name="room_capacity" min="1" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
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
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
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
                
                <div>
                    <label for="room_hotel_id" class="block text-sm font-medium text-gray-700">Hotel</label>
                    <select id="room_hotel_id" name="hotel_id" required class="mt-1 block w-full select2-searchable">
                        <option value="">Search and select hotel...</option>
                        @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}" {{ old('hotel_id', optional($participant->roomAllocation)->hotel_id) == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="room_number" class="block text-sm font-medium text-gray-700">Room Number</label>
                    <input type="text" id="room_number" name="room_number" value="{{ old('room_number', optional($participant->roomAllocation)->room_number) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="e.g., 101, A-205">
                </div>
                
                <div>
                    <label for="number_of_beds" class="block text-sm font-medium text-gray-700">Number of Beds</label>
                    <input type="number" id="number_of_beds" name="number_of_beds" min="1" max="10" value="{{ old('number_of_beds', optional($participant->roomAllocation)->number_of_beds) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="1">
                </div>
                
                <div>
                    <label for="check_in" class="block text-sm font-medium text-gray-700">Check-in Date</label>
                    <input type="date" id="check_in" name="check_in" value="{{ old('check_in', optional($participant->roomAllocation)->check_in ? \Carbon\Carbon::parse($participant->roomAllocation->check_in)->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                
                <div>
                    <label for="check_out" class="block text-sm font-medium text-gray-700">Check-out Date</label>
                    <input type="date" id="check_out" name="check_out" value="{{ old('check_out', optional($participant->roomAllocation)->check_out ? \Carbon\Carbon::parse($participant->roomAllocation->check_out)->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
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
    
    // Handle hotel selection change in main form to load rooms
    $('#hotel-select').on('change', function() {
        const hotelId = $(this).val();
        const roomSelect = $('#room-select');
        
        // Clear existing options
        roomSelect.empty().append('<option value="">Search and select room...</option>');
        
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