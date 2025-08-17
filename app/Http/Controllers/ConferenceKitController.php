<?php

namespace App\Http\Controllers;

use App\Models\ConferenceKit;
use App\Models\ConferenceKitItem;
use App\Models\Conference;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ConferenceKitController extends Controller
{
    /**
     * Display conference kit for participants
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get participant's conference
        $participant = Participant::with(['conference', 'participantType'])
            ->where('user_id', $user->id)
            ->where('registration_status', 'approved')
            ->latest()
            ->first();
            
        if (!$participant) {
            return redirect()->back()->with('error', 'No approved conference registration found.');
        }
        
        // Get conference kit
        $conferenceKit = ConferenceKit::with(['conferenceKitItems'])
            ->where('conference_id', $participant->conference_id)
            ->first();
            
        if (!$conferenceKit) {
            return redirect()->back()->with('error', 'Conference kit not available yet.');
        }
        
        // Organize kit items by type
        $sessionLinks = $conferenceKit->conferenceKitItems->where('type', 'SessionLink');
        $contacts = $conferenceKit->conferenceKitItems->where('type', 'Contact');
        $cityGuide = $conferenceKit->conferenceKitItems->where('type', 'CityGuide')->first();
        
        return view('conference-kits.index', compact(
            'conferenceKit',
            'sessionLinks',
            'contacts',
            'cityGuide',
            'participant'
        ));
    }
    
    /**
     * Download conference kit as PDF
     */
    public function download(ConferenceKit $conferenceKit)
    {
        $user = Auth::user();
        
        // Verify participant has access to this kit
        $participant = Participant::where('user_id', $user->id)
            ->where('conference_id', $conferenceKit->conference_id)
            ->where('registration_status', 'approved')
            ->first();
            
        if (!$participant) {
            abort(403, 'Access denied.');
        }
        
        // Load kit items
        $conferenceKit->load(['conferenceKitItems', 'conference']);
        
        // Organize items by type
        $sessionLinks = $conferenceKit->conferenceKitItems->where('type', 'SessionLink');
        $contacts = $conferenceKit->conferenceKitItems->where('type', 'Contact');
        $cityGuide = $conferenceKit->conferenceKitItems->where('type', 'CityGuide')->first();
        
        // Generate PDF
        $pdf = Pdf::loadView('conference-kits.pdf', compact(
            'conferenceKit',
            'sessionLinks',
            'contacts',
            'cityGuide',
            'participant'
        ));
        
        $filename = 'conference_kit_' . $conferenceKit->conference->name . '_' . date('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }
    
    /**
     * Admin: List all conference kits
     */
    public function adminIndex()
    {
        $conferenceKits = ConferenceKit::with(['conference', 'conferenceKitItems'])
            ->latest()
            ->paginate(10);
            
        return view('conference-kits.admin.index', compact('conferenceKits'));
    }
    
    /**
     * Admin: Show conference kit details
     */
    public function show(ConferenceKit $conferenceKit)
    {
        $conferenceKit->load(['conference', 'conferenceKitItems']);
        
        // Organize items by type
        $sessionLinks = $conferenceKit->conferenceKitItems->where('type', 'SessionLink');
        $contacts = $conferenceKit->conferenceKitItems->where('type', 'Contact');
        $cityGuide = $conferenceKit->conferenceKitItems->where('type', 'CityGuide');
        
        return view('conference-kits.admin.show', compact(
            'conferenceKit',
            'sessionLinks',
            'contacts',
            'cityGuide'
        ));
    }
    
    /**
     * Admin: Create conference kit
     */
    public function create()
    {
        $conferences = Conference::all();
        return view('conference-kits.admin.create', compact('conferences'));
    }
    
    /**
     * Admin: Store conference kit
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'conference_id' => 'required|exists:conferences,id',
            'session_links' => 'array',
            'session_links.*.title' => 'required|string',
            'session_links.*.time' => 'required|string',
            'session_links.*.room' => 'required|string',
            'session_links.*.speaker' => 'required|string',
            'session_links.*.description' => 'required|string',
            'session_links.*.zoom_link' => 'nullable|url',
            'contacts' => 'array',
            'contacts.*.name' => 'required|string',
            'contacts.*.email' => 'required|email',
            'contacts.*.phone' => 'nullable|string',
            'contacts.*.role' => 'required|string',
            'contacts.*.availability' => 'nullable|string',
            'city_guide' => 'array',
            'city_guide.title' => 'required|string',
            'city_guide.description' => 'required|string',
            'city_guide.transportation' => 'nullable|string',
            'city_guide.restaurants' => 'nullable|string',
            'city_guide.attractions' => 'nullable|string',
            'city_guide.emergency_contacts' => 'nullable|string',
        ]);
        
        // Create conference kit
        $conferenceKit = ConferenceKit::create([
            'conference_id' => $validated['conference_id'],
        ]);
        
        // Create session links
        if (isset($validated['session_links'])) {
            foreach ($validated['session_links'] as $sessionLink) {
                ConferenceKitItem::create([
                    'kit_id' => $conferenceKit->id,
                    'type' => 'SessionLink',
                    'content' => json_encode($sessionLink),
                ]);
            }
        }
        
        // Create contacts
        if (isset($validated['contacts'])) {
            foreach ($validated['contacts'] as $contact) {
                ConferenceKitItem::create([
                    'kit_id' => $conferenceKit->id,
                    'type' => 'Contact',
                    'content' => json_encode($contact),
                ]);
            }
        }
        
        // Create city guide
        if (isset($validated['city_guide'])) {
            ConferenceKitItem::create([
                'kit_id' => $conferenceKit->id,
                'type' => 'CityGuide',
                'content' => json_encode($validated['city_guide']),
            ]);
        }
        
        return redirect()->route('conference-kits.admin.index')
            ->with('success', 'Conference kit created successfully.');
    }
    
    /**
     * Admin: Edit conference kit
     */
    public function edit(ConferenceKit $conferenceKit)
    {
        $conferenceKit->load(['conference', 'conferenceKitItems']);
        $conferences = Conference::all();
        
        // Organize items by type
        $sessionLinks = $conferenceKit->conferenceKitItems->where('type', 'SessionLink');
        $contacts = $conferenceKit->conferenceKitItems->where('type', 'Contact');
        $cityGuide = $conferenceKit->conferenceKitItems->where('type', 'CityGuide')->first();
        
        return view('conference-kits.admin.edit', compact(
            'conferenceKit',
            'conferences',
            'sessionLinks',
            'contacts',
            'cityGuide'
        ));
    }
    
    /**
     * Admin: Update conference kit
     */
    public function update(Request $request, ConferenceKit $conferenceKit)
    {
        $validated = $request->validate([
            'conference_id' => 'required|exists:conferences,id',
            'session_links' => 'array',
            'session_links.*.title' => 'required|string',
            'session_links.*.time' => 'required|string',
            'session_links.*.room' => 'required|string',
            'session_links.*.speaker' => 'required|string',
            'session_links.*.description' => 'required|string',
            'session_links.*.zoom_link' => 'nullable|url',
            'contacts' => 'array',
            'contacts.*.name' => 'required|string',
            'contacts.*.email' => 'required|email',
            'contacts.*.phone' => 'nullable|string',
            'contacts.*.role' => 'required|string',
            'contacts.*.availability' => 'nullable|string',
            'city_guide' => 'array',
            'city_guide.title' => 'required|string',
            'city_guide.description' => 'required|string',
            'city_guide.transportation' => 'nullable|string',
            'city_guide.restaurants' => 'nullable|string',
            'city_guide.attractions' => 'nullable|string',
            'city_guide.emergency_contacts' => 'nullable|string',
        ]);
        
        // Update conference kit
        $conferenceKit->update([
            'conference_id' => $validated['conference_id'],
        ]);
        
        // Delete existing items
        $conferenceKit->conferenceKitItems()->delete();
        
        // Create session links
        if (isset($validated['session_links'])) {
            foreach ($validated['session_links'] as $sessionLink) {
                ConferenceKitItem::create([
                    'kit_id' => $conferenceKit->id,
                    'type' => 'SessionLink',
                    'content' => json_encode($sessionLink),
                ]);
            }
        }
        
        // Create contacts
        if (isset($validated['contacts'])) {
            foreach ($validated['contacts'] as $contact) {
                ConferenceKitItem::create([
                    'kit_id' => $conferenceKit->id,
                    'type' => 'Contact',
                    'content' => json_encode($contact),
                ]);
            }
        }
        
        // Create city guide
        if (isset($validated['city_guide'])) {
            ConferenceKitItem::create([
                'kit_id' => $conferenceKit->id,
                'type' => 'CityGuide',
                'content' => json_encode($validated['city_guide']),
            ]);
        }
        
        return redirect()->route('conference-kits.admin.index')
            ->with('success', 'Conference kit updated successfully.');
    }
    
    /**
     * Admin: Delete conference kit
     */
    public function destroy(ConferenceKit $conferenceKit)
    {
        $conferenceKit->conferenceKitItems()->delete();
        $conferenceKit->delete();
        
        return redirect()->route('conference-kits.admin.index')
            ->with('success', 'Conference kit deleted successfully.');
    }
}
