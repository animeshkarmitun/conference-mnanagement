<div>
    <h3 class="text-lg font-semibold mb-4">Sessions</h3>
    @if(count($sessions))
        <ul class="divide-y divide-gray-200">
            @foreach($sessions as $session)
                <li class="py-3">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="font-semibold text-gray-800">{{ $session->title }}</div>
                            <div class="text-sm text-gray-500">{{ $session->start_time->format('M d, Y H:i') }} - {{ $session->end_time->format('H:i') }}</div>
                        </div>
                        <div class="text-xs px-3 py-1 rounded-full bg-blue-100 text-blue-700 font-semibold">
                            {{ $session->pivot->role ?? 'Participant' }}
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    @else
        <p class="text-gray-500">No sessions found for this participant.</p>
    @endif
</div> 