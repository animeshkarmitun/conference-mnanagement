<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Participant Conversations - CGS Events</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .conversation-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        .message-bubble {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .message-bubble-reply {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Participant Conversations</h1>
                        <p class="text-sm text-gray-600">Email communications with conference participants</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <i class="fas fa-circle text-xs mr-2"></i>
                        Connected to Gmail
                    </span>
                    <button onclick="window.location.href='{{ route('gmail.index') }}'" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-sync-alt mr-2"></i>
                        Refresh
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Flash Messages -->
        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Gmail API cache note -->
        <div class="mb-4 text-sm text-yellow-700 bg-yellow-100 border border-yellow-200 rounded p-3 flex items-center">
            <i class="fas fa-info-circle mr-2"></i>
            Gmail may cache results for up to a minute. If you don't see new emails immediately, please wait and try again.
        </div>

        <!-- Search/filter form -->
        <form method="GET" action="{{ route('gmail.index') }}" class="mb-6 flex flex-col sm:flex-row items-center gap-3">
            <input type="text" name="q" value="{{ $searchQuery ?? '' }}" placeholder="Search (e.g. from:someone, after:2024/07/01)" class="w-full sm:w-80 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" />
            <input type="hidden" name="maxResults" value="{{ $maxResults }}">
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-search mr-2"></i>
                Search
            </button>
            @if (!empty($searchQuery))
                <a href="{{ route('gmail.index') }}" class="text-sm text-gray-500 hover:underline ml-2">Clear</a>
            @endif
        </form>

        <!-- Conversations List -->
        @if (isset($threads) && count($threads))
            <div class="grid gap-6">
                @foreach ($threads as $thread)
                    <div class="conversation-card bg-white rounded-lg shadow-sm border border-gray-200 p-6 transition-all duration-200">
                        <!-- Conversation Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full flex items-center justify-center">
                                    <i class="fas fa-envelope text-white text-sm"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ app('App\\Services\\GoogleService')->getHeader($thread['messages'][0], 'Subject') ?: 'No Subject' }}
                                    </h3>
                                    <p class="text-sm text-gray-600">
                                        Thread ID: <span class="font-mono text-xs">{{ substr($thread['id'], 0, 8) }}...</span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ count($thread['messages']) }} messages
                                </span>
                                <button onclick="toggleThread('{{ $thread['id'] }}')" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-chevron-down" id="icon-{{ $thread['id'] }}"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Conversation Snippet -->
                        <div class="mb-4">
                            <p class="text-gray-700 leading-relaxed">{{ $thread['snippet'] }}</p>
                        </div>

                        <!-- Messages (Collapsible) -->
                        <div id="messages-{{ $thread['id'] }}" class="hidden space-y-3">
                            @foreach ($thread['messages'] as $index => $message)
                                <div class="message-bubble rounded-lg p-4 text-white {{ $index % 2 == 0 ? '' : 'message-bubble-reply ml-8' }}">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-6 h-6 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                                <i class="fas fa-user text-xs"></i>
                                            </div>
                                            <span class="font-medium">
                                                {{ app('App\\Services\\GoogleService')->getHeader($message, 'From') }}
                                            </span>
                                        </div>
                                        <span class="text-xs opacity-75">
                                            {{ $message->getInternalDate() ? date('M j, Y g:i A', $message->getInternalDate() / 1000) : 'Unknown date' }}
                                        </span>
                                    </div>
                                    <div class="text-sm leading-relaxed">
                                        <p class="font-semibold mb-1">
                                            {{ app('App\\Services\\GoogleService')->getHeader($message, 'Subject') }}
                                        </p>
                                        <p>{{ $message->getSnippet() }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                            <div class="flex items-center space-x-4 text-sm text-gray-600">
                                <span class="flex items-center">
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ $thread['messages'][0]->getInternalDate() ? date('M j, Y', $thread['messages'][0]->getInternalDate() / 1000) : 'Unknown date' }}
                                </span>
                                <span class="flex items-center">
                                    <i class="fas fa-user mr-1"></i>
                                    {{ app('App\\Services\\GoogleService')->getHeader($thread['messages'][0], 'From') }}
                                </span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('gmail.reply', $thread['id']) }}" 
                                   class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <i class="fas fa-reply mr-1"></i>
                                    Reply
                                </a>
                                <button class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <i class="fas fa-archive mr-1"></i>
                                    Archive
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination or Load More -->
            @if (!empty($nextPageToken))
            <div class="mt-8 text-center">
                <form method="GET" action="{{ route('gmail.index') }}">
                    <input type="hidden" name="pageToken" value="{{ $nextPageToken }}">
                    <input type="hidden" name="maxResults" value="{{ $maxResults }}">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-plus mr-2"></i>
                        Load More Conversations
                    </button>
                </form>
            </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-envelope text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No conversations found</h3>
                <p class="text-gray-600 mb-6">Connect your Gmail account to view participant conversations and email communications.</p>
                <a href="{{ route('google.redirect') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fab fa-google mr-2"></i>
                    Connect Gmail Account
                </a>
            </div>
        @endif
    </main>

    <script>
        function toggleThread(threadId) {
            const messagesDiv = document.getElementById(`messages-${threadId}`);
            const icon = document.getElementById(`icon-${threadId}`);
            
            if (messagesDiv.classList.contains('hidden')) {
                messagesDiv.classList.remove('hidden');
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            } else {
                messagesDiv.classList.add('hidden');
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            }
        }

        function refreshConversations() {
            window.location.reload();
        }
    </script>
</body>
</html> 