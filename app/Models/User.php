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

    /**
     * Check if user role has permission for a route
     * 
     * @param string $routeName Route name (e.g., 'conferences.index') or route path
     * @param \Illuminate\Http\Request|null $request Optional request object for extracting route info
     * @return bool
     */
    public function canAccessRoute(string $routeName = null, $request = null): bool
    {
        // Superadmins have access to everything
        if ($this->hasRole('superadmin')) {
            return true;
        }

        // If no route name provided, try to get it from request
        if ($routeName === null && $request) {
            $route = $request->route();
            $routeName = $route ? $route->getName() : null;
        }

        // If still no route name, cannot determine permission
        if ($routeName === null) {
            return false;
        }

        // Extract permission required for this route
        $requiredPermission = $this->getPermissionForRoute($routeName);
        
        if ($requiredPermission === null) {
            // If no specific permission mapping found, deny by default for security
            // You can modify this behavior based on your requirements
            return false;
        }

        // Check if user has the required permission
        return $this->hasPermission($requiredPermission);
    }

    /**
     * Get the required permission for a given route name
     * 
     * @param string $routeName
     * @return string|null
     */
    protected function getPermissionForRoute(string $routeName): ?string
    {
        // Route name to permission mapping
        $routePermissionMap = [
            // Conference routes
            'conferences.index' => 'conferences.view',
            'conferences.show' => 'conferences.view',
            'conferences.create' => 'conferences.create',
            'conferences.store' => 'conferences.create',
            'conferences.edit' => 'conferences.edit',
            'conferences.update' => 'conferences.edit',
            'conferences.destroy' => 'conferences.delete',
            'conferences.export' => 'conferences.export',
            'conferences.check-conflicts' => 'conferences.view',
            'conferences.conflicts' => 'conferences.view',
            'conferences.resolve-conflict' => 'conferences.edit',

            // Participant routes
            'participants.index' => 'participants.view',
            'participants.show' => 'participants.view',
            'participants.create' => 'participants.create',
            'participants.store' => 'participants.create',
            'participants.edit' => 'participants.edit',
            'participants.update' => 'participants.edit',
            'participants.destroy' => 'participants.delete',
            'participants.import.form' => 'participants.import.view',
            'participants.import.process' => 'participants.import.process',
            'participants.import.sample' => 'participants.import.sample',

            // Session routes
            'sessions.index' => 'sessions.view',
            'sessions.show' => 'sessions.view',
            'sessions.create' => 'sessions.create',
            'sessions.store' => 'sessions.create',
            'sessions.edit' => 'sessions.edit',
            'sessions.update' => 'sessions.edit',
            'sessions.destroy' => 'sessions.delete',
            'sessions.export' => 'sessions.export',
            'sessions.publish' => 'sessions.publish',
            'sessions.resend-email' => 'sessions.resend_email',

            // Task routes
            'tasks.index' => 'tasks.view',
            'tasks.show' => 'tasks.view',
            'tasks.create' => 'tasks.create',
            'tasks.store' => 'tasks.create',
            'tasks.edit' => 'tasks.edit',
            'tasks.update' => 'tasks.edit',
            'tasks.destroy' => 'tasks.delete',
            'tasks.export' => 'tasks.export',
            'tasks.update-status' => 'tasks.update_status',

            // Notification routes
            'notifications.index' => 'notifications.view',
            'notifications.show' => 'notifications.view',
            'notifications.create' => 'notifications.create',
            'notifications.store' => 'notifications.create',
            'notifications.mark-read' => 'notifications.mark_read',
            'notifications.mark-all-read' => 'notifications.mark_read',

            // User routes
            'users.index' => 'users.view',
            'users.show' => 'users.view',
            'users.create' => 'users.create',
            'users.store' => 'users.create',
            'users.edit' => 'users.edit',
            'users.update' => 'users.edit',
            'users.destroy' => 'users.delete',
            'users.activate' => 'users.activate',
            'users.deactivate' => 'users.deactivate',

            // Role routes
            'roles.index' => 'roles.view',
            'roles.show' => 'roles.view',
            'roles.create' => 'roles.create',
            'roles.store' => 'roles.create',
            'roles.edit' => 'roles.edit',
            'roles.update' => 'roles.edit',
            'roles.destroy' => 'roles.delete',
            'roles.assign-users' => 'roles.assign_users',
            'roles.update-user-assignments' => 'roles.assign_users',

            // Conference Docs routes
            'conference-docs.index' => 'conference-docs.view',
            'conference-docs.show' => 'conference-docs.view',
            'conference-docs.create' => 'conference-docs.create',
            'conference-docs.store' => 'conference-docs.create',
            'conference-docs.edit' => 'conference-docs.edit',
            'conference-docs.update' => 'conference-docs.edit',
            'conference-docs.destroy' => 'conference-docs.delete',
            'conference-docs.media.upload' => 'conference-docs.media.upload',
            'conference-docs.media.download' => 'conference-docs.media.download',
            'participant.conference-docs.index' => 'conference-docs.view',
            'participant.conference-docs.download' => 'conference-docs.media.download',

            // Gmail routes
            'gmail.index' => 'gmail.view',
            'gmail.disconnect' => 'gmail.disconnect',
            'gmail.reply' => 'gmail.reply',
            'gmail.send-reply' => 'gmail.send_reply',
            'gmail.participants' => 'gmail.participants',

            // Bulk Email routes
            'bulk.email' => 'bulk-email.view',
            'bulk.email.send' => 'bulk-email.send',
            'bulk.email.participants' => 'bulk-email.participants',

            // Backup routes
            'admin.backup.index' => 'backup.view',
            'admin.backup.create' => 'backup.create',
            'admin.backup.restore' => 'backup.restore',
            'admin.backup.destroy' => 'backup.delete',
            'admin.backup.cleanup' => 'backup.cleanup',

            // Email Tracking routes
            'admin.email-tracking.index' => 'email-tracking.view',
            'admin.email-tracking.stats' => 'email-tracking.stats',
            'admin.email-tracking.emails' => 'email-tracking.emails',
            'admin.email-tracking.show' => 'email-tracking.show',
            'admin.email-tracking.resend' => 'email-tracking.resend',
            'admin.email-tracking.destroy' => 'email-tracking.delete',
            'admin.email-tracking.export' => 'email-tracking.export',

            // Email Settings routes
            'admin.email-settings.index' => 'email-settings.view',
            'admin.email-settings.edit' => 'email-settings.edit',
            'admin.email-settings.update' => 'email-settings.update',
            'admin.email-settings.preview' => 'email-settings.preview',
            'admin.email-settings.test' => 'email-settings.test',
            'admin.email-settings.reset' => 'email-settings.reset',

            // Travel routes
            'admin.itineraries' => 'travel.itineraries.view',
            'admin.export-itinerary' => 'travel.export_itinerary',
            'admin.room-allocations' => 'travel.room_allocations.view',
            'admin.travel-conflicts' => 'travel.travel_conflicts.view',

            // Passwordless Login routes
            'passwordless-login.admin.index' => 'passwordless-login.admin.view',
            'passwordless-login.generate' => 'passwordless-login.generate',
            'passwordless-login.generate.bulk' => 'passwordless-login.generate_bulk',
            'passwordless-login.participants' => 'passwordless-login.participants',
            'passwordless-login.user.links' => 'passwordless-login.user.links',
            'passwordless-login.user.revoke' => 'passwordless-login.user.revoke',
            'passwordless-login.cleanup' => 'passwordless-login.cleanup',
        ];

        // Check direct mapping first
        if (isset($routePermissionMap[$routeName])) {
            return $routePermissionMap[$routeName];
        }

        // Try pattern-based matching for resource routes
        // Format: resource.action => resource.permission_type
        $parts = explode('.', $routeName);
        if (count($parts) >= 2) {
            $resource = $parts[0];
            $action = $parts[1];

            // Map common RESTful actions to permissions
            $actionPermissionMap = [
                'index' => 'view',
                'show' => 'view',
                'create' => 'create',
                'store' => 'create',
                'edit' => 'edit',
                'update' => 'edit',
                'destroy' => 'delete',
            ];

            if (isset($actionPermissionMap[$action])) {
                $permission = $resource . '.' . $actionPermissionMap[$action];
                
                // Check if this permission exists in config
                $configPermissions = config('permissions');
                $resourceKey = str_replace('-', '_', $resource);
                
                // Try to validate if permission exists in config
                // This is a fallback for standard RESTful routes
                return $permission;
            }
        }

        // No mapping found
        return null;
    }
}
