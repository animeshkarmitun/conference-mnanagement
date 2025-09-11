<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Conference;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->with(['conference'])
            ->latest()
            ->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    public function participantIndex()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->with(['conference'])
            ->latest()
            ->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Notification $notification): JsonResponse
    {
        // Ensure user can only mark their own notifications as read
        if ($notification->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->markAsRead();
        
        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }

    public function markAllAsRead(): JsonResponse
    {
        Notification::where('user_id', auth()->id())
            ->where('read_status', false)
            ->update(['read_status' => true]);
        
        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }

    public function getNotificationData(Notification $notification): JsonResponse
    {
        // Ensure user can only access their own notifications
        if ($notification->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'id' => $notification->id,
            'type' => $notification->type,
            'message' => $notification->message,
            'related_model' => $notification->related_model,
            'related_id' => $notification->related_id,
            'action_url' => $notification->action_url,
            'read_status' => $notification->read_status,
            'created_at' => $notification->created_at->toISOString(),
        ]);
    }

    public function getUnreadCount(): JsonResponse
    {
        $count = Notification::where('user_id', auth()->id())
            ->where('read_status', false)
            ->count();
        
        return response()->json(['count' => $count]);
    }

    public function getRecentNotifications(): JsonResponse
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'read_status' => $notification->read_status,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'action_url' => $notification->action_url,
                ];
            });
        
        return response()->json($notifications);
    }

    public function create()
    {
        $users = \App\Models\User::all();
        $conferences = Conference::all();
        return view('notifications.create', compact('users', 'conferences'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'conference_id' => 'required|exists:conferences,id',
            'message' => 'required|string',
            'type' => 'required|in:MissingDocuments,SessionUpdate,TravelUpdate,General,TaskUpdate,ConferenceUpdate,ProfileUpdate',
            'related_model' => 'nullable|string',
            'related_id' => 'nullable|integer',
            'action_url' => 'nullable|string',
        ]);
        
        $validated['sent_at'] = now();
        $validated['read_status'] = false;
        
        Notification::create($validated);
        return redirect()->route('notifications.index')->with('success', 'Notification created successfully.');
    }

    public function show(Notification $notification)
    {
        // Ensure user can only view their own notifications
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }
        
        return view('notifications.show', compact('notification'));
    }

    public function edit(Notification $notification)
    {
        return view('notifications.edit', compact('notification'));
    }

    public function update(Request $request, Notification $notification)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'type' => 'required|in:MissingDocuments,SessionUpdate,TravelUpdate,General,TaskUpdate,ConferenceUpdate,ProfileUpdate',
        ]);
        $notification->update($validated);
        return redirect()->route('notifications.index')->with('success', 'Notification updated successfully.');
    }

    public function destroy(Notification $notification)
    {
        // Ensure user can only delete their own notifications
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }
        
        $notification->delete();
        return redirect()->route('notifications.index')->with('success', 'Notification deleted successfully.');
    }
} 