<?php

namespace App\Http\Controllers;

use App\Models\ConferenceDoc;
use App\Models\ConferenceDocItem;
use App\Models\Conference;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class ConferenceDocController extends Controller
{
    /**
     * Display conference docs (admin resource route)
     */
    public function index()
    {
        // Check if user is admin/superadmin - if so, show admin interface
        $user = Auth::user();
        if ($user->hasRole('admin') || $user->hasRole('superadmin') || $user->hasRole('organizer')) {
            return $this->adminIndex();
        }
        
        // Otherwise, show participant interface
        return $this->participantIndex();
    }
    
    /**
     * Display conference kit for participants
     */
    public function participantIndex()
    {
        $user = Auth::user();
        
        // Get active participant profile
        $participant = $user->getActiveParticipantProfile();
            
        if (!$participant) {
            return redirect()->route('participant-profiles.index')->with('error', 'No active participant profile found. Please create or select a participant profile.');
        }
        
        // Get conference docs for this specific participant
        $conferenceDoc = ConferenceDoc::with(['conferenceDocItems'])
            ->where('participant_id', $participant->id)
            ->where('conference_id', $participant->conference_id)
            ->first();
            
        if (!$conferenceDoc) {
            return redirect()->route('participant-dashboard')->with('error', 'Conference docs not available yet.');
        }
        
        // Organize doc items by type
        $sessionLinks = $conferenceDoc->conferenceDocItems->where('type', 'SessionLink');
        $contacts = $conferenceDoc->conferenceDocItems->where('type', 'Contact');
        $cityGuide = $conferenceDoc->conferenceDocItems->where('type', 'CityGuide')->first();
        $mediaFiles = $conferenceDoc->conferenceDocItems->where('type', 'MediaFile');
        
        return view('conference-docs.index', compact(
            'conferenceDoc',
            'sessionLinks',
            'contacts',
            'cityGuide',
            'mediaFiles',
            'participant'
        ));
    }
    
    /**
     * Download conference docs as PDF
     */
    public function download(ConferenceDoc $conferenceDoc)
    {
        $user = Auth::user();
        
        // Get active participant profile
        $participant = $user->getActiveParticipantProfile();
            
        if (!$participant) {
            abort(403, 'No active participant profile found.');
        }
        
        // Verify participant has access to this doc
        if ($conferenceDoc->participant_id !== $participant->id) {
            abort(403, 'Access denied to this conference document.');
        }
        
        // Load doc items
        $conferenceDoc->load(['conferenceDocItems', 'conference']);
        
        // Organize items by type
        $sessionLinks = $conferenceDoc->conferenceDocItems->where('type', 'SessionLink');
        $contacts = $conferenceDoc->conferenceDocItems->where('type', 'Contact');
        $cityGuide = $conferenceDoc->conferenceDocItems->where('type', 'CityGuide')->first();
        $mediaFiles = $conferenceDoc->conferenceDocItems->where('type', 'MediaFile');
        
        // Generate PDF
        $pdf = Pdf::loadView('conference-docs.pdf', compact(
            'conferenceDoc',
            'sessionLinks',
            'contacts',
            'cityGuide',
            'mediaFiles',
            'participant'
        ));
        
        $filename = 'conference_docs_' . $conferenceDoc->conference->name . '_' . date('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }
    
    /**
     * Admin: List all conference docs
     */
    public function adminIndex()
    {
        $conferenceDocs = ConferenceDoc::with(['conference', 'conferenceDocItems'])
            ->latest()
            ->paginate(10);
            
        return view('conference-docs.admin.index', compact('conferenceDocs'));
    }
    
    /**
     * Admin: Show conference doc details
     */
    public function show(ConferenceDoc $conferenceDoc)
    {
        $conferenceDoc->load(['conference', 'conferenceDocItems']);
        
        // Organize items by type
        $sessionLinks = $conferenceDoc->conferenceDocItems->where('type', 'SessionLink');
        $contacts = $conferenceDoc->conferenceDocItems->where('type', 'Contact');
        $cityGuide = $conferenceDoc->conferenceDocItems->where('type', 'CityGuide');
        $mediaFiles = $conferenceDoc->conferenceDocItems->where('type', 'MediaFile');
        
        return view('conference-docs.admin.show', compact(
            'conferenceDoc',
            'sessionLinks',
            'contacts',
            'cityGuide',
            'mediaFiles'
        ));
    }
    
    /**
     * Admin: Create conference doc
     */
    public function create()
    {
        $conferences = Conference::all();
        return view('conference-docs.admin.create', compact('conferences'));
    }
    
    /**
     * Admin: Store conference doc
     */
    public function store(Request $request)
    {
        try {
            // Debug: Log the incoming request data
            \Log::info('Conference Doc Store Request', [
                'all_data' => $request->all(),
                'user_id' => auth()->id(),
                'user_roles' => auth()->user()->roles->pluck('name')->toArray()
            ]);
            
            // Clean up empty arrays before validation
            $data = $request->all();
            
            // Remove empty session links
            if (isset($data['session_links'])) {
                $data['session_links'] = array_filter($data['session_links'], function($link) {
                    return !empty(array_filter($link, function($value) {
                        return $value !== null && $value !== '';
                    }));
                });
                if (empty($data['session_links'])) {
                    unset($data['session_links']);
                }
            }
            
            // Remove empty contacts
            if (isset($data['contacts'])) {
                $data['contacts'] = array_filter($data['contacts'], function($contact) {
                    return !empty(array_filter($contact, function($value) {
                        return $value !== null && $value !== '';
                    }));
                });
                if (empty($data['contacts'])) {
                    unset($data['contacts']);
                }
            }
            
            // Remove empty city guide
            if (isset($data['city_guide'])) {
                $cityGuide = array_filter($data['city_guide'], function($value) {
                    return $value !== null && $value !== '';
                });
                if (empty($cityGuide)) {
                    unset($data['city_guide']);
                } else {
                    $data['city_guide'] = $cityGuide;
                }
            }
            
            // Preserve files from original request
            $files = $request->allFiles();
            
            \Log::info('Conference Doc Cleaned Data', [
                'cleaned_data' => $data,
                'files_count' => count($files)
            ]);
            
            // Create a new request with cleaned data for validation
            $cleanRequest = new \Illuminate\Http\Request($data);
            $cleanRequest->setLaravelSession($request->getSession());
            $cleanRequest->setUserResolver($request->getUserResolver());
            
            $validated = $cleanRequest->validate([
                'conference_id' => 'required|exists:conferences,id',
                'session_links' => 'nullable|array',
                'session_links.*.title' => 'nullable|string',
                'session_links.*.time' => 'nullable|string',
                'session_links.*.room' => 'nullable|string',
                'session_links.*.speaker' => 'nullable|string',
                'session_links.*.description' => 'nullable|string',
                'session_links.*.zoom_link' => 'nullable|url',
                'contacts' => 'nullable|array',
                'contacts.*.name' => 'nullable|string',
                'contacts.*.email' => 'nullable|email',
                'contacts.*.phone' => 'nullable|string',
                'contacts.*.role' => 'nullable|string',
                'contacts.*.availability' => 'nullable|string',
                'city_guide' => 'nullable|array',
                'city_guide.title' => 'nullable|string',
                'city_guide.description' => 'nullable|string',
                'city_guide.transportation' => 'nullable|string',
                'city_guide.restaurants' => 'nullable|string',
                'city_guide.attractions' => 'nullable|string',
                'city_guide.emergency_contacts' => 'nullable|string',
                'files' => 'nullable|array',
                'files.*' => 'file|max:10240', // 10MB max per file
                'file_description' => 'nullable|string|max:255',
            ]);
        
        // Create conference doc
        $conferenceDoc = ConferenceDoc::create([
            'conference_id' => $validated['conference_id'],
        ]);
        
        // Create session links
        if (isset($validated['session_links'])) {
            foreach ($validated['session_links'] as $sessionLink) {
                ConferenceDocItem::create([
                    'doc_id' => $conferenceDoc->id,
                    'type' => 'SessionLink',
                    'content' => json_encode($sessionLink),
                ]);
            }
        }
        
        // Create contacts
        if (isset($validated['contacts'])) {
            foreach ($validated['contacts'] as $contact) {
                ConferenceDocItem::create([
                    'doc_id' => $conferenceDoc->id,
                    'type' => 'Contact',
                    'content' => json_encode($contact),
                ]);
            }
        }
        
        // Create city guide
        if (isset($validated['city_guide'])) {
            ConferenceDocItem::create([
                'doc_id' => $conferenceDoc->id,
                'type' => 'CityGuide',
                'content' => json_encode($validated['city_guide']),
            ]);
        }
        
        // Handle file uploads (use original request to preserve files)
        if (isset($files['files']) && !empty($files['files'])) {
            foreach ($files['files'] as $file) {
                // Generate unique filename
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = "conference-docs/{$conferenceDoc->conference_id}/" . $this->getFileTypeFolder($file->getMimeType());
                
                // Store file
                $filePath = $file->storeAs($path, $filename, 'public');
                
                // Create database record
                ConferenceDocItem::create([
                    'doc_id' => $conferenceDoc->id,
                    'type' => 'MediaFile',
                    'content' => json_encode([
                        'description' => $request->input('file_description') ?? '',
                        'original_name' => $file->getClientOriginalName(),
                    ]),
                    'file_path' => $filePath,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'uploaded_by' => Auth::id(),
                    'is_public' => true,
                ]);
            }
        }
        
        \Log::info('Conference Doc Created Successfully', [
            'conference_doc_id' => $conferenceDoc->id,
            'conference_id' => $conferenceDoc->conference_id,
            'items_count' => $conferenceDoc->conferenceDocItems()->count()
        ]);
        
            return redirect()->route('conference-docs.index')
                ->with('success', 'Conference doc created successfully.');
                
        } catch (\Exception $e) {
            \Log::error('Conference Doc Creation Failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request_data' => $request->all()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create conference doc: ' . $e->getMessage());
        }
    }
    
    /**
     * Admin: Edit conference doc
     */
    public function edit(ConferenceDoc $conferenceDoc)
    {
        $conferenceDoc->load(['conference', 'conferenceDocItems']);
        $conferences = Conference::all();
        
        // Organize items by type
        $sessionLinks = $conferenceDoc->conferenceDocItems->where('type', 'SessionLink');
        $contacts = $conferenceDoc->conferenceDocItems->where('type', 'Contact');
        $cityGuide = $conferenceDoc->conferenceDocItems->where('type', 'CityGuide')->first();
        $mediaFiles = $conferenceDoc->conferenceDocItems->where('type', 'MediaFile');
        
        return view('conference-docs.admin.edit', compact(
            'conferenceDoc',
            'conferences',
            'sessionLinks',
            'contacts',
            'cityGuide',
            'mediaFiles'
        ));
    }
    
    /**
     * Admin: Update conference doc
     */
    public function update(Request $request, ConferenceDoc $conferenceDoc)
    {
        try {
            \Log::info('Conference Doc Update Request', [
                'conference_doc_id' => $conferenceDoc->id,
                'all_data' => $request->all(),
                'user_id' => auth()->id(),
            ]);
            
            // Clean up empty arrays before validation
            $data = $request->all();
            
            // Remove empty session links
            if (isset($data['session_links'])) {
                $data['session_links'] = array_filter($data['session_links'], function($link) {
                    return !empty(array_filter($link, function($value) {
                        return $value !== null && $value !== '';
                    }));
                });
                if (empty($data['session_links'])) {
                    unset($data['session_links']);
                }
            }
            
            // Remove empty contacts
            if (isset($data['contacts'])) {
                $data['contacts'] = array_filter($data['contacts'], function($contact) {
                    return !empty(array_filter($contact, function($value) {
                        return $value !== null && $value !== '';
                    }));
                });
                if (empty($data['contacts'])) {
                    unset($data['contacts']);
                }
            }
            
            // Remove empty city guide
            if (isset($data['city_guide'])) {
                $cityGuide = array_filter($data['city_guide'], function($value) {
                    return $value !== null && $value !== '';
                });
                if (empty($cityGuide)) {
                    unset($data['city_guide']);
                } else {
                    $data['city_guide'] = $cityGuide;
                }
            }
            
            // Preserve files from original request
            $files = $request->allFiles();
            
            \Log::info('Conference Doc Update Cleaned Data', [
                'conference_doc_id' => $conferenceDoc->id,
                'cleaned_data' => $data,
                'files_count' => count($files)
            ]);
            
            // Create a new request with cleaned data for validation
            $cleanRequest = new \Illuminate\Http\Request($data);
            $cleanRequest->setLaravelSession($request->getSession());
            $cleanRequest->setUserResolver($request->getUserResolver());
            
            $validated = $cleanRequest->validate([
                'conference_id' => 'required|exists:conferences,id',
                'session_links' => 'nullable|array',
                'session_links.*.title' => 'nullable|string',
                'session_links.*.time' => 'nullable|string',
                'session_links.*.room' => 'nullable|string',
                'session_links.*.speaker' => 'nullable|string',
                'session_links.*.description' => 'nullable|string',
                'session_links.*.zoom_link' => 'nullable|url',
                'contacts' => 'nullable|array',
                'contacts.*.name' => 'nullable|string',
                'contacts.*.email' => 'nullable|email',
                'contacts.*.phone' => 'nullable|string',
                'contacts.*.role' => 'nullable|string',
                'contacts.*.availability' => 'nullable|string',
                'city_guide' => 'nullable|array',
                'city_guide.title' => 'nullable|string',
                'city_guide.description' => 'nullable|string',
                'city_guide.transportation' => 'nullable|string',
                'city_guide.restaurants' => 'nullable|string',
                'city_guide.attractions' => 'nullable|string',
                'city_guide.emergency_contacts' => 'nullable|string',
                'files' => 'nullable|array',
                'files.*' => 'file|max:10240', // 10MB max per file
                'file_description' => 'nullable|string|max:255',
            ]);
            
            // Update conference doc
            $conferenceDoc->update([
                'conference_id' => $validated['conference_id'],
            ]);
            
            // Delete existing non-media items (keep media files)
            $conferenceDoc->conferenceDocItems()->where('type', '!=', 'MediaFile')->delete();
            
            // Create session links
            if (isset($validated['session_links'])) {
                foreach ($validated['session_links'] as $sessionLink) {
                    ConferenceDocItem::create([
                        'doc_id' => $conferenceDoc->id,
                        'type' => 'SessionLink',
                        'content' => json_encode($sessionLink),
                    ]);
                }
            }
            
            // Create contacts
            if (isset($validated['contacts'])) {
                foreach ($validated['contacts'] as $contact) {
                    ConferenceDocItem::create([
                        'doc_id' => $conferenceDoc->id,
                        'type' => 'Contact',
                        'content' => json_encode($contact),
                    ]);
                }
            }
            
            // Create city guide
            if (isset($validated['city_guide'])) {
                ConferenceDocItem::create([
                    'doc_id' => $conferenceDoc->id,
                    'type' => 'CityGuide',
                    'content' => json_encode($validated['city_guide']),
                ]);
            }
            
            // Handle file uploads (use original request to preserve files)
            if (isset($files['files']) && !empty($files['files'])) {
                foreach ($files['files'] as $file) {
                    // Generate unique filename
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = "conference-docs/{$conferenceDoc->conference_id}/" . $this->getFileTypeFolder($file->getMimeType());
                    
                    // Store file
                    $filePath = $file->storeAs($path, $filename, 'public');
                    
                    // Create database record
                    ConferenceDocItem::create([
                        'doc_id' => $conferenceDoc->id,
                        'type' => 'MediaFile',
                        'content' => json_encode([
                            'description' => $request->input('file_description') ?? '',
                            'original_name' => $file->getClientOriginalName(),
                        ]),
                        'file_path' => $filePath,
                        'file_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'uploaded_by' => Auth::id(),
                        'is_public' => true,
                    ]);
                }
            }
            
            \Log::info('Conference Doc Update Completed Successfully', [
                'conference_doc_id' => $conferenceDoc->id,
                'conference_id' => $conferenceDoc->conference_id,
                'items_count' => $conferenceDoc->conferenceDocItems()->count()
            ]);
            
            return redirect()->route('conference-docs.index')
                ->with('success', 'Conference doc updated successfully.');
                
        } catch (\Exception $e) {
            \Log::error('Conference Doc Update Failed', [
                'conference_doc_id' => $conferenceDoc->id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request_data' => $request->all()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update conference doc: ' . $e->getMessage());
        }
    }
    
    /**
     * Admin: Delete conference doc
     */
    public function destroy(ConferenceDoc $conferenceDoc)
    {
        $conferenceDoc->conferenceDocItems()->delete();
        $conferenceDoc->delete();
        
        return redirect()->route('conference-docs.index')
            ->with('success', 'Conference doc deleted successfully.');
    }

    /**
     * Upload media files to conference doc
     */
    public function uploadMedia(Request $request, ConferenceDoc $conferenceDoc)
    {
        // Check permissions
        if (!$this->canUploadMedia()) {
            return redirect()->back()->with('error', 'You do not have permission to upload media files.');
        }

        $validated = $request->validate([
            'files.*' => 'required|file|max:10240', // 10MB max per file
            'description' => 'nullable|string|max:255',
        ]);

        $uploadedFiles = [];

        foreach ($request->file('files') as $file) {
            // Generate unique filename
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = "conference-docs/{$conferenceDoc->conference_id}/" . $this->getFileTypeFolder($file->getMimeType());
            
            // Store file
            $filePath = $file->storeAs($path, $filename, 'public');
            
            // Create database record
            $docItem = ConferenceDocItem::create([
                'doc_id' => $conferenceDoc->id,
                'type' => 'MediaFile',
                'content' => json_encode([
                    'description' => $validated['description'] ?? '',
                    'original_name' => $file->getClientOriginalName(),
                ]),
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'uploaded_by' => Auth::id(),
                'is_public' => true,
            ]);

            $uploadedFiles[] = $docItem;
        }

        return redirect()->back()->with('success', 'Media files uploaded successfully.');
    }

    /**
     * Download media file
     */
    public function downloadMedia(ConferenceDoc $conferenceDoc, ConferenceDocItem $docItem)
    {
        $user = Auth::user();
        
        // Check if user is admin/organizer (has full access)
        if ($user->hasRole('superadmin') || $user->hasRole('admin') || $user->hasRole('organizer')) {
            // Admin/organizer has access to all files
        } else {
            // Check if user is a participant with approved registration
            $participant = Participant::where('user_id', $user->id)
                ->where('conference_id', $conferenceDoc->conference_id)
                ->where('registration_status', 'approved')
                ->first();
                
            if (!$participant) {
                abort(403, 'Access denied. You must be registered for this conference to download files.');
            }
        }

        if (!$docItem->file_path || !Storage::disk('public')->exists($docItem->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->download($docItem->file_path, $docItem->file_name);
    }

    /**
     * Delete media file
     */
    public function deleteMedia(ConferenceDoc $conferenceDoc, ConferenceDocItem $docItem)
    {
        // Check permissions
        if (!$this->canUploadMedia()) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'You do not have permission to delete media files.'], 403);
            }
            return redirect()->back()->with('error', 'You do not have permission to delete media files.');
        }

        $docItem->delete(); // This will also delete the file from storage

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Media file deleted successfully.']);
        }

        return redirect()->back()->with('success', 'Media file deleted successfully.');
    }

    /**
     * Check if user can upload media files
     */
    private function canUploadMedia()
    {
        $user = Auth::user();
        return $user->hasRole('superadmin') || $user->hasRole('admin') || $user->hasRole('organizer');
    }

    /**
     * Get folder name based on file type
     */
    private function getFileTypeFolder($mimeType)
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'images';
        } elseif (str_starts_with($mimeType, 'video/')) {
            return 'videos';
        } elseif (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        } elseif (in_array($mimeType, [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain',
            'application/rtf',
        ])) {
            return 'documents';
        } else {
            return 'other';
        }
    }
}
