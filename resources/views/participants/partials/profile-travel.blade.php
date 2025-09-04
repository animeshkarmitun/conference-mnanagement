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
    <div>
        <label class="block text-sm font-medium text-gray-700">Hotel</label>
        <div class="flex gap-2 items-center">
            <select name="hotel_id" id="hotel-select" class="mt-1 block flex-1 rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                <option value="">Select Hotel</option>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('hotel-modal');
    const addBtn = document.getElementById('add-hotel-btn');
    const closeBtn = document.getElementById('close-hotel-modal');
    const cancelBtn = document.getElementById('cancel-hotel');
    const hotelForm = document.getElementById('hotel-form');
    const hotelSelect = document.getElementById('hotel-select');
    const errorDiv = document.getElementById('hotel-form-errors');
    
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