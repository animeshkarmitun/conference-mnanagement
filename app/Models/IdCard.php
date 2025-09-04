<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'conference_id',
        'template_config',
        'background_color',
        'text_color',
        'accent_color',
        'include_qr_code',
        'include_photo',
        'is_active',
    ];

    protected $casts = [
        'template_config' => 'array',
        'include_qr_code' => 'boolean',
        'include_photo' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForConference($query, $conferenceId)
    {
        return $query->where('conference_id', $conferenceId);
    }

    // Helper methods
    public function isParticipantCard()
    {
        return $this->type === 'participant';
    }

    public function isCompanyWorkerCard()
    {
        return $this->type === 'company_worker';
    }

    public function getDefaultTemplateConfig()
    {
        return [
            'layout' => 'standard',
            'fields' => [
                'name' => true,
                'role' => true,
                'organization' => true,
                'photo' => $this->include_photo,
                'qr_code' => $this->include_qr_code,
                'conference_info' => $this->isParticipantCard(),
                'session_info' => $this->isParticipantCard(),
            ],
            'dimensions' => [
                'width' => '3.375', // Standard ID card width in inches
                'height' => '2.125', // Standard ID card height in inches
            ],
        ];
    }
}
