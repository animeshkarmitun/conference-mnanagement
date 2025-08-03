@extends('layouts.app')

@section('title', 'Conference Details')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-xl shadow p-6 mb-8">
        <h2 class="text-2xl font-bold mb-4">{{ $conference->name }}</h2>
        <div class="mb-2"><span class="font-semibold text-gray-700">Start Date:</span> {{ $conference->start_date }}</div>
        <div class="mb-2"><span class="font-semibold text-gray-700">End Date:</span> {{ $conference->end_date }}</div>
        <div class="mb-2"><span class="font-semibold text-gray-700">Status:</span> {{ ucfirst($conference->status) }}</div>
        <div class="mb-2"><span class="font-semibold text-gray-700">Location:</span> {{ $conference->location }}</div>
        <div class="mb-2"><span class="font-semibold text-gray-700">Venue:</span> {{ $conference->venue->name ?? '-' }}<br><span class="text-sm text-gray-500">{{ $conference->venue->address ?? '' }}</span></div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Sessions -->
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold mb-2">Sessions</h3>
            @if($conference->sessions->count())
                <ul class="divide-y divide-gray-200">
                    @foreach($conference->sessions as $session)
                        <li class="py-2">
                            <span class="font-semibold">{{ $session->title }}</span><br>
                            <span class="text-sm text-gray-500">{{ $session->start_time }} - {{ $session->end_time }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500">No sessions found.</p>
            @endif
        </div>
        <!-- Speakers -->
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold mb-2">Speakers</h3>
            @php
                $speakers = $conference->participants->filter(fn($p) => $p->roles->contains('name', 'Speaker'));
            @endphp
            @if($speakers->count())
                <ul class="divide-y divide-gray-200">
                    @foreach($speakers as $speaker)
                        <li class="py-2">
                            <span class="font-semibold">{{ $speaker->user->first_name ?? '' }} {{ $speaker->user->last_name ?? '' }}</span>
                            <span class="text-sm text-gray-500"> ({{ $speaker->user->email ?? '' }})</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500">No speakers found.</p>
            @endif
        </div>
        <!-- Participants -->
        <div class="bg-white rounded-xl shadow p-6 md:col-span-2">
            <h3 class="text-lg font-semibold mb-2">Participants</h3>
            @if($conference->participants->count())
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left">
                                <input type="checkbox" class="participant-checkbox-all">
                            </th>
                            <th class="px-4 py-2 text-left cursor-pointer hover:bg-gray-50" onclick="sortTable(1)">
                                <div class="flex items-center">
                                    SERIAL NO.
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                    </svg>
                                </div>
                            </th>
                            <th class="px-4 py-2 text-left cursor-pointer hover:bg-gray-50" onclick="sortTable(2)">
                                <div class="flex items-center">
                                    NAME
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                    </svg>
                                </div>
                            </th>
                            <th class="px-4 py-2 text-left cursor-pointer hover:bg-gray-50" onclick="sortTable(3)">
                                <div class="flex items-center">
                                    EMAIL
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                    </svg>
                                </div>
                            </th>
                            <th class="px-4 py-2 text-left cursor-pointer hover:bg-gray-50" onclick="sortTable(4)">
                                <div class="flex items-center">
                                    TYPE
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                    </svg>
                                </div>
                            </th>
                            <th class="px-4 py-2 text-left cursor-pointer hover:bg-gray-50" onclick="sortTable(5)">
                                <div class="flex items-center">
                                    ORGANIZATION
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                    </svg>
                                </div>
                            </th>
                            <th class="px-4 py-2 text-left cursor-pointer hover:bg-gray-50" onclick="sortTable(6)">
                                <div class="flex items-center">
                                    STATUS
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                    </svg>
                                </div>
                            </th>
                            <th class="px-4 py-2 text-left cursor-pointer hover:bg-gray-50" onclick="sortTable(7)">
                                <div class="flex items-center">
                                    VISA STATUS
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                    </svg>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($conference->participants as $i => $participant)
                            @php
                                $user = $participant->user;
                                $serial = $participant->serial_number ?? (sprintf('CONF%04d-%03d', $conference->id, $i+1));
                                $serialParts = explode('-', $serial);
                                $conferenceNum = isset($serialParts[0]) ? intval(substr($serialParts[0], 4)) : 0;
                                $participantNum = isset($serialParts[1]) ? intval($serialParts[1]) : 0;
                                $serialSortValue = sprintf('%04d-%03d', $conferenceNum, $participantNum);
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2">
                                    <input type="checkbox" class="participant-checkbox" value="{{ $participant->id }}">
                                </td>
                                <td class="px-4 py-2" data-sort-value="{{ $serialSortValue }}">{{ $serial }}</td>
                                <td class="px-4 py-2" data-sort-value="{{ strtolower(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) }}">
                                    <a href="{{ route('participants.show', $participant) }}" class="text-blue-700 hover:text-blue-800 font-semibold transition-colors duration-200">
                                        {{ $user->first_name ?? '' }} {{ $user->last_name ?? '' }}
                                    </a>
                                </td>
                                <td class="px-4 py-2" data-sort-value="{{ strtolower($user->email ?? '') }}">
                                    <button onclick="openEmailModal('{{ $user->email ?? '' }}', '{{ $user->first_name ?? '' }} {{ $user->last_name ?? '' }}')" class="text-blue-700 hover:text-blue-800 hover:underline cursor-pointer font-semibold transition-colors duration-200 border-none bg-transparent p-0">
                                        {{ $user->email ?? '' }}
                                    </button>
                                </td>
                                <td class="px-4 py-2" data-sort-value="{{ strtolower($participant->participantType->name ?? '') }}">{{ $participant->participantType->name ?? '' }}</td>
                                <td class="px-4 py-2" data-sort-value="{{ strtolower($participant->organization ?? 'zzz') }}">{{ $participant->organization ?? '-' }}</td>
                                <td class="px-4 py-2" data-sort-value="{{ $participant->registration_status === 'approved' ? 1 : ($participant->registration_status === 'pending' ? 2 : 3) }}">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $participant->registration_status == 'approved' ? 'bg-green-100 text-green-700 border border-green-200' : ($participant->registration_status == 'pending' ? 'bg-yellow-100 text-yellow-700 border border-yellow-200' : 'bg-red-100 text-red-700 border border-red-200') }}">
                                        {{ ucfirst($participant->registration_status ?? 'pending') }}
                                    </span>
                                </td>
                                <td class="px-4 py-2" data-sort-value="{{ $participant->visa_status === 'approved' ? 1 : ($participant->visa_status === 'pending' ? 2 : ($participant->visa_status === 'required' ? 3 : 4)) }}">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $participant->visa_status == 'approved' ? 'bg-green-100 text-green-700 border border-green-200' : ($participant->visa_status == 'pending' ? 'bg-yellow-100 text-yellow-700 border border-yellow-200' : ($participant->visa_status == 'required' ? 'bg-blue-100 text-blue-700 border border-blue-200' : 'bg-gray-100 text-gray-700 border border-gray-200')) }}">
                                        {{ ucfirst($participant->visa_status ?? 'not required') }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-500">No participants found.</p>
            @endif
        </div>
    </div>
    <div class="flex justify-end mt-6">
        <a href="{{ route('conferences.edit', $conference) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold mr-2">Edit</a>
        <form method="POST" action="{{ route('conferences.destroy', $conference) }}" onsubmit="return confirm('Are you sure you want to delete this conference?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold">Delete</button>
        </form>
    </div>
    <div class="mt-4">
        <a href="{{ route('conferences.index') }}" class="text-gray-600 hover:text-gray-900">Back to list</a>
    </div>
</div>

<!-- Email Modal -->
<div id="emailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/3 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Send Email</h3>
                <button onclick="closeEmailModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="emailForm" class="space-y-4">
                <div>
                    <label for="fromEmail" class="block text-sm font-medium text-gray-700 mb-1">From</label>
                    <input type="email" id="fromEmail" name="from" value="admin@conference.com" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent" readonly>
                </div>
                
                <div>
                    <label for="toEmail" class="block text-sm font-medium text-gray-700 mb-1">To</label>
                    <input type="email" id="toEmail" name="to" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent" readonly>
                </div>
                
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                    <input type="text" id="subject" name="subject" value="Conference Update - {{ $conference->name }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                </div>
                
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                    <textarea id="message" name="message" rows="8" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent" placeholder="Type your message here..."></textarea>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeEmailModal()" class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md font-medium transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit" id="sendButton" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-md font-medium transition-colors duration-200">
                        Send Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let sortDirection = 1; // 1 for ascending, -1 for descending
let lastSortedColumn = null;

// Email Modal Functions
function openEmailModal(email, name) {
    console.log('openEmailModal called with:', email, name);
    
    const modal = document.getElementById('emailModal');
    const toEmail = document.getElementById('toEmail');
    const message = document.getElementById('message');
    
    if (!modal) {
        console.error('Modal not found!');
        return;
    }
    
    // Set the recipient email
    toEmail.value = email;
    
    // Pre-fill message with greeting
    const greeting = `Dear ${name},\n\n`;
    const defaultMessage = `Thank you for your participation in our conference. We hope you find the sessions informative and engaging.\n\nBest regards,\nConference Team`;
    message.value = greeting + defaultMessage;
    
    // Show modal
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
    console.log('Modal should be visible now');
}

function closeEmailModal() {
    const modal = document.getElementById('emailModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto'; // Restore scrolling
}

// Handle form submission
document.addEventListener('DOMContentLoaded', function() {
    const emailForm = document.getElementById('emailForm');
    const sendButton = document.getElementById('sendButton');
    
    emailForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        const originalText = sendButton.textContent;
        sendButton.textContent = 'Sending...';
        sendButton.disabled = true;
        
        // Simulate email sending (placeholder for now)
        setTimeout(() => {
            // Show success message
            alert('Email sent successfully! (This is a placeholder - email functionality will be implemented later)');
            
            // Reset button
            sendButton.textContent = originalText;
            sendButton.disabled = false;
            
            // Close modal
            closeEmailModal();
        }, 1500);
    });
    
    // Close modal when clicking outside
    const modal = document.getElementById('emailModal');
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeEmailModal();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeEmailModal();
        }
    });
});

function sortTable(columnIndex) {
    const table = document.querySelector('table');
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    // Reset sort direction if clicking on a different column
    if (lastSortedColumn !== columnIndex) {
        sortDirection = 1;
        lastSortedColumn = columnIndex;
    } else {
        sortDirection *= -1; // Toggle direction
    }
    
    // Sort rows
    rows.sort((a, b) => {
        const aValue = a.cells[columnIndex].getAttribute('data-sort-value') || a.cells[columnIndex].textContent.trim();
        const bValue = b.cells[columnIndex].getAttribute('data-sort-value') || b.cells[columnIndex].textContent.trim();
        
        // Handle numeric sorting for serial numbers and status priorities
        if (columnIndex === 1 || columnIndex === 6 || columnIndex === 7) {
            const aNum = parseFloat(aValue) || 0;
            const bNum = parseFloat(bValue) || 0;
            return (aNum - bNum) * sortDirection;
        }
        
        // Handle string sorting
        return aValue.localeCompare(bValue) * sortDirection;
    });
    
    // Reorder rows in the table
    rows.forEach(row => tbody.appendChild(row));
    
    // Update sort indicators
    updateSortIndicators(columnIndex, sortDirection);
}

function updateSortIndicators(columnIndex, direction) {
    // Remove all sort indicators
    document.querySelectorAll('th svg').forEach(svg => {
        svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>';
    });
    
    // Add sort indicator to current column
    const currentTh = document.querySelector(`th:nth-child(${columnIndex + 1}) svg`);
    if (currentTh) {
        if (direction === 1) {
            currentTh.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>';
        } else {
            currentTh.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>';
        }
    }
}

// Checkbox functionality
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.querySelector('.participant-checkbox-all');
    const participantCheckboxes = document.querySelectorAll('.participant-checkbox');
    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            participantCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }
    
    // Update select all checkbox when individual checkboxes change
    participantCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedBoxes = document.querySelectorAll('.participant-checkbox:checked');
            const totalBoxes = participantCheckboxes.length;
            
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = checkedBoxes.length === totalBoxes;
                selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < totalBoxes;
            }
        });
    });
});
</script>
@endsection 