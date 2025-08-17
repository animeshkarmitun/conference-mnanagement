<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Conference Kit - {{ $conferenceKit->conference->name ?? 'Conference' }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #3B82F6, #1E40AF);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .header p {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .section-title {
            background: #F3F4F6;
            padding: 15px 20px;
            border-left: 4px solid #3B82F6;
            margin-bottom: 20px;
            font-size: 20px;
            font-weight: bold;
            color: #1F2937;
        }
        .session-card {
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
        }
        .session-title {
            font-size: 18px;
            font-weight: bold;
            color: #1F2937;
            margin-bottom: 10px;
        }
        .session-details {
            color: #6B7280;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .session-description {
            color: #374151;
            margin-bottom: 10px;
        }
        .contact-card {
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
        }
        .contact-name {
            font-size: 16px;
            font-weight: bold;
            color: #1F2937;
            margin-bottom: 5px;
        }
        .contact-role {
            color: #6B7280;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .contact-info {
            color: #374151;
            font-size: 14px;
        }
        .city-guide {
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            padding: 20px;
        }
        .city-guide h3 {
            color: #1F2937;
            margin-bottom: 15px;
        }
        .city-guide-section {
            margin-bottom: 15px;
        }
        .city-guide-section h4 {
            color: #374151;
            font-size: 16px;
            margin-bottom: 5px;
        }
        .city-guide-section p {
            color: #6B7280;
            font-size: 14px;
            margin: 0;
        }
        .footer {
            margin-top: 40px;
            padding: 20px;
            background: #F3F4F6;
            border-radius: 8px;
            text-align: center;
            color: #6B7280;
            font-size: 12px;
        }
        .page-break {
            page-break-before: always;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        @media print {
            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Conference Kit</h1>
        <p>{{ $conferenceKit->conference->name ?? 'Conference' }}</p>
        <p>Participant: {{ $participant->user->first_name ?? $participant->user->name }} {{ $participant->user->last_name ?? '' }} ({{ $participant->participantType->name ?? '' }})</p>
        <p>Generated on: {{ date('F j, Y') }}</p>
    </div>

    <!-- Session Links Section -->
    <div class="section">
        <div class="section-title">Session Links & Schedule</div>
        
        @if($sessionLinks->count() > 0)
            @foreach($sessionLinks as $sessionLink)
                @php $content = json_decode($sessionLink->content, true); @endphp
                <div class="session-card">
                    <div class="session-title">{{ $content['title'] ?? 'Session' }}</div>
                    <div class="session-details">
                        <strong>Time:</strong> {{ $content['time'] ?? 'Time TBD' }} | 
                        <strong>Room:</strong> {{ $content['room'] ?? 'Room TBD' }} | 
                        <strong>Speaker:</strong> {{ $content['speaker'] ?? 'TBD' }}
                    </div>
                    <div class="session-description">{{ $content['description'] ?? 'No description available.' }}</div>
                    @if(isset($content['zoom_link']) && $content['zoom_link'])
                        <div style="color: #3B82F6; font-weight: bold;">Join Link: {{ $content['zoom_link'] }}</div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="session-card">
                <div class="session-title">No Sessions Available</div>
                <div class="session-description">Session links will be added as they become available.</div>
            </div>
        @endif
    </div>

    <!-- Important Contacts Section -->
    <div class="section">
        <div class="section-title">Important Contacts</div>
        
        @if($contacts->count() > 0)
            <div class="info-grid">
                @foreach($contacts as $contact)
                    @php $content = json_decode($contact->content, true); @endphp
                    <div class="contact-card">
                        <div class="contact-name">{{ $content['name'] ?? 'Contact' }}</div>
                        <div class="contact-role">{{ $content['role'] ?? '' }}</div>
                        <div class="contact-info">
                            @if(isset($content['email']) && $content['email'])
                                <strong>Email:</strong> {{ $content['email'] }}<br>
                            @endif
                            @if(isset($content['phone']) && $content['phone'])
                                <strong>Phone:</strong> {{ $content['phone'] }}<br>
                            @endif
                            @if(isset($content['availability']) && $content['availability'])
                                <strong>Availability:</strong> {{ $content['availability'] }}
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="contact-card">
                <div class="contact-name">No Contacts Available</div>
                <div class="contact-info">Contact information will be added as it becomes available.</div>
            </div>
        @endif
    </div>

    <!-- City Guide Section -->
    @if($cityGuide)
        @php $content = json_decode($cityGuide->content, true); @endphp
        <div class="section">
            <div class="section-title">{{ $content['title'] ?? 'City Guide' }}</div>
            
            <div class="city-guide">
                <div class="city-guide-section">
                    <h4>Welcome & Overview</h4>
                    <p>{{ $content['description'] ?? 'No description available.' }}</p>
                </div>

                @if(isset($content['transportation']) && $content['transportation'])
                    <div class="city-guide-section">
                        <h4>Transportation</h4>
                        <p>{{ $content['transportation'] }}</p>
                    </div>
                @endif

                @if(isset($content['restaurants']) && $content['restaurants'])
                    <div class="city-guide-section">
                        <h4>Dining Options</h4>
                        <p>{{ $content['restaurants'] }}</p>
                    </div>
                @endif

                @if(isset($content['attractions']) && $content['attractions'])
                    <div class="city-guide-section">
                        <h4>Local Attractions</h4>
                        <p>{{ $content['attractions'] }}</p>
                    </div>
                @endif

                @if(isset($content['emergency_contacts']) && $content['emergency_contacts'])
                    <div class="city-guide-section">
                        <h4>Emergency Contacts</h4>
                        <p>{{ $content['emergency_contacts'] }}</p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p><strong>Conference Management System</strong></p>
        <p>This conference kit was generated automatically for {{ $participant->user->first_name ?? $participant->user->name }} {{ $participant->user->last_name ?? '' }}</p>
        <p>For support, please contact the conference organizers</p>
    </div>
</body>
</html>
