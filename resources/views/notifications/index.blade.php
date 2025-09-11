@extends(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('admin') || auth()->user()->hasRole('tasker') || auth()->user()->hasRole('event_coordinator') ? 'layouts.app' : 'layouts.participant')

@section('title', 'Notifications')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">Notifications</h2>
    <div class="flex space-x-2">
        <button onclick="markAllAsRead()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            Mark All as Read
        </button>
        <a href="{{ route('notifications.create') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            Create Notification
        </a>
    </div>
</div>
<div class="bg-white rounded-xl shadow p-6">
    <ul class="divide-y divide-gray-200">
        @forelse($notifications ?? [] as $notification)
            <li class="py-4 flex items-center justify-between cursor-pointer hover:bg-gray-50 transition-colors duration-200 rounded-lg px-3"
                onclick="handleNotificationClick({{ $notification->id }}, '{{ route('notifications.action', $notification->id) }}')"
                title="Click to view related content">
                <div class="flex-1">
                    <p class="font-medium text-gray-900">{{ $notification->message }}</p>
                    <p class="text-xs text-gray-500">
                        {{ $notification->created_at->diffForHumans() }}
                        <span class="text-gray-400">•</span>
                        {{ $notification->created_at->format('M j, Y \a\t g:i A') }}
                    </p>
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
function handleNotificationClick(notificationId, actionUrl) {
    console.log('Notification clicked:', notificationId, actionUrl);
    
    // Mark notification as read
    markNotificationAsRead(notificationId);
    
    fetch(`/notifications/${notificationId}/data`)
        .then(response => response.json())
        .then(notification => {
            console.log('Notification data:', notification);
            
            if (notification.related_model === 'Task' && notification.related_id) {
                const taskUrl = `/tasks/${notification.related_id}`;
                console.log('Navigating to task:', taskUrl);
                window.location.href = taskUrl;
            } else if (notification.related_model === 'Participant' && notification.related_id) {
                const participantUrl = `/participants/${notification.related_id}`;
                console.log('Navigating to participant:', participantUrl);
                window.location.href = participantUrl;
            } else if (notification.related_model === 'Session' && notification.related_id) {
                const sessionUrl = `/sessions/${notification.related_id}`;
                console.log('Navigating to session:', sessionUrl);
                window.location.href = sessionUrl;
            } else if (notification.related_model === 'Conference' && notification.related_id) {
                const conferenceUrl = `/conferences/${notification.related_id}`;
                console.log('Navigating to conference:', conferenceUrl);
                window.location.href = conferenceUrl;
            } else if (notification.action_url) {
                console.log('Using action URL:', notification.action_url);
                window.location.href = notification.action_url;
            } else {
                console.log('No navigation logic for this notification type:', notification.type);
                window.location.href = '/dashboard';
            }
        })
        .catch(error => {
            console.error('Error fetching notification:', error);
            window.location.href = actionUrl;
        });
}

function markNotificationAsRead(notificationId) {
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
            const notificationElement = document.querySelector(`li[onclick*="${notificationId}"]`);
            if (notificationElement) {
                const statusBadge = notificationElement.querySelector('.bg-yellow-100');
                if (statusBadge) {
                    statusBadge.className = 'inline-block px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700';
                    statusBadge.textContent = 'Read';
                }
                
                const unreadDot = notificationElement.querySelector('.bg-blue-500');
                if (unreadDot) {
                    unreadDot.remove();
                }
            }
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}

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
});
</script>
@endpush
@endsection 