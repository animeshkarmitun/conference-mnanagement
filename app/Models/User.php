<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'first_name',
        'last_name',
        'gender',
        'date_of_birth',
        'organization',
        'profile_picture',
        'google_token',
        'email_verified_at',
        // Enhanced participant fields
        'pronoun',
        'contact_no',
        'whatsapp_no',
        'messaging_type',
        'messaging_number',
        'country',
        'field_of_work_study',
        'designation',
        'organization_institution',
        'is_student',
        'year',
        'department_name',
        'institution_name',
        'address',
        'home_district',
        'nid_passport_birth_certificate',
        'how_found_bobc',
        'attended_previous_bobc',
        // Media/Speaker fields
        'media_type',
        'other_contact_type',
        'other_contact_no',
        'dietary_requirements',
        'dietary_requirements_other',
        'sector',
        'current_designation',
        'areas_of_expertise',
        'preferred_topic',
        'linkedin_link',
        'twitter_link',
        'facebook_link',
        'has_valid_passport',
        'had_visa_issue_bd',
        'visa_issue_explanation',
        'resume',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relationships
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_user')->withPivot('status', 'notes');
    }

    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function communications()
    {
        return $this->hasMany(Communication::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function passwordlessLogins()
    {
        return $this->hasMany(PasswordlessLogin::class);
    }

    public function emails()
    {
        return $this->hasMany(Email::class);
    }

    public function conferenceConflicts()
    {
        return $this->hasMany(ConferenceConflict::class);
    }

    public function participantProfileSessions()
    {
        return $this->hasMany(ParticipantProfileSession::class);
    }

    // Scope: Filter users by role name
    public function scopeWithRole($query, $roleName)
    {
        return $query->whereHas('roles', function ($q) use ($roleName) {
            $q->where('name', $roleName);
        });
    }

    // Helper: Check if user has a specific role
    public function hasRole($roleName)
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    // Helper: Check if user has any roles assigned
    public function hasAnyRole()
    {
        return $this->roles()->count() > 0;
    }

    // Helper: Get user's primary role (first role)
    public function getPrimaryRole()
    {
        return $this->roles()->first();
    }

    // Multi-participant profile methods
    public function getActiveParticipants()
    {
        return $this->participants()->where('status', 'active')->get();
    }

    public function getParticipantsByConference($conferenceId)
    {
        return $this->participants()
            ->where('conference_id', $conferenceId)
            ->where('status', 'active')
            ->get();
    }

    public function hasConferenceConflict($conferenceId, $excludeParticipantId = null)
    {
        $query = $this->conferenceConflicts()
            ->where('conference_id', $conferenceId)
            ->where('status', 'pending');

        if ($excludeParticipantId) {
            $query->where('participant_id', '!=', $excludeParticipantId);
        }

        return $query->exists();
    }

    public function getActiveParticipantProfile()
    {
        $sessionId = session()->getId();
        $profileSession = $this->participantProfileSessions()
            ->where('session_id', $sessionId)
            ->with('participant')
            ->first();

        if ($profileSession) {
            return $profileSession->participant;
        }

        // Fallback to primary participant or first active participant
        return $this->participants()
            ->where('status', 'active')
            ->where('is_primary', true)
            ->first() ?? $this->participants()
            ->where('status', 'active')
            ->first();
    }

    public function setActiveParticipantProfile($participantId)
    {
        $sessionId = session()->getId();
        $participant = $this->participants()->find($participantId);

        if (!$participant || $participant->status !== 'active') {
            return false;
        }

        // Update or create profile session
        $this->participantProfileSessions()->updateOrCreate(
            ['session_id' => $sessionId],
            [
                'participant_id' => $participantId,
                'last_activity' => now()
            ]
        );

        return true;
    }

    public function getConferencesWithConflicts()
    {
        return $this->conferenceConflicts()
            ->where('status', 'pending')
            ->with(['conference', 'conflictingConference'])
            ->get()
            ->groupBy('conference_id');
    }

    /**
     * Get all permissions for this user across all their roles
     * Returns a flat array of unique permissions
     */
    public function getAllPermissions(): array
    {
        $permissions = [];
        
        foreach ($this->roles as $role) {
            $rolePermissions = $role->permissions ?? [];
            
            // Handle wildcard permission (*) - grants all permissions
            if (in_array('*', $rolePermissions)) {
                return ['*']; // Return wildcard to indicate all permissions
            }
            
            $permissions = array_merge($permissions, $rolePermissions);
        }
        
        return array_unique($permissions);
    }

    /**
     * Check if user has a specific permission
     * Supports wildcard patterns like 'conferences.*' or exact matches like 'conferences.view'
     */
    public function hasPermission(string $permission): bool
    {
        $userPermissions = $this->getAllPermissions();
        
        // Check for wildcard (all permissions)
        if (in_array('*', $userPermissions)) {
            return true;
        }
        
        // Exact match
        if (in_array($permission, $userPermissions)) {
            return true;
        }
        
        // Check for wildcard pattern match (e.g., 'conferences.*' matches 'conferences.view')
        foreach ($userPermissions as $userPermission) {
            if (str_ends_with($userPermission, '.*')) {
                $prefix = str_replace('.*', '', $userPermission);
                if (str_starts_with($permission, $prefix . '.')) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user has all of the given permissions
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get the default dashboard route for this user based on their permissions
     */
    public function getDefaultDashboardRoute(): string
    {
        // Admin/superadmin go to main dashboard
        if ($this->hasRole('admin') || $this->hasRole('superadmin')) {
            return 'dashboard';
        }
        
        // Specific role-based dashboards (for backward compatibility)
        if ($this->hasRole('attendee') || $this->hasRole('speaker')) {
            // Only redirect to participant dashboard if they have participant records
            if ($this->participants()->exists()) {
                return 'participant-dashboard';
            }
        }
        
        if ($this->hasRole('tasker')) {
            return 'dashboard.tasker';
        }
        
        if ($this->hasRole('event_coordinator')) {
            return 'event-coordinator.dashboard';
        }
        
        // For any other role (including custom roles like organizer), use role-based dashboard
        // This will show a dashboard based on their permissions
        return 'role-dashboard';
    }
}
