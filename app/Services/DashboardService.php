<?php

namespace App\Services;

use App\Models\Conference;
use App\Models\Participant;
use App\Models\Session;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardService
{
    /**
     * Get conference progress data
     */
    public function getConferenceProgress($conferenceId)
    {
        $conference = Conference::findOrFail($conferenceId);
        
        $totalSessions = $conference->sessions()->count();
        $completedSessions = $conference->sessions()
            ->where('end_time', '<', now())
            ->count();
        
        $progressPercentage = $totalSessions > 0 ? round(($completedSessions / $totalSessions) * 100) : 0;
        
        // Calculate days remaining
        $daysRemaining = now()->diffInDays($conference->end_date, false);
        $daysRemaining = max(0, $daysRemaining); // Ensure non-negative
        
        return [
            'conference' => $conference,
            'total_sessions' => $totalSessions,
            'completed_sessions' => $completedSessions,
            'progress_percentage' => $progressPercentage,
            'days_remaining' => $daysRemaining,
            'start_date' => $conference->start_date,
            'end_date' => $conference->end_date,
        ];
    }

    /**
     * Get task progress data
     */
    public function getTaskProgress($conferenceId)
    {
        $totalTasks = Task::where('conference_id', $conferenceId)->count();
        $completedTasks = Task::where('conference_id', $conferenceId)
            ->where('status', 'completed')
            ->count();
        
        $progressPercentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
        $remainingTasks = $totalTasks - $completedTasks;
        
        return [
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'remaining_tasks' => $remainingTasks,
            'progress_percentage' => $progressPercentage,
        ];
    }

    /**
     * Get participant statistics
     */
    public function getParticipantStatistics($conferenceId)
    {
        $participants = Participant::with('user')
            ->where('conference_id', $conferenceId)
            ->get();

        // Gender distribution
        $genderStats = $participants->groupBy(function ($participant) {
            return $participant->user->gender ?? 'Other';
        })->map->count();

        // Age distribution (using date_of_birth field from users table)
        $ageStats = $participants->groupBy(function ($participant) {
            if (!$participant->user->date_of_birth) return 'Unknown';
            
            $age = Carbon::parse($participant->user->date_of_birth)->age;
            if ($age < 25) return '18-25';
            if ($age < 35) return '26-35';
            if ($age < 50) return '36-50';
            return '51+';
        })->map->count();


        return [
            'total_participants' => $participants->count(),
            'gender_distribution' => $genderStats,
            'age_distribution' => $ageStats,
        ];
    }

    /**
     * Get speaker statistics
     */
    public function getSpeakerStatistics($conferenceId)
    {
        $speakers = DB::table('participant_session')
            ->join('participants', 'participant_session.participant_id', '=', 'participants.id')
            ->join('users', 'participants.user_id', '=', 'users.id')
            ->where('participants.conference_id', $conferenceId)
            ->whereIn('participant_session.role', ['Speaker', 'Panelist', 'Moderator'])
            ->select('users.*', 'participant_session.role')
            ->get();

        // Gender distribution of speakers
        $speakerGenderStats = $speakers->groupBy('gender')->map->count();

        // Role distribution
        $roleStats = $speakers->groupBy('role')->map->count();

        return [
            'total_speakers' => $speakers->count(),
            'gender_distribution' => $speakerGenderStats,
            'role_distribution' => $roleStats,
        ];
    }

    /**
     * Get summary statistics
     */
    public function getSummaryStats($conferenceId)
    {
        // IMPORTANT: Use a base query and clone it for each metric to avoid condition leakage
        $base = Participant::where('conference_id', $conferenceId);
        
        // Invited participants (all participants)
        $invitedCount = (clone $base)->count();
        
        // Accepted participants (registration_status = approved)
        $acceptedCount = (clone $base)->where('registration_status', 'approved')->count();
        
        // Flying participants (with travel details)
        $flyingCount = (clone $base)->whereHas('travelDetails')->count();
        
        // Status breakdown scoped to the selected conference
        $statusScope = Participant::query()->where('conference_id', $conferenceId);
        $statusBreakdown = [
            'pending' => (clone $statusScope)->where('registration_status', 'pending')->count(),
            'approved' => (clone $statusScope)->where('registration_status', 'approved')->count(),
            'declined' => (clone $statusScope)->where('registration_status', 'rejected')->count(),
        ];
        
        // Speaker count
        $speakerCount = DB::table('participant_session')
            ->join('participants', 'participant_session.participant_id', '=', 'participants.id')
            ->where('participants.conference_id', $conferenceId)
            ->whereIn('participant_session.role', ['Speaker', 'Panelist', 'Moderator'])
            ->distinct()
            ->count('participant_session.participant_id');

        // Participant type counts scoped to the selected conference, sorted desc
        $participantTypeCounts = Participant::with('participantType')
            ->where('conference_id', $conferenceId)
            ->get()
            ->groupBy(function ($p) {
                return optional($p->participantType)->name ?: 'Unknown';
            })
            ->map->count()
            ->sortDesc()
            ->toArray();

        // Passwordless login stats for participants of this conference
        $pwdQuery = \App\Models\PasswordlessLogin::whereHas('user.participants', function ($q) use ($conferenceId) {
            $q->where('conference_id', $conferenceId);
        });
        $passwordlessStats = [
            'total' => (clone $pwdQuery)->count(),
            'active' => (clone $pwdQuery)->active()->count(),
            'used' => (clone $pwdQuery)->used()->count(),
            'expired' => (clone $pwdQuery)->expired()->count(),
        ];

        // Gender breakdown for participants in this conference
        $genderBreakdown = DB::table('participants')
            ->join('users', 'participants.user_id', '=', 'users.id')
            ->where('participants.conference_id', $conferenceId)
            ->select('users.gender', DB::raw('COUNT(*) as count'))
            ->groupBy('users.gender')
            ->pluck('count', 'gender')
            ->toArray();
        
        // Ensure all gender categories are present with 0 count
        $genderStats = [
            'male' => $genderBreakdown['male'] ?? 0,
            'female' => $genderBreakdown['female'] ?? 0,
            'other' => ($genderBreakdown['prefer_not_to_say'] ?? 0) + ($genderBreakdown['other'] ?? 0) + ($genderBreakdown[''] ?? 0) + ($genderBreakdown[null] ?? 0),
        ];

        return [
            'invited' => $invitedCount,
            'accepted' => $acceptedCount,
            'flying' => $flyingCount,
            'status_breakdown' => $statusBreakdown,
            'speakers' => $speakerCount,
            'participant_type_counts' => $participantTypeCounts,
            'passwordless_login_stats' => $passwordlessStats,
            'gender_breakdown' => $genderStats,
        ];
    }

    /**
     * Get country statistics for participants in a conference
     */
    public function getCountryStatistics($conferenceId)
    {
        // Aggregate via SQL to avoid collection inaccuracies and normalize country values
        $rows = DB::table('participants')
            ->join('users', 'participants.user_id', '=', 'users.id')
            ->where('participants.conference_id', $conferenceId)
            ->selectRaw("LOWER(TRIM(users.country)) as country_key, TRIM(users.country) as country, COUNT(*) as cnt")
            ->whereNotNull('users.country')
            ->whereRaw("TRIM(users.country) <> ''")
            ->groupBy('country_key', 'country')
            ->orderByDesc('cnt')
            ->get();

        // Participants without country
        $participantsWithoutCountry = DB::table('participants')
            ->leftJoin('users', 'participants.user_id', '=', 'users.id')
            ->where('participants.conference_id', $conferenceId)
            ->where(function ($q) {
                $q->whereNull('users.country')
                  ->orWhereRaw("TRIM(users.country) = ''");
            })
            ->count();

        // Global participants without country (across all conferences)
        $participantsWithoutCountryGlobal = DB::table('participants')
            ->leftJoin('users', 'participants.user_id', '=', 'users.id')
            ->where(function ($q) {
                $q->whereNull('users.country')
                  ->orWhereRaw("TRIM(users.country) = ''");
            })
            ->distinct('participants.user_id')
            ->count('participants.user_id');

        // Total with country
        $totalWithCountry = DB::table('participants')
            ->join('users', 'participants.user_id', '=', 'users.id')
            ->where('participants.conference_id', $conferenceId)
            ->whereNotNull('users.country')
            ->whereRaw("TRIM(users.country) <> ''")
            ->count();

        // Global country count (across all conferences) where at least one participant has a country
        $globalCountryCount = DB::table('participants')
            ->join('users', 'participants.user_id', '=', 'users.id')
            ->whereNotNull('users.country')
            ->whereRaw("TRIM(users.country) <> ''")
            ->selectRaw("COUNT(DISTINCT LOWER(TRIM(users.country))) as cnt")
            ->value('cnt');

        // Pretty format country names (Title Case) while preserving known acronyms
        $countries = $rows->map(function ($r) {
            $name = $r->country;
            // Normalize casing safely
            $pretty = ucwords(strtolower(trim($name)));
            // Handle common acronyms
            $pretty = preg_replace_callback('/\b(usa|uk|uae|eu)\b/i', function ($m) {
                $map = [
                    'usa' => 'USA',
                    'uk' => 'UK',
                    'uae' => 'UAE',
                    'eu' => 'EU',
                ];
                return $map[strtolower($m[0])] ?? strtoupper($m[0]);
            }, $pretty);

            return [
                'country' => $pretty,
                'count' => (int) $r->cnt,
            ];
        })->values();

        return [
            'countries' => $countries,
            'total_countries' => $countries->count(),
            'participants_without_country' => $participantsWithoutCountry,
            'participants_without_country_global' => (int) $participantsWithoutCountryGlobal,
            'total_participants_with_country' => $totalWithCountry,
            'global_country_count' => (int) $globalCountryCount,
        ];
    }

    /**
     * Get recent activities
     */
    public function getRecentActivities($conferenceId, $limit = 5)
    {
        try {
            // This would typically come from an activity log table
            // For now, we'll return recent participant registrations and task updates
            $activities = collect();
            
            // Only query if conference exists
            if (!Conference::find($conferenceId)) {
                return $activities;
            }
            
            // Get recent participants
            $recentParticipants = Participant::with('user')
                ->where('conference_id', $conferenceId)
                ->latest()
                ->take($limit)
                ->get()
                ->map(function ($participant) {
                    if (!$participant->user) {
                        return null;
                    }
                    return [
                        'type' => 'participant',
                        'description' => "New participant registered: {$participant->user->name}",
                        'created_at' => $participant->created_at,
                        'icon' => 'user',
                        'color' => 'blue',
                    ];
                })
                ->filter(); // Remove null values

            // Get recent tasks
            $recentTasks = Task::where('conference_id', $conferenceId)
                ->latest()
                ->take($limit)
                ->get()
                ->map(function ($task) {
                    // Ensure we have a valid task object
                    if (!$task || !is_object($task)) {
                        return null;
                    }
                    return [
                        'type' => 'task',
                        'description' => "Task updated: {$task->title}",
                        'created_at' => $task->updated_at,
                        'icon' => 'task',
                        'color' => 'green',
                    ];
                })
                ->filter(); // Remove null values

            // Merge and sort manually to avoid getKey() issues
            $allActivities = $recentParticipants->toArray() + $recentTasks->toArray();
            
            // Sort by created_at descending
            usort($allActivities, function ($a, $b) {
                return $b['created_at']->timestamp - $a['created_at']->timestamp;
            });
            
            // Take only the limit
            $allActivities = array_slice($allActivities, 0, $limit);
            
            return collect($allActivities);
                
        } catch (\Exception $e) {
            // Return empty collection if there's an error
            return collect();
        }
    }

    /**
     * Get upcoming deadlines
     */
    public function getUpcomingDeadlines($conferenceId)
    {
        $upcomingTasks = Task::where('conference_id', $conferenceId)
            ->where('due_date', '>=', now())
            ->where('status', '!=', 'completed')
            ->orderBy('due_date')
            ->take(5)
            ->get();

        $upcomingSessions = Session::where('conference_id', $conferenceId)
            ->where('start_time', '>', now())
            ->orderBy('start_time')
            ->take(5)
            ->get();

        return [
            'tasks' => $upcomingTasks,
            'sessions' => $upcomingSessions,
        ];
    }

    /**
     * Get all dashboard data for a conference
     */
    public function getAllDashboardData($conferenceId)
    {
        return [
            'conference_progress' => $this->getConferenceProgress($conferenceId),
            'task_progress' => $this->getTaskProgress($conferenceId),
            'participant_statistics' => $this->getParticipantStatistics($conferenceId),
            'speaker_statistics' => $this->getSpeakerStatistics($conferenceId),
            'summary_stats' => $this->getSummaryStats($conferenceId),
            'country_statistics' => $this->getCountryStatistics($conferenceId),
            'recent_activities' => $this->getRecentActivities($conferenceId),
            'upcoming_deadlines' => $this->getUpcomingDeadlines($conferenceId),
        ];
    }
} 