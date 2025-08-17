<div>
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold">My Sessions</h3>
    </div>

    @php
        $sessionCount = count($sessions ?? []);
        echo "<script>console.log('Sessions count: $sessionCount');</script>";
    @endphp
    @if($sessionCount)
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <ul class="divide-y divide-gray-200">
                @foreach($sessions as $session)
                    <li class="p-4 hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex justify-between items-center">
                            <div class="flex-1">
                                <div class="font-semibold text-gray-800">{{ $session->title }}</div>
                                <div class="text-sm text-gray-500 mt-1">
                                    <span class="inline-flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ \Carbon\Carbon::parse($session->start_time)->format('M d, Y H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                                    </span>
                                </div>
                                @if($session->description)
                                    <div class="text-sm text-gray-600 mt-2">{{ Str::limit($session->description, 100) }}</div>
                                @endif
                                @if($session->room)
                                    <div class="text-sm text-gray-500 mt-1">
                                        <span class="inline-flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                            {{ $session->room }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="text-xs px-3 py-1 rounded-full bg-blue-100 text-blue-700 font-semibold">
                                    {{ $session->pivot->role ?? 'Participant' }}
                                </span>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        <div class="text-center py-12 bg-gray-50 rounded-lg">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No sessions assigned</h3>
            <p class="mt-1 text-sm text-gray-500">You haven't been assigned to any sessions yet. Contact the conference organizers for session assignments.</p>
        </div>
    @endif
</div> 