<?php

namespace App\Http\Controllers;

use App\Models\IdCard;
use App\Models\User;
use App\Models\Participant;
use App\Models\Conference;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
// use SimpleSoftwareIO\QrCode\Facades\QrCode;

class IdCardController extends Controller
{
    /**
     * Display a listing of ID card templates
     */
    public function index()
    {
        $idCards = IdCard::with('conference')->latest()->get();
        return view('id-cards.index', compact('idCards'));
    }

    /**
     * Show the form for creating a new ID card template
     */
    public function create()
    {
        $conferences = Conference::all();
        return view('id-cards.create', compact('conferences'));
    }

    /**
     * Store a newly created ID card template
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:participant,company_worker',
            'conference_id' => 'nullable|exists:conferences,id',
            'background_color' => 'required|string|max:7',
            'text_color' => 'required|string|max:7',
            'accent_color' => 'required|string|max:7',
            'include_qr_code' => 'boolean',
            'include_photo' => 'boolean',
        ]);

        // Set conference_id to null for company worker cards
        if ($validated['type'] === 'company_worker') {
            $validated['conference_id'] = null;
        }

        IdCard::create($validated);

        return redirect()->route('id-cards.index')
            ->with('success', 'ID card template created successfully.');
    }

    /**
     * Display the specified ID card template
     */
    public function show(IdCard $idCard)
    {
        return view('id-cards.show', compact('idCard'));
    }

    /**
     * Show the form for editing the specified ID card template
     */
    public function edit(IdCard $idCard)
    {
        $conferences = Conference::all();
        return view('id-cards.edit', compact('idCard', 'conferences'));
    }

    /**
     * Update the specified ID card template
     */
    public function update(Request $request, IdCard $idCard)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:participant,company_worker',
            'conference_id' => 'nullable|exists:conferences,id',
            'background_color' => 'required|string|max:7',
            'text_color' => 'required|string|max:7',
            'accent_color' => 'required|string|max:7',
            'include_qr_code' => 'boolean',
            'include_photo' => 'boolean',
        ]);

        // Set conference_id to null for company worker cards
        if ($validated['type'] === 'company_worker') {
            $validated['conference_id'] = null;
        }

        $idCard->update($validated);

        return redirect()->route('id-cards.index')
            ->with('success', 'ID card template updated successfully.');
    }

    /**
     * Remove the specified ID card template
     */
    public function destroy(IdCard $idCard)
    {
        $idCard->delete();

        return redirect()->route('id-cards.index')
            ->with('success', 'ID card template deleted successfully.');
    }

    /**
     * Generate ID card for a specific user
     */
    public function generateForUser(Request $request, User $user)
    {
        $request->validate([
            'id_card_id' => 'required|exists:id_cards,id',
        ]);

        $idCard = IdCard::findOrFail($request->id_card_id);
        
        return $this->generateCard($user, $idCard);
    }

    /**
     * Generate ID card for a participant
     */
    public function generateForParticipant(Request $request, Participant $participant)
    {
        $request->validate([
            'id_card_id' => 'required|exists:id_cards,id',
        ]);

        $idCard = IdCard::findOrFail($request->id_card_id);
        
        return $this->generateCard($participant->user, $idCard, $participant);
    }

    /**
     * Generate ID card for current user
     */
    public function generateMyCard(Request $request)
    {
        $user = auth()->user();
        
        // Determine card type based on user roles
        $isParticipant = $user->hasRole('attendee') || $user->hasRole('speaker');
        $isCompanyWorker = $user->hasRole('admin') || $user->hasRole('superadmin') || $user->hasRole('organizer');
        
        if ($isParticipant) {
            // Get the first active participant card template
            $idCard = IdCard::active()->byType('participant')->first();
            if (!$idCard) {
                return back()->with('error', 'No participant ID card template found.');
            }
            
            // Get the first participant record for this user
            $participant = $user->participants()->first();
            if (!$participant) {
                return back()->with('error', 'No participant record found for this user.');
            }
            
            return $this->generateCard($user, $idCard, $participant);
        } elseif ($isCompanyWorker) {
            // Get the first active company worker card template
            $idCard = IdCard::active()->byType('company_worker')->first();
            if (!$idCard) {
                return back()->with('error', 'No company worker ID card template found.');
            }
            
            return $this->generateCard($user, $idCard);
        }
        
        return back()->with('error', 'Unable to determine user type for ID card generation.');
    }

    /**
     * Generate ID card for all participants in a conference
     */
    public function generateForConference(Request $request, Conference $conference)
    {
        $request->validate([
            'id_card_id' => 'required|exists:id_cards,id',
        ]);

        $idCard = IdCard::findOrFail($request->id_card_id);
        
        if (!$idCard->isParticipantCard()) {
            return back()->with('error', 'Selected template is not for participants.');
        }

        $participants = $conference->participants()->with('user')->get();
        
        if ($participants->isEmpty()) {
            return back()->with('error', 'No participants found for this conference.');
        }

        // Generate cards for all participants
        $cards = [];
        foreach ($participants as $participant) {
            $cards[] = $this->generateCardData($participant->user, $idCard, $participant);
        }

        // Generate PDF with multiple cards
        $pdf = Pdf::loadView('id-cards.bulk-pdf', compact('cards', 'conference'));
        $pdf->setPaper('A4', 'portrait');

        $filename = 'id_cards_' . $conference->name . '_' . date('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Generate a single ID card
     */
    private function generateCard(User $user, IdCard $idCard, Participant $participant = null)
    {
        $cardData = $this->generateCardData($user, $idCard, $participant);
        
        $pdf = Pdf::loadView('id-cards.single-pdf', compact('cardData'));
        $pdf->setPaper([0, 0, 324, 204], 'portrait'); // 3.375" x 2.125" in points
        
        $filename = 'id_card_' . $user->first_name . '_' . $user->last_name . '_' . date('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Generate card data for a user
     */
    private function generateCardData(User $user, IdCard $idCard, Participant $participant = null)
    {
        $cardData = [
            'user' => $user,
            'idCard' => $idCard,
            'participant' => $participant,
            'qrCode' => null,
            'photoUrl' => null,
        ];

        // Generate QR code if enabled
        if ($idCard->include_qr_code) {
            $qrData = [
                'user_id' => $user->id,
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'type' => $idCard->type,
            ];
            
            if ($participant) {
                $qrData['participant_id'] = $participant->id;
                $qrData['conference_id'] = $participant->conference_id;
            }
            
            // For now, we'll create a placeholder QR code
            // TODO: Install QR code package when PHP version is updated
            $cardData['qrCode'] = null; // Placeholder for QR code
        }

        // Get user photo if enabled
        if ($idCard->include_photo && $user->profile_picture) {
            $cardData['photoUrl'] = Storage::url($user->profile_picture);
        }

        return $cardData;
    }

    /**
     * Toggle ID card template active status
     */
    public function toggleStatus(IdCard $idCard)
    {
        $idCard->update(['is_active' => !$idCard->is_active]);
        
        return back()->with('success', 'ID card template status updated successfully.');
    }
}
