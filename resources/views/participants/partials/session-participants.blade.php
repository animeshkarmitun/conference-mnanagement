@php
    $allParticipants = $session->participants ?? collect();

    $formatParticipantName = static function ($participant) {
        $first = $participant->user->first_name ?? '';
        $last = $participant->user->last_name ?? '';
        $fullName = trim($first . ' ' . $last);

        return $fullName !== '' ? $fullName : 'Unnamed Participant';
    };

    $resolveDesignation = static function ($participant) {
        return $participant->designation
            ?? $participant->user->designation
            ?? $participant->user->current_designation
            ?? 'Designation not provided';
    };

    $resolveOrganization = static function ($participant) {
        return $participant->organization
            ?? $participant->user->organization_institution
            ?? $participant->user->organization
            ?? null;
    };
@endphp

<div class="mt-4">
    <span class="text-sm font-semibold text-gray-700 flex items-center">
        <i class="fas fa-users text-blue-500 mr-2"></i>
        Participants in This Session
    </span>

    @if($allParticipants->isNotEmpty())
        <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach($allParticipants as $sessionParticipant)
                @php
                    $isModerator = $sessionParticipant->pivot->role === 'moderator';
                @endphp
                <div class="border border-gray-200 rounded-lg p-4 bg-white shadow-sm">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-sm font-semibold text-gray-900">
                            {{ $formatParticipantName($sessionParticipant) }}
                        </p>
                        @if($isModerator)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-microphone mr-1"></i>
                                Moderator
                            </span>
                        @endif
                    </div>

                    <p class="text-xs text-gray-600 mt-1">
                        {{ $resolveDesignation($sessionParticipant) }}
                    </p>

                    @php
                        $organization = $resolveOrganization($sessionParticipant);
                    @endphp

                    @if($organization)
                        <p class="text-xs text-gray-600 mt-1">{{ $organization }}</p>
                    @endif

                    @if(optional($sessionParticipant->user)->email)
                        <p class="text-xs text-blue-600 mt-2 flex items-center">
                            <i class="fas fa-envelope mr-1"></i>
                            <a href="mailto:{{ $sessionParticipant->user->email }}" class="hover:text-blue-800">
                                {{ $sessionParticipant->user->email }}
                            </a>
                        </p>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <p class="text-sm text-gray-500 mt-2">
            No participants are assigned to this session yet.
        </p>
    @endif
</div>


