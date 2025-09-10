@extends('layouts.app')

@section('title', 'Passwordless Login Management')

@push('styles')
<style>
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .stat-item {
        text-align: center;
        padding: 10px;
    }
    .stat-number {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 5px;
    }
    .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }
    .action-btn {
        transition: all 0.3s ease;
    }
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Passwordless Login Management</h1>
        <p class="text-gray-600 mt-2">Generate secure login links for conference attendees and speakers</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="stats-card">
            <div class="stat-item">
                <div class="stat-number">{{ $stats['total_tokens'] }}</div>
                <div class="stat-label">Total Tokens</div>
            </div>
        </div>
        <div class="stats-card">
            <div class="stat-item">
                <div class="stat-number">{{ $stats['active_tokens'] }}</div>
                <div class="stat-label">Active Tokens</div>
            </div>
        </div>
        <div class="stats-card">
            <div class="stat-item">
                <div class="stat-number">{{ $stats['used_tokens'] }}</div>
                <div class="stat-label">Used Tokens</div>
            </div>
        </div>
        <div class="stats-card">
            <div class="stat-item">
                <div class="stat-number">{{ $stats['tokens_created_today'] }}</div>
                <div class="stat-label">Created Today</div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <button onclick="openGenerateModal()" 
                    class="action-btn bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Generate Single Link
            </button>
            
            <button onclick="openBulkModal()" 
                    class="action-btn bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Generate Bulk Links
            </button>
            
            <button onclick="cleanupExpired()" 
                    class="action-btn bg-orange-600 hover:bg-orange-700 text-white px-6 py-3 rounded-lg font-semibold flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Cleanup Expired
            </button>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Recent Login Activity</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expires</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentLogins as $login)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-sm font-medium text-gray-700">
                                            {{ substr($login->user->first_name, 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $login->user->first_name }} {{ $login->user->last_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $login->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($login->used_at)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Used
                                    </span>
                                @elseif($login->expires_at->isPast())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Expired
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Active
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $login->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $login->expires_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if(!$login->used_at && $login->expires_at->isFuture())
                                    <button onclick="copyToClipboard('{{ route('passwordless-login.verify', $login->token) }}')" 
                                            class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        Copy Link
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                No recent login activity
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Generate Single Link Modal -->
<div id="generateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">Generate Login Link</h3>
                <form id="generateForm">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Attendee/Speaker</label>
                        <select id="userSelect" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Loading participants...</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Expiration (hours)</label>
                        <input type="number" id="expirationHours" value="24" min="1" max="168" 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeGenerateModal()" 
                                class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Generate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Generate Modal -->
<div id="bulkModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">Generate Bulk Login Links</h3>
                <form id="bulkForm">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Attendees/Speakers</label>
                        <div id="participantList" class="max-h-60 overflow-y-auto border border-gray-300 rounded-md p-3">
                            <div class="text-center text-gray-500">Loading participants...</div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Expiration (hours)</label>
                        <input type="number" id="bulkExpirationHours" value="24" min="1" max="168" 
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeBulkModal()" 
                                class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                        <button type="submit" 
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Generate All</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Load participants for single link generation
async function loadParticipants() {
    try {
        const response = await fetch('{{ route("passwordless-login.participants") }}');
        const data = await response.json();
        
        if (data.success) {
            const select = document.getElementById('userSelect');
            select.innerHTML = '<option value="">Select a participant...</option>';
            data.data.forEach(participant => {
                const option = document.createElement('option');
                option.value = participant.id;
                option.textContent = `${participant.first_name} ${participant.last_name} (${participant.email})`;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading participants:', error);
    }
}

// Load participants for bulk generation
async function loadBulkParticipants() {
    try {
        const response = await fetch('{{ route("passwordless-login.participants") }}');
        const data = await response.json();
        
        if (data.success) {
            const container = document.getElementById('participantList');
            container.innerHTML = '';
            data.data.forEach(participant => {
                const div = document.createElement('div');
                div.className = 'flex items-center mb-2';
                div.innerHTML = `
                    <input type="checkbox" id="participant_${participant.id}" value="${participant.id}" 
                           class="mr-3 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="participant_${participant.id}" class="text-sm text-gray-700">
                        ${participant.first_name} ${participant.last_name} (${participant.email})
                    </label>
                `;
                container.appendChild(div);
            });
        }
    } catch (error) {
        console.error('Error loading participants:', error);
    }
}

// Modal functions
function openGenerateModal() {
    document.getElementById('generateModal').classList.remove('hidden');
    loadParticipants();
}

function closeGenerateModal() {
    document.getElementById('generateModal').classList.add('hidden');
}

function openBulkModal() {
    document.getElementById('bulkModal').classList.remove('hidden');
    loadBulkParticipants();
}

function closeBulkModal() {
    document.getElementById('bulkModal').classList.add('hidden');
}

// Form submissions
document.getElementById('generateForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('user_id', document.getElementById('userSelect').value);
    formData.append('expiration_hours', document.getElementById('expirationHours').value);
    
    try {
        const response = await fetch('{{ route("passwordless-login.generate") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Login link generated successfully!');
            closeGenerateModal();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while generating the link.');
    }
});

document.getElementById('bulkForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const selectedParticipants = Array.from(document.querySelectorAll('#participantList input[type="checkbox"]:checked'))
        .map(checkbox => checkbox.value);
    
    if (selectedParticipants.length === 0) {
        alert('Please select at least one participant.');
        return;
    }
    
    const formData = new FormData();
    formData.append('user_ids', JSON.stringify(selectedParticipants));
    formData.append('expiration_hours', document.getElementById('bulkExpirationHours').value);
    
    try {
        const response = await fetch('{{ route("passwordless-login.generate.bulk") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            closeBulkModal();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while generating the links.');
    }
});

// Cleanup expired tokens
async function cleanupExpired() {
    if (!confirm('Are you sure you want to cleanup expired tokens?')) {
        return;
    }
    
    try {
        const response = await fetch('{{ route("passwordless-login.cleanup") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while cleaning up expired tokens.');
    }
}

// Copy to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Link copied to clipboard!');
    }, function(err) {
        console.error('Could not copy text: ', err);
        alert('Failed to copy link to clipboard.');
    });
}
</script>
@endpush
