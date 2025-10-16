@extends('layouts.app')

@section('title', 'Conference Details')

@section('content')
<div class="max-w-7xl mx-auto">
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
                $speakers = $conference->participants->filter(function($participant) {
                    return $participant->participantType && $participant->participantType->category === 'presenter';
                });
            @endphp
            @if($speakers->count())
                <ul class="divide-y divide-gray-200">
                    @foreach($speakers as $speaker)
                        <li class="py-2">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="font-semibold">{{ $speaker->user->first_name ?? '' }} {{ $speaker->user->last_name ?? '' }}</span>
                                    <span class="text-sm text-gray-500"> ({{ $speaker->user->email ?? '' }})</span>
                                </div>
                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                                    {{ ucwords(str_replace('_', ' ', $speaker->participantType->name ?? '')) }}
                                </span>
                            </div>
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
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200 text-sm min-w-full table-fixed">
                    <thead>
                        <tr>
                            <th class="w-24 px-4 py-2 text-left cursor-pointer hover:bg-gray-50" onclick="sortTable(0)">
                                <div class="flex items-center">
                                    SERIAL NO.
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                    </svg>
                                </div>
                            </th>
                            <th class="w-48 px-4 py-2 text-left cursor-pointer hover:bg-gray-50" onclick="sortTable(1)">
                                <div class="flex items-center">
                                    NAME
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                    </svg>
                                </div>
                            </th>
                            <th class="w-64 px-4 py-2 text-left cursor-pointer hover:bg-gray-50" onclick="sortTable(2)">
                                <div class="flex items-center">
                                    EMAIL
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                    </svg>
                                </div>
                            </th>
                            <th class="w-32 px-4 py-2 text-left cursor-pointer hover:bg-gray-50" onclick="sortTable(3)">
                                <div class="flex items-center">
                                    TYPE
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                    </svg>
                                </div>
                            </th>
                            <th class="w-48 px-4 py-2 text-left cursor-pointer hover:bg-gray-50" onclick="sortTable(4)">
                                <div class="flex items-center">
                                    ORGANIZATION
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                    </svg>
                                </div>
                            </th>
                            <th class="w-24 px-4 py-2 text-left cursor-pointer hover:bg-gray-50" onclick="sortTable(5)">
                                <div class="flex items-center">
                                    STATUS
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                    </svg>
                                </div>
                            </th>
                            <th class="w-24 px-4 py-2 text-left cursor-pointer hover:bg-gray-50" onclick="sortTable(6)">
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
                                $serial = sprintf('CONF%04d-%03d', $conference->id, $i+1);
                                $serialParts = explode('-', $serial);
                                $conferenceNum = isset($serialParts[0]) ? intval(substr($serialParts[0], 4)) : 0;
                                $participantNum = isset($serialParts[1]) ? intval($serialParts[1]) : 0;
                                $serialSortValue = sprintf('%04d-%03d', $conferenceNum, $participantNum);
                            @endphp
                            <tr class="hover:bg-gray-50">
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
                </div>
            @else
                <p class="text-gray-500">No participants found.</p>
            @endif
        </div>
    </div>
    <div class="flex justify-end mt-6">
        <a href="{{ route('conferences.edit', $conference) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold mr-2">Edit</a>
        <button onclick="confirmConferenceDeletion({{ $conference->id }}, '{{ addslashes($conference->name) }}')" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold">Delete</button>
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
                    <input type="email" id="fromEmail" name="from" value="conferencescgs@gmail.com" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent" readonly>
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
        
        // Get form data
        const formData = new FormData(emailForm);
        const emailData = {
            to: formData.get('to'),
            subject: formData.get('subject'),
            message: formData.get('message'),
            conference_id: '{{ $conference->id ?? null }}'
        };
        
        // Send email via AJAX
        fetch('{{ route("participants.send-email") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(emailData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                alert('Email sent successfully!');
                
                // Close modal
                closeEmailModal();
            } else {
                // Show error message
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while sending the email. Please try again.');
        })
        .finally(() => {
            // Reset button
            sendButton.textContent = originalText;
            sendButton.disabled = false;
        });
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
        if (columnIndex === 0 || columnIndex === 5 || columnIndex === 6) {
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

// Conference deletion confirmation functions
function confirmConferenceDeletion(conferenceId, conferenceName) {
    // Show loading state
    const modal = document.getElementById('deleteConferenceModal');
    const modalContent = modal.querySelector('.modal-content');
    modalContent.innerHTML = `
        <div class="flex items-center justify-center p-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600"></div>
            <span class="ml-3 text-gray-600">Loading conference details...</span>
        </div>
    `;
    modal.classList.remove('hidden');
    
    // Fetch deletion info
    fetch(`/conferences/${conferenceId}/deletion-info`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        showDeletionConfirmation(data);
    })
    .catch(error => {
        console.error('Error fetching deletion info:', error);
        modalContent.innerHTML = `
            <div class="p-6">
                <div class="flex items-center justify-center text-red-600 mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Error Loading Details</h3>
                <p class="text-gray-600 mb-2">Unable to load conference deletion details.</p>
                <p class="text-sm text-gray-500 mb-6">Error: ${error.message}</p>
                <div class="flex justify-end">
                    <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        Cancel
                    </button>
                </div>
            </div>
        `;
    });
}

function showDeletionConfirmation(data) {
    const modalContent = document.getElementById('deleteConferenceModal').querySelector('.modal-content');
    const { conference, venue, related_data, participant_users_info, has_related_data } = data;
    
    let relatedDataHtml = '';
    if (has_related_data) {
        const items = [];
        if (related_data.participants > 0) items.push(`${related_data.participants} participant(s)`);
        if (related_data.sessions > 0) items.push(`${related_data.sessions} session(s)`);
        if (related_data.tasks > 0) items.push(`${related_data.tasks} task(s)`);
        if (related_data.notifications > 0) items.push(`${related_data.notifications} notification(s)`);
        if (related_data.communications > 0) items.push(`${related_data.communications} communication(s)`);
        if (related_data.checkins > 0) items.push(`${related_data.checkins} checkin(s)`);
        if (related_data.conference_docs > 0) items.push(`${related_data.conference_docs} document(s)`);
        
        relatedDataHtml = `
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <div>
                        <h4 class="font-semibold text-yellow-800 mb-2">Warning: This action will also delete:</h4>
                        <ul class="text-yellow-700 space-y-1">
                            ${items.map(item => `<li>• ${item}</li>`).join('')}
                        </ul>
                    </div>
                </div>
            </div>
        `;
    }
    
    modalContent.innerHTML = `
        <div class="p-6">
            <div class="flex items-center justify-center text-red-600 mb-4">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Delete Conference</h3>
            <p class="text-gray-600 mb-4">
                Are you sure you want to delete the conference <strong>"${conference.name}"</strong>?
            </p>
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-700">Start Date:</span>
                        <span class="text-gray-600">${new Date(conference.start_date).toLocaleDateString()}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">End Date:</span>
                        <span class="text-gray-600">${new Date(conference.end_date).toLocaleDateString()}</span>
                    </div>
                </div>
            </div>
            ${venue ? `
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <div class="flex-1">
                            <h4 class="font-semibold text-blue-800 mb-2">Venue Information</h4>
                            <div class="space-y-1 text-sm">
                                <div><span class="font-medium text-blue-700">Venue:</span> <span class="text-blue-600">${venue.name}</span></div>
                                <div><span class="font-medium text-blue-700">Address:</span> <span class="text-blue-600">${venue.address || 'Not specified'}</span></div>
                                <div><span class="font-medium text-blue-700">Capacity:</span> <span class="text-blue-600">${venue.capacity || 'Not specified'}</span></div>
                                ${venue.other_conferences_count > 0 ? `
                                    <div class="mt-2 p-2 bg-blue-100 rounded">
                                        <div class="font-medium text-blue-800">⚠️ This venue is also used by ${venue.other_conferences_count} other conference(s):</div>
                                        <ul class="mt-1 text-blue-700 text-xs">
                                            ${venue.other_conferences.map(conf => `<li>• ${conf.name} (${new Date(conf.start_date).toLocaleDateString()} - ${new Date(conf.end_date).toLocaleDateString()})</li>`).join('')}
                                        </ul>
                                    </div>
                                ` : `
                                    <div class="mt-2 p-2 bg-green-100 rounded">
                                        <div class="font-medium text-green-800">✓ This venue is only used by this conference</div>
                                        <div class="mt-2">
                                            <label class="flex items-center">
                                                <input type="checkbox" id="deleteVenueCheckbox" name="delete_venue" value="1" class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-500 focus:ring-red-500">
                                                <span class="ml-2 text-sm text-green-700 font-medium">Also delete this venue</span>
                                            </label>
                                            <p class="text-xs text-green-600 mt-1">⚠️ This will permanently delete the venue and cannot be undone.</p>
                                        </div>
                                    </div>
                                `}
                            </div>
                        </div>
                    </div>
                </div>
            ` : ''}
            ${participant_users_info && participant_users_info.single_participant_users > 0 ? `
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-orange-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <div class="flex-1">
                            <h4 class="font-semibold text-orange-800 mb-2">Participant User Deletion</h4>
                            <div class="text-sm text-orange-700 mb-3">
                                ${participant_users_info.single_participant_users} user(s) have only one participant profile (this conference only):
                            </div>
                            <div class="space-y-2 mb-3">
                                ${participant_users_info.single_participant_users_list.map(user => `
                                    <div class="text-xs bg-orange-100 p-2 rounded">
                                        <span class="font-medium">${user.user_name}</span> (${user.user_email})
                                    </div>
                                `).join('')}
                            </div>
                            <label class="flex items-center">
                                <input type="checkbox" id="deleteParticipantUsersCheckbox" name="delete_participant_users" value="1" class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-500 focus:ring-red-500">
                                <span class="ml-2 text-sm text-orange-700 font-medium">Also delete these user accounts</span>
                            </label>
                            <p class="text-xs text-orange-600 mt-1">⚠️ This will permanently delete the user accounts and cannot be undone.</p>
                        </div>
                    </div>
                </div>
            ` : ''}
            ${relatedDataHtml}
            <div class="flex justify-end space-x-3">
                <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    Cancel
                </button>
                <button onclick="deleteConference(${conference.id})" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Delete Conference
                </button>
            </div>
        </div>
    `;
}

function deleteConference(conferenceId) {
    // Create and submit the form
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/conferences/${conferenceId}`;
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    
    const methodField = document.createElement('input');
    methodField.type = 'hidden';
    methodField.name = '_method';
    methodField.value = 'DELETE';
    
    // Add venue deletion checkbox value if checked
    const deleteVenueCheckbox = document.getElementById('deleteVenueCheckbox');
    if (deleteVenueCheckbox && deleteVenueCheckbox.checked) {
        const deleteVenueField = document.createElement('input');
        deleteVenueField.type = 'hidden';
        deleteVenueField.name = 'delete_venue';
        deleteVenueField.value = '1';
        form.appendChild(deleteVenueField);
    }
    
    // Add participant user deletion checkbox value if checked
    const deleteParticipantUsersCheckbox = document.getElementById('deleteParticipantUsersCheckbox');
    if (deleteParticipantUsersCheckbox && deleteParticipantUsersCheckbox.checked) {
        const deleteParticipantUsersField = document.createElement('input');
        deleteParticipantUsersField.type = 'hidden';
        deleteParticipantUsersField.name = 'delete_participant_users';
        deleteParticipantUsersField.value = '1';
        form.appendChild(deleteParticipantUsersField);
    }
    
    form.appendChild(csrfToken);
    form.appendChild(methodField);
    document.body.appendChild(form);
    form.submit();
}

function closeDeleteModal() {
    document.getElementById('deleteConferenceModal').classList.add('hidden');
}

</script>

<!-- Conference Deletion Confirmation Modal -->
<div id="deleteConferenceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="modal-content">
            <!-- Content will be dynamically loaded here -->
        </div>
    </div>
</div>

@endsection 