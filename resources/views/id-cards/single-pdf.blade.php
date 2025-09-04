<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ID Card - {{ $cardData['user']->first_name }} {{ $cardData['user']->last_name }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: {{ $cardData['idCard']->background_color }};
            color: {{ $cardData['idCard']->text_color }};
        }
        
        .id-card {
            width: 324px;
            height: 204px;
            background: linear-gradient(135deg, {{ $cardData['idCard']->background_color }} 0%, {{ $cardData['idCard']->accent_color }}20 100%);
            border: 2px solid {{ $cardData['idCard']->accent_color }};
            border-radius: 8px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background: {{ $cardData['idCard']->accent_color }};
            color: white;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }
        
        .card-content {
            padding: 12px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            height: calc(100% - 40px);
        }
        
        .user-info {
            flex: 1;
            margin-right: 12px;
        }
        
        .user-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 4px;
            color: {{ $cardData['idCard']->text_color }};
        }
        
        .user-role {
            font-size: 14px;
            margin-bottom: 4px;
            color: {{ $cardData['idCard']->text_color }};
        }
        
        .user-organization {
            font-size: 12px;
            margin-bottom: 4px;
            color: {{ $cardData['idCard']->text_color }};
        }
        
        @if($cardData['participant'])
        .conference-info {
            font-size: 11px;
            margin-bottom: 4px;
            color: {{ $cardData['idCard']->text_color }};
        }
        
        .participant-type {
            font-size: 11px;
            color: {{ $cardData['idCard']->text_color }};
        }
        @endif
        
        .user-photo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 2px solid {{ $cardData['idCard']->accent_color }};
            overflow: hidden;
            flex-shrink: 0;
        }
        
        .user-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .photo-placeholder {
            width: 100%;
            height: 100%;
            background-color: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #6b7280;
        }
        
        .card-footer {
            position: absolute;
            bottom: 8px;
            left: 12px;
            right: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .qr-code {
            width: 40px;
            height: 40px;
        }
        
        .qr-code img {
            width: 100%;
            height: 100%;
        }
        
        .qr-placeholder {
            width: 100%;
            height: 100%;
            background-color: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            color: #6b7280;
            border: 1px solid #d1d5db;
        }
        
        .card-id {
            font-size: 10px;
            color: {{ $cardData['idCard']->text_color }};
            text-align: right;
        }
        
        .card-type-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            background: {{ $cardData['idCard']->accent_color }};
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="id-card">
        <div class="card-header">
            @if($cardData['participant'])
                {{ $cardData['participant']->conference->name ?? 'Conference' }}
            @else
                Company ID Card
            @endif
        </div>
        
        <div class="card-type-badge">
            {{ ucfirst($cardData['idCard']->type) }}
        </div>
        
        <div class="card-content">
            <div class="user-info">
                <div class="user-name">{{ $cardData['user']->first_name }} {{ $cardData['user']->last_name }}</div>
                
                @if($cardData['participant'])
                    <div class="user-role">{{ $cardData['participant']->participantType->name ?? 'Participant' }}</div>
                    <div class="user-organization">{{ $cardData['user']->organization ?? 'N/A' }}</div>
                    <div class="conference-info">{{ $cardData['participant']->conference->name ?? 'Conference' }}</div>
                    <div class="participant-type">ID: {{ $cardData['participant']->serial_number ?? $cardData['participant']->id }}</div>
                @else
                    @php
                        $roles = $cardData['user']->roles->pluck('name')->first() ?? 'Employee';
                    @endphp
                    <div class="user-role">{{ ucfirst($roles) }}</div>
                    <div class="user-organization">{{ $cardData['user']->organization ?? 'Company' }}</div>
                    <div class="participant-type">ID: {{ $cardData['user']->id }}</div>
                @endif
            </div>
            
            @if($cardData['idCard']->include_photo)
                <div class="user-photo">
                    @if($cardData['photoUrl'])
                        <img src="{{ $cardData['photoUrl'] }}" alt="Profile Photo">
                    @else
                        <div class="photo-placeholder">Photo</div>
                    @endif
                </div>
            @endif
        </div>
        
        <div class="card-footer">
            @if($cardData['idCard']->include_qr_code)
                <div class="qr-code">
                    @if($cardData['qrCode'])
                        <img src="{{ $cardData['qrCode'] }}" alt="QR Code">
                    @else
                        <div class="qr-placeholder">QR</div>
                    @endif
                </div>
            @endif
            
            <div class="card-id">
                @if($cardData['participant'])
                    {{ $cardData['participant']->serial_number ?? 'ID: ' . $cardData['participant']->id }}
                @else
                    ID: {{ $cardData['user']->id }}
                @endif
            </div>
        </div>
    </div>
</body>
</html>
