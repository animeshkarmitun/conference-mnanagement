<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\TravelDetail;
use App\Models\RoomAllocation;
use App\Models\Conference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventCoordinatorController extends Controller
{
    /**
     * Event Coordinator Dashboard
     */
    public function dashboard()
    {
        // Check if user is event coordinator
        if (!Auth::user()->hasRole('event_coordinator')) {
            return redirect()->back()->with('error', 'Access denied. Event Coordinator privileges required.');
        }

        // Get travel statistics
        $totalParticipants = Participant::count();
        $participantsWithTravel = TravelDetail::count();
        $participantsWithRooms = RoomAllocation::count();
        $activeConferences = Conference::where('end_date', '>=', now())->count();

        // Get recent travel details
        $recentTravelDetails = TravelDetail::with(['participant.user', 'hotel'])
            ->latest()
            ->take(10)
            ->get();

        // Get upcoming room allocations
        $upcomingRooms = RoomAllocation::with(['participant.user', 'hotel'])
            ->where('check_in', '>=', now())
            ->orderBy('check_in')
            ->take(10)
            ->get();

        return view('event-coordinator.dashboard', compact(
            'totalParticipants',
            'participantsWithTravel',
            'participantsWithRooms',
            'activeConferences',
            'recentTravelDetails',
            'upcomingRooms'
        ));
    }

    /**
     * Travel Manifest List
     */
    public function travelManifests(Request $request)
    {
        // Check if user is event coordinator
        if (!Auth::user()->hasRole('event_coordinator')) {
            return redirect()->back()->with('error', 'Access denied. Event Coordinator privileges required.');
        }

        $query = TravelDetail::with(['participant.user', 'hotel', 'participant.conference']);

        // Apply filters
        if ($request->filled('conference_id')) {
            $query->whereHas('participant', function($q) use ($request) {
                $q->where('conference_id', $request->conference_id);
            });
        }

        if ($request->filled('hotel_id')) {
            $query->where('hotel_id', $request->hotel_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('participant.user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $travelManifests = $query->latest()->paginate(20);
        $conferences = Conference::all();

        return view('event-coordinator.travel-manifests', compact('travelManifests', 'conferences'));
    }

    /**
     * Export Travel Manifest
     */
    public function exportManifest(Request $request)
    {
        // Check if user is event coordinator
        if (!Auth::user()->hasRole('event_coordinator')) {
            return redirect()->back()->with('error', 'Access denied. Event Coordinator privileges required.');
        }

        $query = TravelDetail::with(['participant.user', 'hotel', 'participant.conference']);

        // Apply same filters as list view
        if ($request->filled('conference_id')) {
            $query->whereHas('participant', function($q) use ($request) {
                $q->where('conference_id', $request->conference_id);
            });
        }

        if ($request->filled('hotel_id')) {
            $query->where('hotel_id', $request->hotel_id);
        }

        $travelDetails = $query->get();

        // For now, return a simple CSV export
        // In Phase 2, this will be enhanced with PDF/Excel
        $filename = 'travel_manifest_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($travelDetails) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'Participant Name',
                'Email',
                'Conference',
                'Hotel',
                'Arrival Date',
                'Departure Date',
                'Flight Info',
                'Extra Nights',
                'Organization'
            ]);

            // CSV Data
            foreach ($travelDetails as $detail) {
                fputcsv($file, [
                    ($detail->participant->user->first_name ?? $detail->participant->user->name) . ' ' . ($detail->participant->user->last_name ?? ''),
                    $detail->participant->user->email,
                    $detail->participant->conference->name ?? 'N/A',
                    $detail->hotel->name ?? 'N/A',
                    $detail->arrival_date ? date('Y-m-d H:i', strtotime($detail->arrival_date)) : 'N/A',
                    $detail->departure_date ? date('Y-m-d H:i', strtotime($detail->departure_date)) : 'N/A',
                    $detail->flight_info ?? 'N/A',
                    $detail->extra_nights ?? 0,
                    $detail->participant->organization ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
