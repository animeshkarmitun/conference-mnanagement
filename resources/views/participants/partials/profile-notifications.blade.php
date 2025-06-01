<div>
    <h3 class="text-lg font-semibold mb-4">Notifications</h3>
    @if(count($notifications))
        <ul class="divide-y divide-gray-200">
            @foreach($notifications as $notification)
                <li class="py-3">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="font-semibold text-gray-800">{{ $notification->message }}</div>
                            <div class="text-xs text-gray-500">Type: {{ $notification->type }} | {{ $notification->created_at->format('M d, Y H:i') }}</div>
                        </div>
                        <div>
                            <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold {{ $notification->read_status ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ $notification->read_status ? 'Read' : 'Unread' }}
                            </span>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    @else
        <p class="text-gray-500">No notifications found for this participant.</p>
    @endif
</div> 