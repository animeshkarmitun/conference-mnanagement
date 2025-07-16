<!DOCTYPE html>
<html>
<head>
    <title>Gmail Conversations</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Gmail Conversations</h1>
        @if (session('success'))
            <div class="bg-green-100 text-green-800 p-2 rounded mb-4">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 text-red-800 p-2 rounded mb-4">{{ session('error') }}</div>
        @endif
        @if (isset($threads) && count($threads))
            <table class="min-w-full bg-white border border-gray-200 rounded">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b">Thread ID</th>
                        <th class="py-2 px-4 border-b">Snippet</th>
                        <th class="py-2 px-4 border-b">Messages</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($threads as $thread)
                        <tr>
                            <td class="py-2 px-4 border-b">{{ $thread['id'] }}</td>
                            <td class="py-2 px-4 border-b">{{ $thread['snippet'] }}</td>
                            <td class="py-2 px-4 border-b">
                                @foreach ($thread['messages'] as $message)
                                    <div class="mb-2 p-2 border rounded bg-gray-50">
                                        <p><strong>From:</strong> {{ app('App\\Services\\GoogleService')->getHeader($message, 'From') }}</p>
                                        <p><strong>Subject:</strong> {{ app('App\\Services\\GoogleService')->getHeader($message, 'Subject') }}</p>
                                        <p>{{ $message->getSnippet() }}</p>
                                    </div>
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No conversations found. Please connect your Gmail account.</p>
            <a href="{{ route('google.redirect') }}" class="btn btn-primary">Connect Gmail</a>
        @endif
    </div>
</body>
</html> 