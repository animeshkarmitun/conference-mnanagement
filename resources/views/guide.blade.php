@extends(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('admin') ? 'layouts.app' : 'layouts.participant')

@section('title', 'How to Use CGS Events')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">How to Use CGS Events</h1>

        <!-- Quick Start Guide -->
        <div class="mb-12">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Quick Start Guide</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <div class="text-yellow-600 mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">1. Register as Speaker</h3>
                    <p class="text-sm text-gray-600">Submit your speaker application with your session proposal.</p>
                </div>
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="text-blue-600 mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">2. Track Application</h3>
                    <p class="text-sm text-gray-600">Monitor your application status and receive updates via email.</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="text-green-600 mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">3. Prepare Session</h3>
                    <p class="text-sm text-gray-600">Once approved, prepare and manage your conference session.</p>
                </div>
            </div>
        </div>

        <!-- Detailed Features -->
        <div class="space-y-8">
            <div>
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">For Speakers</h2>
                <div class="space-y-4">
                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-2">Registration Process</h3>
                        <ol class="list-decimal list-inside space-y-2 text-gray-600">
                            <li>Click "Register as Speaker" in the navigation menu</li>
                            <li>Fill out the registration form with your personal and professional information</li>
                            <li>Upload your CV/Resume (PDF format) and a professional photo</li>
                            <li>Submit your session proposal with title, description, and duration</li>
                            <li>Review and submit your application</li>
                        </ol>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-2">After Registration</h3>
                        <ul class="list-disc list-inside space-y-2 text-gray-600">
                            <li>You'll receive a confirmation email with your application details</li>
                            <li>Track your application status in your dashboard</li>
                            <li>Receive email notifications for any updates or requests</li>
                            <li>Once approved, you can access session management tools</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">For Conference Organizers</h2>
                <div class="space-y-4">
                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-2">Managing Speakers</h3>
                        <ul class="list-disc list-inside space-y-2 text-gray-600">
                            <li>Review speaker applications in the dashboard</li>
                            <li>Approve or reject applications with feedback</li>
                            <li>Manage speaker profiles and session details</li>
                            <li>Schedule sessions and assign venues</li>
                        </ul>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-2">Conference Management</h3>
                        <ul class="list-disc list-inside space-y-2 text-gray-600">
                            <li>Create and manage conference events</li>
                            <li>Track participant registrations</li>
                            <li>Monitor session schedules and venues</li>
                            <li>Generate reports and analytics</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">For Participants</h2>
                <div class="space-y-4">
                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-2">Registration and Attendance</h3>
                        <ul class="list-disc list-inside space-y-2 text-gray-600">
                            <li>Register for conferences through the registration form</li>
                            <li>View and select sessions to attend</li>
                            <li>Receive session reminders and updates</li>
                            <li>Access conference materials and resources</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Support Section -->
        <div class="mt-12 bg-gray-50 rounded-lg p-6">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Need Help?</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-semibold text-gray-900 mb-2">Contact Support</h3>
                    <p class="text-gray-600 mb-4">If you need assistance, our support team is here to help.</p>
                    <a href="mailto:support@cgsevents.com" class="text-yellow-600 hover:text-yellow-700">support@cgsevents.com</a>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-2">Documentation</h3>
                    <p class="text-gray-600 mb-4">Access our detailed documentation for more information.</p>
                    <a href="#" class="text-yellow-600 hover:text-yellow-700">View Documentation →</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 