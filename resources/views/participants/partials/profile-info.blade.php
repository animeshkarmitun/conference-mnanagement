<form method="POST" action="{{ route('participants.update', $participant) }}" enctype="multipart/form-data" class="space-y-4">
    @csrf
    @method('PUT')
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">First Name</label>
            <input type="text" name="first_name" value="{{ old('first_name', $participant->user->first_name ?? $participant->user->name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Last Name</label>
            <input type="text" name="last_name" value="{{ old('last_name', $participant->user->last_name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Email</label>
        <input type="email" name="email" value="{{ old('email', $participant->user->email) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Organization</label>
        <input type="text" name="organization" value="{{ old('organization', $participant->organization) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Dietary Needs</label>
        <input type="text" name="dietary_needs" value="{{ old('dietary_needs', $participant->dietary_needs) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Profile Picture</label>
        <input type="file" name="profile_picture" class="mt-1 block w-full text-sm text-gray-500">
        @if($participant->user->profile_picture)
            <img src="{{ asset('storage/' . $participant->user->profile_picture) }}" alt="Profile Picture" class="mt-2 w-24 h-24 rounded-full object-cover">
        @endif
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Resume/CV</label>
        <input type="file" name="resume" class="mt-1 block w-full text-sm text-gray-500">
        @if($participant->user->resume)
            <a href="{{ asset('storage/' . $participant->user->resume) }}" target="_blank" class="text-blue-600 hover:underline mt-2 block">View Resume</a>
        @endif
    </div>
    <div class="flex justify-end">
        <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold">Update Info</button>
    </div>
</form> 