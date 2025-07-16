<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reply to Email - CGS Events</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#email-body',
            height: 300,
            menubar: false,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | ' +
                'bold italic forecolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
        });
    </script>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('gmail.index') }}" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Reply to Email</h1>
                        <p class="text-sm text-gray-600">Compose your response</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <i class="fas fa-circle text-xs mr-2"></i>
                        Connected to Gmail
                    </span>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Flash Messages -->
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

        <!-- Original Message Preview -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Original Message</h3>
            <div class="space-y-3">
                <div class="flex items-center space-x-4 text-sm">
                    <span class="font-medium text-gray-700">From:</span>
                    <span class="text-gray-900">{{ app('App\\Services\\GoogleService')->getHeader($originalMessage, 'From') }}</span>
                </div>
                <div class="flex items-center space-x-4 text-sm">
                    <span class="font-medium text-gray-700">Subject:</span>
                    <span class="text-gray-900">{{ $originalSubject }}</span>
                </div>
                <div class="flex items-center space-x-4 text-sm">
                    <span class="font-medium text-gray-700">Date:</span>
                    <span class="text-gray-900">
                        {{ $originalMessage->getInternalDate() ? date('M j, Y g:i A', $originalMessage->getInternalDate() / 1000) : 'Unknown date' }}
                    </span>
                </div>
                <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                    <p class="text-gray-700">{{ $originalMessage->getSnippet() }}</p>
                </div>
            </div>
        </div>

        <!-- Reply Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Compose Reply</h3>
            
            <form action="{{ route('gmail.send-reply', $threadId) }}" method="POST">
                @csrf
                
                <div class="space-y-6">
                    <!-- To Field -->
                    <div>
                        <label for="to" class="block text-sm font-medium text-gray-700 mb-2">To:</label>
                        <input type="email" 
                               id="to" 
                               name="to" 
                               value="{{ $toEmail }}" 
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="recipient@example.com">
                        @error('to')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Subject Field -->
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Subject:</label>
                        <input type="text" 
                               id="subject" 
                               name="subject" 
                               value="Re: {{ $originalSubject }}" 
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Email subject">
                        @error('subject')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Message Body -->
                    <div>
                        <label for="email-body" class="block text-sm font-medium text-gray-700 mb-2">Message:</label>
                        <textarea id="email-body" 
                                  name="body" 
                                  required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Type your message here..."></textarea>
                        @error('body')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <a href="{{ route('gmail.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </a>
                        <div class="flex items-center space-x-3">
                            <button type="button" 
                                    onclick="saveDraft()" 
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <i class="fas fa-save mr-2"></i>
                                Save Draft
                            </button>
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Send Reply
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <script>
        function saveDraft() {
            // This would typically save to a drafts folder or local storage
            alert('Draft saved! (This is a placeholder - implement actual draft saving)');
        }

        // Auto-focus on the body field when page loads
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                if (tinymce.get('email-body')) {
                    tinymce.get('email-body').focus();
                }
            }, 500);
        });
    </script>
</body>
</html> 