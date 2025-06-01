<div>
    <h3 class="text-lg font-semibold mb-4">Comments</h3>
    <form method="POST" action="{{ route('participants.comments.store', $participant) }}" class="mb-6">
        @csrf
        <textarea name="comment" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500" placeholder="Add a comment..."></textarea>
        <div class="flex justify-end mt-2">
            <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-semibold">Add Comment</button>
        </div>
    </form>
    @if(count($comments))
        <ul class="divide-y divide-gray-200">
            @foreach($comments as $comment)
                <li class="py-3">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="text-gray-800">{{ $comment->content }}</div>
                            <div class="text-xs text-gray-500">By {{ $comment->user->name }} on {{ $comment->created_at->format('M d, Y H:i') }}</div>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    @else
        <p class="text-gray-500">No comments yet.</p>
    @endif
</div> 