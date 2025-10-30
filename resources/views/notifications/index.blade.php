@extends(auth()->user()->hasAnyRole() ? 'layouts.app' : 'layouts.participant')

@section('title', 'Notifications')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">
        @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('admin'))
            @if($type === 'admin')
                Admin Notifications
            @else
                User Notifications
            @endif
        @else
            My Notifications
        @endif
    </h2>
    <div class="flex space-x-2">
        @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('admin'))
            <button onclick="markAllAsRead()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Mark All as Read
            </button>
            <a href="{{ route('notifications.create') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Create Notification
            </a>
        @else
            <button onclick="markAllAsRead()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Mark All as Read
            </button>
        @endif
    </div>
</div>

@if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('admin'))
<div class="bg-white rounded-xl shadow p-4 mb-6">
    <div class="flex items-center space-x-4">
        <div class="flex space-x-2">
            <a href="{{ route('notifications.index', ['type' => 'admin']) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $type === 'admin' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                Admin Notifications
            </a>
            <a href="{{ route('notifications.index', ['type' => 'user']) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $type === 'user' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                User Notifications
            </a>
        </div>
        <div class="flex-1">
            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Notifications</label>
            <input type="text" id="search" placeholder="Search by message, user, or conference..." 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select id="status-filter" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All</option>
                <option value="unread">Unread</option>
                <option value="read">Read</option>
            </select>
        </div>
        <div>
            <label for="type-filter" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
            <select id="type-filter" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Types</option>
                <option value="General">General</option>
                <option value="TaskUpdate">Task Update</option>
                <option value="ConferenceUpdate">Conference Update</option>
                <option value="SessionUpdate">Session Update</option>
                <option value="TravelUpdate">Travel Update</option>
                <option value="ProfileUpdate">Profile Update</option>
                <option value="MissingDocuments">Missing Documents</option>
            </select>
        </div>
    </div>
</div>
@else
<div class="bg-white rounded-xl shadow p-4 mb-6">
    <div class="flex items-center space-x-4">
        <div class="flex-1">
            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Notifications</label>
            <input type="text" id="search" placeholder="Search by message..." 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select id="status-filter" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All</option>
                <option value="unread">Unread</option>
                <option value="read">Read</option>
            </select>
        </div>
    </div>
</div>
@endif

<div class="bg-white rounded-xl shadow p-6">
    <ul class="divide-y divide-gray-200">
        @forelse($notifications ?? [] as $notification)
            <li class="py-4 flex items-center justify-between {{ (auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('admin')) ? 'cursor-pointer hover:bg-gray-50' : 'cursor-default' }} transition-colors duration-200 rounded-lg px-3"
                @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('admin'))
                    onclick="markNotificationAsRead({{ $notification->id }})"
                @endif
                data-notification-id="{{ $notification->id }}"
                title="{{ (auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('admin')) ? 'Click to mark as read' : 'View only' }}">
                <div class="flex-1">
                    <p class="font-medium text-gray-900">{{ $notification->message }}</p>
                    <div class="text-xs text-gray-500 mt-1">
                        <p>
                        {{ $notification->created_at->diffForHumans() }}
                        <span class="text-gray-400">•</span>
                        {{ $notification->created_at->format('M j, Y \a\t g:i A') }}
                    </p>
                        @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('admin'))
                            <p class="mt-1">
                                <span class="font-medium">To:</span> 
                                @if($notification->user)
                                    {{ $notification->user->first_name }} {{ $notification->user->last_name }} ({{ $notification->user->email }})
                                    @if($notification->user->roles->count() > 0)
                                        <span class="inline-block px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-full ml-2">
                                            {{ $notification->user->roles->first()->name }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-red-500">User not found (ID: {{ $notification->user_id }})</span>
                                @endif
                                @if($notification->conference)
                                    <span class="text-gray-400">•</span>
                                    <span class="font-medium">Conference:</span> {{ $notification->conference->name }}
                                @endif
                                <span class="text-gray-400">•</span>
                                <span class="font-medium">Type:</span> {{ $notification->type }}
                            </p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    @if(!$notification->read_status)
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                    @endif
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $notification->read_status ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ $notification->read_status ? 'Read' : 'Unread' }}
                    </span>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </li>
        @empty
            <li class="py-4 text-center text-gray-500">No notifications found.</li>
        @endforelse
    </ul>
    
    @if($notifications->hasPages())
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>

function markAllAsRead() {
    if (confirm('Are you sure you want to mark all notifications as read?')) {
        fetch('/notifications/mark-all-read', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload the page to show updated status
                window.location.reload();
            } else {
                alert('Failed to mark all notifications as read');
            }
        })
        .catch(error => {
            console.error('Error marking all notifications as read:', error);
            alert('Failed to mark all notifications as read');
        });
    }
}

// Add hover effects for better UX
document.addEventListener('DOMContentLoaded', function() {
    const notificationItems = document.querySelectorAll('li[onclick]');
    
    notificationItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(2px)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });

    // Admin filtering functionality
    @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('admin'))
    const searchInput = document.getElementById('search');
    const statusFilter = document.getElementById('status-filter');
    const typeFilter = document.getElementById('type-filter');
    const notificationList = document.querySelector('.bg-white.rounded-xl.shadow.p-6 ul');

    function filterNotifications() {
        if (!searchInput || !statusFilter || !typeFilter || !notificationList) {
            return;
        }
        
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;
        const typeValue = typeFilter.value;
        
        const notifications = notificationList.querySelectorAll('li.py-4');
        let visibleCount = 0;
        
        notifications.forEach((notification, index) => {
            const message = notification.querySelector('.font-medium');
            if (!message) {
                return;
            }
            
            const messageText = message.textContent.toLowerCase();
            const allText = notification.textContent.toLowerCase();
            
            // Find status badge - look for both possible selectors
            const statusBadge = notification.querySelector('.bg-yellow-100, .bg-green-100, [class*="yellow-100"], [class*="green-100"]');
            const statusText = statusBadge ? statusBadge.textContent.trim() : '';
            const isUnread = statusText === 'Unread';
            const isRead = statusText === 'Read';
            
            // Skip filtering for empty status text (like "No notifications found" message)
            if (statusText === '') {
                return;
            }
            
            // Find type information - look for "Type:" text
            const typeText = allText.includes('type:') ? 
                allText.split('type:')[1].split('•')[0].trim() : '';
            
            // Check search term
            const matchesSearch = searchTerm === '' || 
                messageText.includes(searchTerm) || 
                allText.includes(searchTerm);
            
            // Check status filter
            const matchesStatus = statusValue === '' || 
                (statusValue === 'unread' && isUnread) || 
                (statusValue === 'read' && isRead);
            
            // Check type filter
            const matchesType = typeValue === '' || 
                typeText.toLowerCase().includes(typeValue.toLowerCase());
            
            const shouldShow = matchesSearch && matchesStatus && matchesType;
            
            if (shouldShow) {
                notification.style.display = 'flex';
                visibleCount++;
            } else {
                notification.style.display = 'none';
            }
        });
        
        
        // Show/hide "No notifications found" message
        const noNotificationsMsg = document.querySelector('.no-notifications-msg');
        
        if (visibleCount === 0 && notifications.length > 0) {
            if (!noNotificationsMsg) {
                const noMsg = document.createElement('li');
                noMsg.className = 'py-4 text-center text-gray-500 no-notifications-msg';
                noMsg.textContent = 'No notifications match your filters.';
                notificationList.appendChild(noMsg);
            }
        } else if (noNotificationsMsg) {
            noNotificationsMsg.remove();
        }
    }

    // Add event listeners
    if (searchInput) {
        searchInput.addEventListener('input', filterNotifications);
    }
    if (statusFilter) {
        statusFilter.addEventListener('change', filterNotifications);
    }
    if (typeFilter) {
        typeFilter.addEventListener('change', filterNotifications);
    }
    @else
    // Regular user filtering functionality
    const searchInput = document.getElementById('search');
    const statusFilter = document.getElementById('status-filter');
    const notificationList = document.querySelector('.bg-white.rounded-xl.shadow.p-6 ul');

    function filterNotifications() {
        if (!searchInput || !statusFilter || !notificationList) {
            return;
        }
        
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;
        
        const notifications = notificationList.querySelectorAll('li.py-4');
        let visibleCount = 0;
        
        notifications.forEach((notification, index) => {
            const message = notification.querySelector('.font-medium');
            if (!message) {
                return;
            }
            
            const messageText = message.textContent.toLowerCase();
            const allText = notification.textContent.toLowerCase();
            
            // Find status badge - look for both possible selectors
            const statusBadge = notification.querySelector('.bg-yellow-100, .bg-green-100, [class*="yellow-100"], [class*="green-100"]');
            const statusText = statusBadge ? statusBadge.textContent.trim() : '';
            const isUnread = statusText === 'Unread';
            const isRead = statusText === 'Read';
            
            // Skip filtering for empty status text (like "No notifications found" message)
            if (statusText === '') {
                return;
            }
            
            // Check search term
            const matchesSearch = searchTerm === '' || 
                messageText.includes(searchTerm) || 
                allText.includes(searchTerm);
            
            // Check status filter
            const matchesStatus = statusValue === '' || 
                (statusValue === 'unread' && isUnread) || 
                (statusValue === 'read' && isRead);
            
            const shouldShow = matchesSearch && matchesStatus;
            
            if (shouldShow) {
                notification.style.display = 'flex';
                visibleCount++;
            } else {
                notification.style.display = 'none';
            }
        });
        
        
        // Show/hide "No notifications found" message
        const noNotificationsMsg = document.querySelector('.no-notifications-msg');
        
        if (visibleCount === 0 && notifications.length > 0) {
            if (!noNotificationsMsg) {
                const noMsg = document.createElement('li');
                noMsg.className = 'py-4 text-center text-gray-500 no-notifications-msg';
                noMsg.textContent = 'No notifications match your filters.';
                notificationList.appendChild(noMsg);
            }
        } else if (noNotificationsMsg) {
            noNotificationsMsg.remove();
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterNotifications);
    }
    if (statusFilter) {
        statusFilter.addEventListener('change', filterNotifications);
    }
    @endif
});
</script>


<script>
// Function to mark notification as read (for admin/superadmin only)
function markNotificationAsRead(notificationId) {
    // Check if user is superadmin trying to read admin/user notifications
    const isSuperAdmin = @json(auth()->user()->hasRole('superadmin'));
    const currentType = new URLSearchParams(window.location.search).get('type');
    
    if (isSuperAdmin && (currentType === 'admin' || currentType === 'user')) {
        alert('You cannot mark admin/user notifications as read. Only the recipient can mark their own notifications as read.');
        return;
    }
    
    fetch(`/notifications/${notificationId}/read`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the UI to show notification as read
            const notificationElement = document.querySelector(`li[data-notification-id="${notificationId}"]`);
            
            if (notificationElement) {
                // Update status badge
                const statusBadge = notificationElement.querySelector('.bg-yellow-100, .bg-green-100, [class*="yellow-100"], [class*="green-100"]');
                if (statusBadge) {
                    statusBadge.className = 'inline-block px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700';
                    statusBadge.textContent = 'Read';
                }
                
                // Remove unread dot
                const unreadDot = notificationElement.querySelector('.bg-blue-500');
                if (unreadDot) {
                    unreadDot.remove();
                }
            }
        } else {
            console.error('Failed to mark notification as read');
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}
</script>
@endpush
@endsection 