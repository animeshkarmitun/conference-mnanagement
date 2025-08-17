<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ParticipantType;

class ParticipantTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            // Presenters & Speakers
            [
                'name' => 'keynote_speaker',
                'description' => 'Distinguished speakers delivering keynote addresses and plenary sessions',
                'category' => 'presenter',
                'requires_approval' => true,
                'has_special_privileges' => true,
                'display_order' => 1,
            ],
            [
                'name' => 'speaker',
                'description' => 'Individuals presenting sessions, papers, or workshops at the conference',
                'category' => 'presenter',
                'requires_approval' => true,
                'has_special_privileges' => true,
                'display_order' => 2,
            ],
            [
                'name' => 'presenter',
                'description' => 'General presenters delivering content in various session formats',
                'category' => 'presenter',
                'requires_approval' => true,
                'has_special_privileges' => true,
                'display_order' => 3,
            ],
            [
                'name' => 'panelist',
                'description' => 'Participants in panel discussions and roundtable sessions',
                'category' => 'presenter',
                'requires_approval' => true,
                'has_special_privileges' => true,
                'display_order' => 4,
            ],
            [
                'name' => 'moderator',
                'description' => 'Individuals facilitating sessions, panels, or discussions',
                'category' => 'presenter',
                'requires_approval' => true,
                'has_special_privileges' => true,
                'display_order' => 5,
            ],
            [
                'name' => 'facilitator',
                'description' => 'Session facilitators and discussion leaders',
                'category' => 'presenter',
                'requires_approval' => true,
                'has_special_privileges' => true,
                'display_order' => 6,
            ],
            [
                'name' => 'session_chair',
                'description' => 'Chairs responsible for managing and coordinating specific sessions',
                'category' => 'presenter',
                'requires_approval' => true,
                'has_special_privileges' => true,
                'display_order' => 7,
            ],
            [
                'name' => 'workshop_leader',
                'description' => 'Leaders conducting hands-on workshops and training sessions',
                'category' => 'presenter',
                'requires_approval' => true,
                'has_special_privileges' => true,
                'display_order' => 8,
            ],
            [
                'name' => 'trainer',
                'description' => 'Professional trainers conducting educational sessions',
                'category' => 'presenter',
                'requires_approval' => true,
                'has_special_privileges' => true,
                'display_order' => 9,
            ],
            [
                'name' => 'mentor',
                'description' => 'Mentors providing guidance and support to participants',
                'category' => 'presenter',
                'requires_approval' => true,
                'has_special_privileges' => true,
                'display_order' => 10,
            ],

            // Attendees & Participants
            [
                'name' => 'attendee',
                'description' => 'General conference attendees who register to participate in sessions and activities',
                'category' => 'attendee',
                'requires_approval' => false,
                'has_special_privileges' => false,
                'display_order' => 11,
            ],
            [
                'name' => 'participant',
                'description' => 'General participants in conference activities and sessions',
                'category' => 'attendee',
                'requires_approval' => false,
                'has_special_privileges' => false,
                'display_order' => 12,
            ],
            [
                'name' => 'delegate',
                'description' => 'Official representatives from organizations or institutions',
                'category' => 'attendee',
                'requires_approval' => true,
                'has_special_privileges' => true,
                'display_order' => 13,
            ],
            [
                'name' => 'student',
                'description' => 'Students attending the conference (may have special pricing)',
                'category' => 'attendee',
                'requires_approval' => false,
                'has_special_privileges' => false,
                'display_order' => 14,
            ],
            [
                'name' => 'researcher',
                'description' => 'Academic and industry researchers presenting or attending',
                'category' => 'attendee',
                'requires_approval' => false,
                'has_special_privileges' => false,
                'display_order' => 15,
            ],
            [
                'name' => 'industry_professional',
                'description' => 'Professionals from various industries attending the conference',
                'category' => 'attendee',
                'requires_approval' => false,
                'has_special_privileges' => false,
                'display_order' => 16,
            ],

            // Commercial & Exhibition
            [
                'name' => 'exhibitor',
                'description' => 'Companies or organizations showcasing products/services at exhibition booths',
                'category' => 'commercial',
                'requires_approval' => true,
                'has_special_privileges' => false,
                'display_order' => 17,
            ],
            [
                'name' => 'vendor',
                'description' => 'Vendors and suppliers participating in the conference',
                'category' => 'commercial',
                'requires_approval' => true,
                'has_special_privileges' => false,
                'display_order' => 18,
            ],
            [
                'name' => 'demonstrator',
                'description' => 'Individuals demonstrating products or services',
                'category' => 'commercial',
                'requires_approval' => true,
                'has_special_privileges' => false,
                'display_order' => 19,
            ],
            [
                'name' => 'booth_staff',
                'description' => 'Staff working at exhibition booths and vendor areas',
                'category' => 'commercial',
                'requires_approval' => true,
                'has_special_privileges' => false,
                'display_order' => 20,
            ],

            // Staff & Organization
            [
                'name' => 'organizer',
                'description' => 'Conference organizers, committee members, and staff',
                'category' => 'staff',
                'requires_approval' => true,
                'has_special_privileges' => true,
                'display_order' => 21,
            ],
            [
                'name' => 'conference_staff',
                'description' => 'General conference staff and support personnel',
                'category' => 'staff',
                'requires_approval' => true,
                'has_special_privileges' => false,
                'display_order' => 22,
            ],
            [
                'name' => 'volunteer',
                'description' => 'Conference volunteers and helpers',
                'category' => 'staff',
                'requires_approval' => true,
                'has_special_privileges' => false,
                'display_order' => 23,
            ],
            [
                'name' => 'technical_support',
                'description' => 'Technical support staff and IT personnel',
                'category' => 'staff',
                'requires_approval' => true,
                'has_special_privileges' => false,
                'display_order' => 24,
            ],
            [
                'name' => 'conference_committee_member',
                'description' => 'Members of the conference organizing committee',
                'category' => 'staff',
                'requires_approval' => true,
                'has_special_privileges' => true,
                'display_order' => 25,
            ],
            [
                'name' => 'program_coordinator',
                'description' => 'Coordinators managing conference programs and schedules',
                'category' => 'staff',
                'requires_approval' => true,
                'has_special_privileges' => true,
                'display_order' => 26,
            ],

            // Sponsors & Partners
            [
                'name' => 'sponsor',
                'description' => 'Financial sponsors and their representatives',
                'category' => 'sponsor',
                'requires_approval' => true,
                'has_special_privileges' => true,
                'display_order' => 27,
            ],
            [
                'name' => 'partner',
                'description' => 'Conference partners and collaborators',
                'category' => 'sponsor',
                'requires_approval' => true,
                'has_special_privileges' => true,
                'display_order' => 28,
            ],
            [
                'name' => 'donor',
                'description' => 'Donors and financial contributors to the conference',
                'category' => 'sponsor',
                'requires_approval' => true,
                'has_special_privileges' => true,
                'display_order' => 29,
            ],

            // Media & Press
            [
                'name' => 'media',
                'description' => 'General media representatives and press',
                'category' => 'press',
                'requires_approval' => true,
                'has_special_privileges' => false,
                'display_order' => 30,
            ],
            [
                'name' => 'press',
                'description' => 'Press representatives and journalists',
                'category' => 'press',
                'requires_approval' => true,
                'has_special_privileges' => false,
                'display_order' => 31,
            ],
            [
                'name' => 'journalist',
                'description' => 'Journalists and reporters covering the conference',
                'category' => 'press',
                'requires_approval' => true,
                'has_special_privileges' => false,
                'display_order' => 32,
            ],
            [
                'name' => 'photographer',
                'description' => 'Photographers and visual media professionals',
                'category' => 'press',
                'requires_approval' => true,
                'has_special_privileges' => false,
                'display_order' => 33,
            ],
            [
                'name' => 'broadcaster',
                'description' => 'Broadcasting professionals and media outlets',
                'category' => 'press',
                'requires_approval' => true,
                'has_special_privileges' => false,
                'display_order' => 34,
            ],
            [
                'name' => 'blogger',
                'description' => 'Bloggers and digital content creators',
                'category' => 'press',
                'requires_approval' => true,
                'has_special_privileges' => false,
                'display_order' => 35,
            ],
            [
                'name' => 'influencer',
                'description' => 'Social media influencers and content creators',
                'category' => 'press',
                'requires_approval' => true,
                'has_special_privileges' => false,
                'display_order' => 36,
            ],

            // Special & VIP
            [
                'name' => 'special_guest',
                'description' => 'Special guests and distinguished visitors',
                'category' => 'special',
                'requires_approval' => true,
                'has_special_privileges' => true,
                'display_order' => 37,
            ],
            [
                'name' => 'vip',
                'description' => 'Very Important Persons, dignitaries, and special guests',
                'category' => 'special',
                'requires_approval' => true,
                'has_special_privileges' => true,
                'display_order' => 38,
            ],
            [
                'name' => 'policymaker',
                'description' => 'Government policymakers and decision-makers',
                'category' => 'special',
                'requires_approval' => true,
                'has_special_privileges' => true,
                'display_order' => 39,
            ],
            [
                'name' => 'government_representative',
                'description' => 'Representatives from government agencies and departments',
                'category' => 'special',
                'requires_approval' => true,
                'has_special_privileges' => true,
                'display_order' => 40,
            ],
            [
                'name' => 'association_representative',
                'description' => 'Representatives from professional associations and organizations',
                'category' => 'special',
                'requires_approval' => true,
                'has_special_privileges' => true,
                'display_order' => 41,
            ],
            [
                'name' => 'award_recipient',
                'description' => 'Recipients of awards and honors at the conference',
                'category' => 'special',
                'requires_approval' => true,
                'has_special_privileges' => true,
                'display_order' => 42,
            ],

            // Support & Services
            [
                'name' => 'session_recorder',
                'description' => 'Individuals recording and documenting sessions',
                'category' => 'support',
                'requires_approval' => true,
                'has_special_privileges' => false,
                'display_order' => 43,
            ],
            [
                'name' => 'interpreter',
                'description' => 'Language interpreters and translators',
                'category' => 'support',
                'requires_approval' => true,
                'has_special_privileges' => false,
                'display_order' => 44,
            ],
            [
                'name' => 'sign_language_interpreter',
                'description' => 'Sign language interpreters for accessibility',
                'category' => 'support',
                'requires_approval' => true,
                'has_special_privileges' => false,
                'display_order' => 45,
            ],
            [
                'name' => 'networking_host',
                'description' => 'Hosts facilitating networking events and activities',
                'category' => 'support',
                'requires_approval' => true,
                'has_special_privileges' => false,
                'display_order' => 46,
            ],
            [
                'name' => 'evaluator',
                'description' => 'Evaluators and assessors for conference content',
                'category' => 'support',
                'requires_approval' => true,
                'has_special_privileges' => false,
                'display_order' => 47,
            ],
            [
                'name' => 'judge',
                'description' => 'Judges for competitions, awards, or evaluations',
                'category' => 'support',
                'requires_approval' => true,
                'has_special_privileges' => true,
                'display_order' => 48,
            ],
        ];
        
        foreach ($types as $type) {
            ParticipantType::firstOrCreate(
                ['name' => $type['name']],
                $type
            );
        }
    }
}
