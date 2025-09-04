<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ID Cards - {{ $conference->name ?? 'Conference' }}</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }
        
        .page {
            background: white;
            margin-bottom: 20px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #007bff;
        }
        
        .page-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        
        .page-subtitle {
            font-size: 14px;
            color: #666;
        }
        
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .id-card {
            width: 324px;
            height: 204px;
            background: linear-gradient(135deg, #ffffff 0%, #007bff20 100%);
            border: 2px solid #007bff;
            border-radius: 8px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin: 0 auto;
        }
        
        .card-header {
            background: #007bff;
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
            color: #333;
        }
        
        .user-role {
            font-size: 14px;
            margin-bottom: 4px;
            color: #333;
        }
        
        .user-organization {
            font-size: 12px;
            margin-bottom: 4px;
            color: #333;
        }
        
        .conference-info {
            font-size: 11px;
            margin-bottom: 4px;
            color: #333;
        }
        
        .participant-type {
            font-size: 11px;
            color: #333;
        }
        
        .user-photo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 2px solid #007bff;
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
            color: #333;
            text-align: right;
        }
        
        .card-type-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            background: #007bff;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .page-footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
        }
        
        @media print {
            body {
                background-color: white;
                margin: 0;
                padding: 0;
            }
            
            .page {
                margin-bottom: 0;
                padding: 10px;
                box-shadow: none;
                page-break-after: always;
            }
            
            .page:last-child {
                page-break-after: avoid;
            }
        }
    </style>
</head>
<body>
    @php
        $cardsPerPage = 4;
        $totalCards = count($cards);
        $totalPages = ceil($totalCards / $cardsPerPage);
    @endphp
    
    @for($page = 0; $page < $totalPages; $page++)
        <div class="page">
            <div class="page-header">
                <div class="page-title">{{ $conference->name ?? 'Conference' }} - ID Cards</div>
                <div class="page-subtitle">Page {{ $page + 1 }} of {{ $totalPages }} | Generated on {{ date('M j, Y') }}</div>
            </div>
            
            <div class="cards-grid">
                @for($i = $page * $cardsPerPage; $i < min(($page + 1) * $cardsPerPage, $totalCards); $i++)
                    @php
                        $cardData = $cards[$i];
                        $user = $cardData['user'];
                        $participant = $cardData['participant'] ?? null;
                        $idCard = $cardData['idCard'];
                    @endphp
                    
                    <div class="id-card">
                        <div class="card-header">
                            {{ $participant ? ($participant->conference->name ?? 'Conference') : 'Company ID Card' }}
                        </div>
                        
                        <div class="card-type-badge">
                            {{ ucfirst($idCard->type) }}
                        </div>
                        
                        <div class="card-content">
                            <div class="user-info">
                                <div class="user-name">{{ $user->first_name }} {{ $user->last_name }}</div>
                                
                                @if($participant)
                                    <div class="user-role">{{ $participant->participantType->name ?? 'Participant' }}</div>
                                    <div class="user-organization">{{ $user->organization ?? 'N/A' }}</div>
                                    <div class="conference-info">{{ $participant->conference->name ?? 'Conference' }}</div>
                                    <div class="participant-type">ID: {{ $participant->serial_number ?? $participant->id }}</div>
                                @else
                                    @php
                                        $roles = $user->roles->pluck('name')->first() ?? 'Employee';
                                    @endphp
                                    <div class="user-role">{{ ucfirst($roles) }}</div>
                                    <div class="user-organization">{{ $user->organization ?? 'Company' }}</div>
                                    <div class="participant-type">ID: {{ $user->id }}</div>
                                @endif
                            </div>
                            
                            @if($idCard->include_photo)
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
                            @if($idCard->include_qr_code)
                                <div class="qr-code">
                                    @if($cardData['qrCode'])
                                        <img src="{{ $cardData['qrCode'] }}" alt="QR Code">
                                    @else
                                        <div class="qr-placeholder">QR</div>
                                    @endif
                                </div>
                            @endif
                            
                            <div class="card-id">
                                @if($participant)
                                    {{ $participant->serial_number ?? 'ID: ' . $participant->id }}
                                @else
                                    ID: {{ $user->id }}
                                @endif
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
            
            <div class="page-footer">
                <p>Total Cards: {{ $totalCards }} | Generated: {{ date('M j, Y g:i A') }}</p>
            </div>
        </div>
    @endfor
</body>
</html>
