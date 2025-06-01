<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\ParticipantType;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SpeakerRegistrationController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.speaker-register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:participants,email',
            'phone' => 'required|string|max:20',
            'organization' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'bio' => 'required|string|min:100',
            'expertise' => 'required|string|max:255',
            'session_title' => 'required|string|max:255',
            'session_description' => 'required|string|min:100',
            'session_duration' => 'required|integer|min:15|max:180',
            'cv' => 'required|file|mimes:pdf|max:10240', // 10MB max
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048', // 2MB max
        ]);

        try {
            DB::beginTransaction();

            // Upload files
            $cvPath = $request->file('cv')->store('speakers/cv', 'public');
            $photoPath = $request->file('photo')->store('speakers/photos', 'public');

            // Get speaker participant type
            $speakerType = ParticipantType::where('name', 'Speaker')->firstOrFail();

            // Create participant record
            $participant = Participant::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'organization' => $validated['organization'],
                'position' => $validated['position'],
                'bio' => $validated['bio'],
                'expertise' => $validated['expertise'],
                'participant_type_id' => $speakerType->id,
                'cv_path' => $cvPath,
                'photo_path' => $photoPath,
                'status' => 'pending', // Will be reviewed by admin
            ]);

            // Create session proposal
            Session::create([
                'title' => $validated['session_title'],
                'description' => $validated['session_description'],
                'duration' => $validated['session_duration'],
                'speaker_id' => $participant->id,
                'status' => 'pending', // Will be reviewed by admin
            ]);

            DB::commit();

            return redirect()->route('speaker.registration.success')
                ->with('success', 'Your registration has been submitted successfully. We will review your application and get back to you soon.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete uploaded files if they exist
            if (isset($cvPath)) Storage::disk('public')->delete($cvPath);
            if (isset($photoPath)) Storage::disk('public')->delete($photoPath);

            return back()->withInput()
                ->withErrors(['error' => 'An error occurred while processing your registration. Please try again.']);
        }
    }

    public function success()
    {
        return view('auth.speaker-registration-success');
    }
} 