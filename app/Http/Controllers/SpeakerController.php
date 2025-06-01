<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use Illuminate\Http\Request;

class SpeakerController extends Controller
{
    // List all registered speakers
    public function index()
    {
        $speakers = Participant::whereHas('participantType', function($q) {
            $q->where('name', 'Speaker');
        })->latest()->paginate(20);

        return view('speakers.index', compact('speakers'));
    }
} 