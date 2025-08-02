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
} 