<div class="w-full min-h-96 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
    <h3 class="text-lg font-semibold mb-4">Comments</h3>
    
    <!-- Add Comment Form -->
    <form method="POST" action="{{ route('participants.comments.store', $participant) }}" class="mb-6">
        @csrf
        <div class="bg-gray-50 p-4 rounded-lg">
            <textarea 
                name="comment" 
                rows="3" 
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 @error('comment') border-red-500 @enderror" 
                placeholder="Add a comment..."
                required
            >{{ old('comment') }}</textarea>
            @error('comment')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <div class="flex justify-end mt-3">
                <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-2 rounded-lg font-semibold transition duration-200">
                    <i class="fas fa-comment mr-2"></i>Add Comment
                </button>
            </div>
        </div>
    </form>

    <!-- Comments List -->
    @if(count($comments) > 0)
        <div class="space-y-4">
            @foreach($comments as $comment)
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-yellow-600 text-sm"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2 mb-1">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $comment->user->first_name }} {{ $comment->user->last_name }}
                                </p>
                                @if($comment->user->hasRole('admin') || $comment->user->hasRole('superadmin'))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        Admin
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                        Participant
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600 mb-2">{{ $comment->content }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $comment->created_at->format('M d, Y \a\t H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-comments text-gray-400 text-xl"></i>
            </div>
            <p class="text-gray-500 text-lg">No comments yet</p>
            <p class="text-gray-400 text-sm mt-1">Be the first to add a comment!</p>
        </div>
    @endif

    <!-- Success Message -->
    @if(session('success'))
        <div class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-lg z-50" id="success-message">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        </div>
        <script>
            setTimeout(function() {
                document.getElementById('success-message').style.display = 'none';
            }, 5000);
        </script>
    @endif
</div> 