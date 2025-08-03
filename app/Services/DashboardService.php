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

        // Nationality distribution
        $nationalityStats = $participants->groupBy(function ($participant) {
            return $participant->user->nationality ?? 'Other';
        })->map->count();

        // Profession distribution
        $professionStats = $participants->groupBy(function ($participant) {
            return $participant->user->profession ?? 'Other';
        })->map->count();

        return [
            'total_participants' => $participants->count(),
            'gender_distribution' => $genderStats,
            'age_distribution' => $ageStats,
            'nationality_distribution' => $nationalityStats,
            'profession_distribution' => $professionStats,
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
        $participants = Participant::where('conference_id', $conferenceId);
        
        // Invited participants (all participants)
        $invitedCount = $participants->count();
        
        // Accepted participants (approved = true)
        $acceptedCount = $participants->where('approved', true)->count();
        
        // Flying participants (with travel details)
        $flyingCount = $participants->whereHas('travelDetails')->count();
        
        // Status breakdown
        $statusBreakdown = [
            'pending' => $participants->where('approved', null)->count(),
            'approved' => $participants->where('approved', true)->count(),
            'declined' => $participants->where('approved', false)->count(),
        ];
        
        // Speaker count
        $speakerCount = DB::table('participant_session')
            ->join('participants', 'participant_session.participant_id', '=', 'participants.id')
            ->where('participants.conference_id', $conferenceId)
            ->whereIn('participant_session.role', ['Speaker', 'Panelist', 'Moderator'])
            ->distinct()
            ->count('participant_session.participant_id');

        return [
            'invited' => $invitedCount,
            'accepted' => $acceptedCount,
            'flying' => $flyingCount,
            'status_breakdown' => $statusBreakdown,
            'speakers' => $speakerCount,
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
            'recent_activities' => $this->getRecentActivities($conferenceId),
            'upcoming_deadlines' => $this->getUpcomingDeadlines($conferenceId),
        ];
    }
} 