<?php

namespace Database\Seeders;

use App\Models\Conference;
use App\Models\Session;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpcomingConferenceSessionsSeeder extends Seeder
{
    public function run(): void
    {
        // Get participant type IDs
        $speakerTypeId = DB::table('participant_types')->where('name', 'speaker')->value('id');
        $attendeeTypeId = DB::table('participant_types')->where('name', 'attendee')->value('id');

        // Get upcoming conferences
        $upcomingConferences = Conference::where('start_date', '>=', now())
            ->where('status', 'planned')
            ->get();

        foreach ($upcomingConferences as $conference) {
            $this->createSessionsForConference($conference, $speakerTypeId, $attendeeTypeId);
        }
    }

    private function createSessionsForConference($conference, $speakerTypeId, $attendeeTypeId)
    {
        $venueId = $conference->venue_id;
        $conferenceStartDate = \Carbon\Carbon::parse($conference->start_date);
        $conferenceEndDate = \Carbon\Carbon::parse($conference->end_date);

        // Create speakers for this conference
        $speakers = $this->createSpeakersForConference($conference->id, $speakerTypeId);

        // Session 1: Opening Keynote
        $keynoteSession = Session::firstOrCreate(
            ['title' => 'Opening Keynote: Future of Technology', 'conference_id' => $conference->id],
            [
                'description' => 'An inspiring keynote address exploring the future of technology and its impact on society.',
                'start_time' => $conferenceStartDate->copy()->setTime(9, 0),
                'end_time' => $conferenceStartDate->copy()->setTime(10, 30),
                'venue_id' => $venueId,
                'seating_arrangement' => 'Theater style - 500 seats'
            ]
        );

        // Assign keynote speaker
        if (isset($speakers[0])) {
            $keynoteSession->participants()->attach($speakers[0]->id, ['role' => 'Speaker']);
        }

        // Session 2: Panel Discussion
        $panelSession = Session::firstOrCreate(
            ['title' => 'Industry Leaders Panel Discussion', 'conference_id' => $conference->id],
            [
                'description' => 'Interactive panel discussion with industry leaders sharing insights and experiences.',
                'start_time' => $conferenceStartDate->copy()->setTime(11, 0),
                'end_time' => $conferenceStartDate->copy()->setTime(12, 30),
                'venue_id' => $venueId,
                'seating_arrangement' => 'Round table - 200 seats'
            ]
        );

        // Assign panelists
        if (isset($speakers[1])) {
            $panelSession->participants()->attach($speakers[1]->id, ['role' => 'Panelist']);
        }
        if (isset($speakers[2])) {
            $panelSession->participants()->attach($speakers[2]->id, ['role' => 'Panelist']);
        }
        if (isset($speakers[3])) {
            $panelSession->participants()->attach($speakers[3]->id, ['role' => 'Moderator']);
        }

        // Session 3: Workshop (Day 2)
        if ($conferenceEndDate->diffInDays($conferenceStartDate) >= 1) {
            $workshopSession = Session::firstOrCreate(
                ['title' => 'Hands-on Workshop: Practical Applications', 'conference_id' => $conference->id],
                [
                    'description' => 'Interactive workshop where participants can apply concepts learned during the conference.',
                    'start_time' => $conferenceStartDate->copy()->addDay()->setTime(10, 0),
                    'end_time' => $conferenceStartDate->copy()->addDay()->setTime(12, 0),
                    'venue_id' => $venueId,
                    'seating_arrangement' => 'Classroom style - 100 seats'
                ]
            );

            // Assign workshop facilitator
            if (isset($speakers[4])) {
                $workshopSession->participants()->attach($speakers[4]->id, ['role' => 'Speaker']);
            }
        }

        // Session 4: Closing Session
        $closingSession = Session::firstOrCreate(
            ['title' => 'Closing Remarks and Networking', 'conference_id' => $conference->id],
            [
                'description' => 'Closing session with final remarks and networking opportunities.',
                'start_time' => $conferenceEndDate->copy()->setTime(16, 0),
                'end_time' => $conferenceEndDate->copy()->setTime(17, 30),
                'venue_id' => $venueId,
                'seating_arrangement' => 'Reception style - 300 seats'
            ]
        );

        // Assign closing speaker
        if (isset($speakers[0])) {
            $closingSession->participants()->attach($speakers[0]->id, ['role' => 'Speaker']);
        }
    }

    private function createSpeakersForConference($conferenceId, $speakerTypeId)
    {
        $speakers = [];

        // Speaker 1: Keynote Speaker
        $user1 = User::firstOrCreate(
            ['email' => 'dr.sarah.johnson@example.com'],
            [
                'first_name' => 'Dr. Sarah',
                'last_name' => 'Johnson',
                'password' => bcrypt('password'),
            ]
        );

        $speaker1 = Participant::firstOrCreate(
            ['user_id' => $user1->id, 'conference_id' => $conferenceId],
            [
                'participant_type_id' => $speakerTypeId,
                'bio' => 'Dr. Sarah Johnson is a renowned technology expert with over 15 years of experience in AI and machine learning. She has published over 50 research papers and holds multiple patents.',
                'organization' => 'Tech Innovation Institute',
                'dietary_needs' => 'vegetarian',
                'travel_intent' => true,
                'approved' => true,
                'registration_status' => 'approved'
            ]
        );
        $speakers[] = $speaker1;

        // Speaker 2: Industry Expert
        $user2 = User::firstOrCreate(
            ['email' => 'michael.chen@example.com'],
            [
                'first_name' => 'Michael',
                'last_name' => 'Chen',
                'password' => bcrypt('password'),
            ]
        );

        $speaker2 = Participant::firstOrCreate(
            ['user_id' => $user2->id, 'conference_id' => $conferenceId],
            [
                'participant_type_id' => $speakerTypeId,
                'bio' => 'Michael Chen is a senior executive at Global Tech Solutions with expertise in digital transformation and business strategy.',
                'organization' => 'Global Tech Solutions',
                'dietary_needs' => 'none',
                'travel_intent' => false,
                'approved' => true,
                'registration_status' => 'approved'
            ]
        );
        $speakers[] = $speaker2;

        // Speaker 3: Research Scientist
        $user3 = User::firstOrCreate(
            ['email' => 'prof.emma.rodriguez@example.com'],
            [
                'first_name' => 'Prof. Emma',
                'last_name' => 'Rodriguez',
                'password' => bcrypt('password'),
            ]
        );

        $speaker3 = Participant::firstOrCreate(
            ['user_id' => $user3->id, 'conference_id' => $conferenceId],
            [
                'participant_type_id' => $speakerTypeId,
                'bio' => 'Professor Emma Rodriguez leads the Advanced Research Lab at State University, specializing in sustainable technology solutions.',
                'organization' => 'State University',
                'dietary_needs' => 'vegan',
                'travel_intent' => true,
                'approved' => true,
                'registration_status' => 'approved'
            ]
        );
        $speakers[] = $speaker3;

        // Speaker 4: Startup Founder
        $user4 = User::firstOrCreate(
            ['email' => 'alex.thompson@example.com'],
            [
                'first_name' => 'Alex',
                'last_name' => 'Thompson',
                'password' => bcrypt('password'),
            ]
        );

        $speaker4 = Participant::firstOrCreate(
            ['user_id' => $user4->id, 'conference_id' => $conferenceId],
            [
                'participant_type_id' => $speakerTypeId,
                'bio' => 'Alex Thompson is the founder and CEO of InnovateTech, a successful startup focused on renewable energy solutions.',
                'organization' => 'InnovateTech',
                'dietary_needs' => 'gluten-free',
                'travel_intent' => true,
                'approved' => true,
                'registration_status' => 'approved'
            ]
        );
        $speakers[] = $speaker4;

        // Speaker 5: Workshop Facilitator
        $user5 = User::firstOrCreate(
            ['email' => 'dr.james.wilson@example.com'],
            [
                'first_name' => 'Dr. James',
                'last_name' => 'Wilson',
                'password' => bcrypt('password'),
            ]
        );

        $speaker5 = Participant::firstOrCreate(
            ['user_id' => $user5->id, 'conference_id' => $conferenceId],
            [
                'participant_type_id' => $speakerTypeId,
                'bio' => 'Dr. James Wilson is a certified trainer and consultant with expertise in practical applications of emerging technologies.',
                'organization' => 'Tech Training Institute',
                'dietary_needs' => 'none',
                'travel_intent' => false,
                'approved' => true,
                'registration_status' => 'approved'
            ]
        );
        $speakers[] = $speaker5;

        return $speakers;
    }
} 