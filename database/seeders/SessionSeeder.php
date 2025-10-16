<?php

namespace Database\Seeders;

use App\Models\Session;
use Illuminate\Database\Seeder;

class SessionSeeder extends Seeder
{
    public function run(): void
    {
        // Sessions for ongoing conferences (Digital Marketing Summit 2025)
        Session::firstOrCreate(
            ['title' => 'Opening Keynote: The Future of Digital Marketing', 'conference_id' => 1],
            [
                'description' => 'Opening keynote by industry leaders on emerging trends in digital marketing.',
                'start_time' => now()->subDays(1)->setTime(9, 0),
                'end_time' => now()->subDays(1)->setTime(10, 30),
                'venue_id' => 1,
                'seating_arrangement' => 'Theater style - 500 seats',
                'status' => 'published'
            ]
        );
        
        Session::firstOrCreate(
            ['title' => 'Social Media Strategy Panel', 'conference_id' => 1],
            [
                'description' => 'Interactive panel discussion on social media marketing strategies.',
                'start_time' => now()->subDays(1)->setTime(11, 0),
                'end_time' => now()->subDays(1)->setTime(12, 30),
                'venue_id' => 1,
                'seating_arrangement' => 'Round table - 200 seats',
                'status' => 'published'
            ]
        );

        Session::firstOrCreate(
            ['title' => 'AI in Marketing Workshop', 'conference_id' => 1],
            [
                'description' => 'Hands-on workshop exploring AI tools for marketing automation.',
                'start_time' => now()->setTime(10, 0),
                'end_time' => now()->setTime(12, 0),
                'venue_id' => 1,
                'seating_arrangement' => 'Classroom style - 100 seats',
                'status' => 'published'
            ]
        );

        Session::firstOrCreate(
            ['title' => 'Content Marketing Masterclass', 'conference_id' => 1],
            [
                'description' => 'Advanced content marketing strategies and best practices.',
                'start_time' => now()->setTime(14, 0),
                'end_time' => now()->setTime(15, 30),
                'venue_id' => 1,
                'seating_arrangement' => 'Theater style - 300 seats',
                'status' => 'published'
            ]
        );

        // Sessions for Healthcare Innovation Conference
        Session::firstOrCreate(
            ['title' => 'Telemedicine Revolution', 'conference_id' => 2],
            [
                'description' => 'Exploring the latest advances in telemedicine and remote healthcare.',
                'start_time' => now()->setTime(9, 0),
                'end_time' => now()->setTime(10, 30),
                'venue_id' => 2,
                'seating_arrangement' => 'Theater style - 250 seats',
                'status' => 'published'
            ]
        );
        
        Session::firstOrCreate(
            ['title' => 'Medical AI Applications', 'conference_id' => 2],
            [
                'description' => 'Workshop on artificial intelligence applications in medical diagnosis.',
                'start_time' => now()->setTime(11, 0),
                'end_time' => now()->setTime(13, 0),
                'venue_id' => 2,
                'seating_arrangement' => 'Lab style - 50 seats',
                'status' => 'published'
            ]
        );

        Session::firstOrCreate(
            ['title' => 'Healthcare Data Privacy Panel', 'conference_id' => 2],
            [
                'description' => 'Panel discussion on patient data privacy and regulatory compliance.',
                'start_time' => now()->setTime(14, 30),
                'end_time' => now()->setTime(16, 0),
                'venue_id' => 2,
                'seating_arrangement' => 'Round table - 150 seats',
                'status' => 'published'
            ]
        );

        // Sessions for upcoming AI & Machine Learning Expo
        Session::firstOrCreate(
            ['title' => 'Machine Learning Fundamentals', 'conference_id' => 3],
            [
                'description' => 'Introduction to core machine learning concepts and applications.',
                'start_time' => now()->addDays(2)->setTime(9, 0),
                'end_time' => now()->addDays(2)->setTime(10, 30),
                'venue_id' => 1,
                'seating_arrangement' => 'Theater style - 400 seats',
                'status' => 'draft'
            ]
        );

        Session::firstOrCreate(
            ['title' => 'Deep Learning Workshop', 'conference_id' => 3],
            [
                'description' => 'Hands-on workshop on neural networks and deep learning frameworks.',
                'start_time' => now()->addDays(2)->setTime(11, 0),
                'end_time' => now()->addDays(2)->setTime(13, 0),
                'venue_id' => 1,
                'seating_arrangement' => 'Computer lab - 80 seats',
                'status' => 'draft'
            ]
        );

        Session::firstOrCreate(
            ['title' => 'AI Ethics and Governance', 'conference_id' => 3],
            [
                'description' => 'Discussion on ethical considerations in AI development and deployment.',
                'start_time' => now()->addDays(2)->setTime(14, 0),
                'end_time' => now()->addDays(2)->setTime(15, 30),
                'venue_id' => 1,
                'seating_arrangement' => 'Round table - 200 seats',
                'status' => 'draft'
            ]
        );

        // Sessions for Sustainable Business Conference
        Session::firstOrCreate(
            ['title' => 'Green Business Strategies', 'conference_id' => 4],
            [
                'description' => 'Workshop on implementing sustainable practices in business operations.',
                'start_time' => now()->addDays(2)->setTime(10, 0),
                'end_time' => now()->addDays(2)->setTime(12, 0),
                'venue_id' => 2,
                'seating_arrangement' => 'Classroom style - 150 seats',
                'status' => 'draft'
            ]
        );

        Session::firstOrCreate(
            ['title' => 'Carbon Footprint Reduction Panel', 'conference_id' => 4],
            [
                'description' => 'Panel discussion on reducing carbon footprint in corporate operations.',
                'start_time' => now()->addDays(2)->setTime(13, 30),
                'end_time' => now()->addDays(2)->setTime(15, 0),
                'venue_id' => 2,
                'seating_arrangement' => 'Round table - 200 seats',
                'status' => 'draft'
            ]
        );

        // Sessions for future conferences
        Session::firstOrCreate(
            ['title' => 'FinTech Innovation Keynote', 'conference_id' => 5],
            [
                'description' => 'Keynote on the future of financial technology and digital banking.',
                'start_time' => now()->addDays(7)->setTime(9, 0),
                'end_time' => now()->addDays(7)->setTime(10, 30),
                'venue_id' => 3,
                'seating_arrangement' => 'Theater style - 600 seats',
                'status' => 'draft'
            ]
        );

        Session::firstOrCreate(
            ['title' => 'Climate Action Strategies', 'conference_id' => 6],
            [
                'description' => 'Workshop on developing actionable climate change mitigation strategies.',
                'start_time' => now()->addDays(14)->setTime(10, 0),
                'end_time' => now()->addDays(14)->setTime(12, 0),
                'venue_id' => 1,
                'seating_arrangement' => 'Classroom style - 100 seats',
                'status' => 'draft'
            ]
        );
    }
}