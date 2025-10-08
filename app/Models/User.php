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
        'expertise_interests',
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
}
