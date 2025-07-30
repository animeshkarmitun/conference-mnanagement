<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Participant Biographies</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
            background-color: #fff;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #f59e0b;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #f59e0b;
            font-size: 28px;
            margin: 0 0 10px 0;
            font-weight: bold;
        }
        .header .subtitle {
            color: #666;
            font-size: 14px;
            margin: 0;
        }
        .participant {
            margin-bottom: 40px;
            page-break-inside: avoid;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            background-color: #fafafa;
        }
        .participant-header {
            border-bottom: 2px solid #f59e0b;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .participant-name {
            font-size: 20px;
            font-weight: bold;
            color: #1f2937;
            margin: 0 0 5px 0;
        }
        .participant-details {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 15px;
        }
        .detail-item {
            flex: 1;
            min-width: 200px;
        }
        .detail-label {
            font-weight: bold;
            color: #6b7280;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .detail-value {
            color: #374151;
            font-size: 14px;
            margin-top: 2px;
        }
        .biography {
            background-color: #fff;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #f59e0b;
        }
        .biography-title {
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .biography-content {
            color: #4b5563;
            line-height: 1.7;
            text-align: justify;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
        .page-number {
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            margin-top: 20px;
        }
        @page {
            margin: 2cm;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Participant Biographies</h1>
        <p class="subtitle">Generated on {{ date('F j, Y \a\t g:i A') }}</p>
        <p class="subtitle">Total Participants: {{ count($participants) }}</p>
    </div>

    @foreach($participants as $index => $participant)
        <div class="participant">
            <div class="participant-header">
                <h2 class="participant-name">
                    {{ $participant->user->first_name ?? $participant->user->name }} {{ $participant->user->last_name ?? '' }}
                </h2>
            </div>
            
            <div class="participant-details">
                <div class="detail-item">
                    <div class="detail-label">Email</div>
                    <div class="detail-value">{{ $participant->user->email }}</div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Organization</div>
                    <div class="detail-value">{{ $participant->organization ?? 'Not specified' }}</div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Participant Type</div>
                    <div class="detail-value">{{ $participant->participantType->name ?? 'Not specified' }}</div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Registration Status</div>
                    <div class="detail-value">{{ ucfirst($participant->registration_status) }}</div>
                </div>
            </div>
            
            <div class="biography">
                <div class="biography-title">Biography</div>
                <div class="biography-content">
                    {{ $participant->bio ?? 'No biography provided.' }}
                </div>
            </div>
        </div>
        
        @if($index < count($participants) - 1)
            <div style="page-break-after: always;"></div>
        @endif
    @endforeach

    <div class="footer">
        <p>This document was automatically generated by the Conference Management System.</p>
        <p>For any questions, please contact the conference organizers.</p>
    </div>
</body>
</html> 