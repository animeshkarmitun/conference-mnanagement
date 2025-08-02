<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * Format session time in a human-readable way
     */
    public static function formatSessionTime($startTime, $endTime, $status = null)
    {
        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);
        $now = Carbon::now();
        
        // Calculate duration
        $duration = $start->diffInMinutes($end);
        $durationText = self::formatDuration($duration);
        
        // Format times in 12-hour format
        $startTimeFormatted = $start->format('g:i A');
        $endTimeFormatted = $end->format('g:i A');
        
        // Determine date format based on relative position
        $dateFormat = self::getDateFormat($start, $now);
        
        // Build the main time string
        $timeString = $dateFormat . ' at ' . $startTimeFormatted . ' - ' . $endTimeFormatted;
        
        // Add status-specific information
        $statusInfo = self::getStatusInfo($start, $end, $now, $status);
        
        return [
            'time_string' => $timeString,
            'duration' => $durationText,
            'status_info' => $statusInfo,
            'is_today' => $start->isToday(),
            'is_tomorrow' => $start->isTomorrow(),
            'is_past' => $end->isPast(),
            'is_active' => $start->isPast() && $end->isFuture(),
            'duration_minutes' => $duration,
            'start_time' => $startTimeFormatted,
            'end_time' => $endTimeFormatted,
        ];
    }
    
    /**
     * Get appropriate date format based on relative position
     */
    private static function getDateFormat($date, $now)
    {
        if ($date->isToday()) {
            return 'Today';
        } elseif ($date->isTomorrow()) {
            return 'Tomorrow';
        } elseif ($date->diffInDays($now) <= 7) {
            return $date->format('l'); // Monday, Tuesday, etc.
        } else {
            return $date->format('l, M jS'); // Monday, July 25th
        }
    }
    
    /**
     * Format duration in human-readable way
     */
    private static function formatDuration($minutes)
    {
        if ($minutes < 60) {
            return $minutes . ' minute' . ($minutes != 1 ? 's' : '');
        }
        
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        
        if ($remainingMinutes == 0) {
            return $hours . ' hour' . ($hours != 1 ? 's' : '');
        }
        
        return $hours . ' hour' . ($hours != 1 ? 's' : '') . ' ' . $remainingMinutes . ' minute' . ($remainingMinutes != 1 ? 's' : '');
    }
    
    /**
     * Get status-specific information
     */
    private static function getStatusInfo($start, $end, $now, $status)
    {
        if ($start->isPast() && $end->isFuture()) {
            // Active session
            $remainingMinutes = $now->diffInMinutes($end);
            if ($remainingMinutes < 60) {
                return 'Ends in ' . $remainingMinutes . ' minute' . ($remainingMinutes != 1 ? 's' : '');
            } else {
                $remainingHours = floor($remainingMinutes / 60);
                $remainingMins = $remainingMinutes % 60;
                $text = 'Ends in ' . $remainingHours . ' hour' . ($remainingHours != 1 ? 's' : '');
                if ($remainingMins > 0) {
                    $text .= ' ' . $remainingMins . ' minute' . ($remainingMins != 1 ? 's' : '');
                }
                return $text;
            }
        } elseif ($end->isPast()) {
            // Finished session
            return 'Completed';
        } elseif ($start->isToday()) {
            // Today's upcoming session
            $minutesUntilStart = $now->diffInMinutes($start);
            if ($minutesUntilStart < 60) {
                return 'Starts in ' . $minutesUntilStart . ' minute' . ($minutesUntilStart != 1 ? 's' : '');
            } else {
                $hoursUntilStart = floor($minutesUntilStart / 60);
                $minsUntilStart = $minutesUntilStart % 60;
                $text = 'Starts in ' . $hoursUntilStart . ' hour' . ($hoursUntilStart != 1 ? 's' : '');
                if ($minsUntilStart > 0) {
                    $text .= ' ' . $minsUntilStart . ' minute' . ($minsUntilStart != 1 ? 's' : '');
                }
                return $text;
            }
        } elseif ($start->isTomorrow()) {
            // Tomorrow's session
            return 'Starts tomorrow';
        } else {
            // Future session
            $daysUntilStart = $now->diffInDays($start);
            if ($daysUntilStart <= 7) {
                return 'Starts in ' . $daysUntilStart . ' day' . ($daysUntilStart != 1 ? 's' : '');
            } else {
                $weeksUntilStart = floor($daysUntilStart / 7);
                return 'Starts in ' . $weeksUntilStart . ' week' . ($weeksUntilStart != 1 ? 's' : '');
            }
        }
    }
    
    /**
     * Get duration color class based on session length
     */
    public static function getDurationColorClass($minutes)
    {
        if ($minutes < 60) {
            return 'bg-green-100 text-green-800'; // Short sessions
        } elseif ($minutes <= 180) {
            return 'bg-blue-100 text-blue-800'; // Medium sessions (1-3 hours)
        } else {
            return 'bg-orange-100 text-orange-800'; // Long sessions (>3 hours)
        }
    }
    
    /**
     * Get status color class
     */
    public static function getStatusColorClass($isActive, $isPast, $isToday)
    {
        if ($isActive) {
            return 'bg-green-100 text-green-800 border-green-200';
        } elseif ($isPast) {
            return 'bg-gray-100 text-gray-600 border-gray-200';
        } elseif ($isToday) {
            return 'bg-blue-100 text-blue-800 border-blue-200';
        } else {
            return 'bg-blue-100 text-blue-800 border-blue-200';
        }
    }

    /**
     * Format conference dates in a human-readable way
     */
    public static function formatConferenceDates($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $now = Carbon::now();
        
        // Calculate conference duration
        $durationDays = $start->diffInDays($end) + 1; // Include both start and end day
        $durationText = self::formatConferenceDuration($durationDays);
        
        // Determine conference status
        $isActive = $start->isPast() && $end->isFuture();
        $isPast = $end->isPast();
        $isToday = $start->isToday() || $end->isToday();
        $isUpcoming = $start->isFuture();
        
        // Format the schedule string
        $scheduleString = self::getConferenceScheduleString($start, $end, $now);
        
        return [
            'schedule_string' => $scheduleString,
            'duration' => $durationText,
            'duration_days' => $durationDays,
            'is_active' => $isActive,
            'is_past' => $isPast,
            'is_today' => $isToday,
            'is_upcoming' => $isUpcoming,
            'start_date_formatted' => $start->format('l, M jS, Y'),
            'end_date_formatted' => $end->format('l, M jS, Y'),
        ];
    }
    
    /**
     * Get conference schedule string based on relative position
     */
    private static function getConferenceScheduleString($start, $end, $now)
    {
        if ($start->isToday() && $end->isToday()) {
            return 'Today';
        } elseif ($start->isToday() && $end->isFuture()) {
            return 'Today - ' . $end->format('M jS');
        } elseif ($start->isTomorrow() && $end->isTomorrow()) {
            return 'Tomorrow';
        } elseif ($start->isTomorrow() && $end->isFuture()) {
            return 'Tomorrow - ' . $end->format('M jS');
        } elseif ($start->diffInDays($now) <= 7) {
            return $start->format('l') . ' - ' . $end->format('M jS');
        } else {
            return $start->format('M jS') . ' - ' . $end->format('M jS, Y');
        }
    }
    
    /**
     * Format conference duration in human-readable way
     */
    private static function formatConferenceDuration($days)
    {
        if ($days == 1) {
            return '1 day';
        } elseif ($days < 7) {
            return $days . ' days';
        } elseif ($days == 7) {
            return '1 week';
        } else {
            $weeks = floor($days / 7);
            $remainingDays = $days % 7;
            
            if ($remainingDays == 0) {
                return $weeks . ' week' . ($weeks != 1 ? 's' : '');
            } else {
                $text = $weeks . ' week' . ($weeks != 1 ? 's' : '');
                $text .= ' ' . $remainingDays . ' day' . ($remainingDays != 1 ? 's' : '');
                return $text;
            }
        }
    }
    
    /**
     * Get conference status text
     */
    public static function getConferenceStatusText($isActive, $isPast, $isToday, $isUpcoming)
    {
        if ($isActive) {
            return 'Active';
        } elseif ($isPast) {
            return 'Completed';
        } elseif ($isToday) {
            return 'Today';
        } elseif ($isUpcoming) {
            return 'Upcoming';
        } else {
            return 'Scheduled';
        }
    }
    
    /**
     * Get conference status color class
     */
    public static function getConferenceStatusColorClass($isActive, $isPast, $isToday, $isUpcoming)
    {
        if ($isActive) {
            return 'bg-green-100 text-green-800 border-green-200';
        } elseif ($isPast) {
            return 'bg-gray-100 text-gray-600 border-gray-200';
        } elseif ($isToday) {
            return 'bg-blue-100 text-blue-800 border-blue-200';
        } elseif ($isUpcoming) {
            return 'bg-blue-100 text-blue-800 border-blue-200';
        } else {
            return 'bg-gray-100 text-gray-600 border-gray-200';
        }
    }
    
    /**
     * Get conference duration color class
     */
    public static function getConferenceDurationColorClass($days)
    {
        if ($days <= 2) {
            return 'bg-green-100 text-green-800'; // Short conferences
        } elseif ($days <= 5) {
            return 'bg-blue-100 text-blue-800'; // Medium conferences
        } else {
            return 'bg-orange-100 text-orange-800'; // Long conferences
        }
    }
} 