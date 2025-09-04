<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IdCard;
use App\Models\Conference;

class IdCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first conference for participant cards
        $conference = Conference::first();

        // Create default participant ID card template
        IdCard::firstOrCreate(
            ['name' => 'Default Participant Card'],
            [
                'type' => 'participant',
                'conference_id' => $conference ? $conference->id : null,
                'template_config' => [
                    'layout' => 'standard',
                    'fields' => [
                        'name' => true,
                        'role' => true,
                        'organization' => true,
                        'photo' => true,
                        'qr_code' => true,
                        'conference_info' => true,
                        'session_info' => false,
                    ],
                    'dimensions' => [
                        'width' => '3.375',
                        'height' => '2.125',
                    ],
                ],
                'background_color' => '#ffffff',
                'text_color' => '#000000',
                'accent_color' => '#007bff',
                'include_qr_code' => true,
                'include_photo' => true,
                'is_active' => true,
            ]
        );

        // Create default company worker ID card template
        IdCard::firstOrCreate(
            ['name' => 'Default Company Worker Card'],
            [
                'type' => 'company_worker',
                'conference_id' => null,
                'template_config' => [
                    'layout' => 'standard',
                    'fields' => [
                        'name' => true,
                        'role' => true,
                        'organization' => true,
                        'photo' => true,
                        'qr_code' => true,
                        'conference_info' => false,
                        'session_info' => false,
                    ],
                    'dimensions' => [
                        'width' => '3.375',
                        'height' => '2.125',
                    ],
                ],
                'background_color' => '#ffffff',
                'text_color' => '#000000',
                'accent_color' => '#28a745',
                'include_qr_code' => true,
                'include_photo' => true,
                'is_active' => true,
            ]
        );

        // Create premium participant card template
        IdCard::firstOrCreate(
            ['name' => 'Premium Participant Card'],
            [
                'type' => 'participant',
                'conference_id' => $conference ? $conference->id : null,
                'template_config' => [
                    'layout' => 'premium',
                    'fields' => [
                        'name' => true,
                        'role' => true,
                        'organization' => true,
                        'photo' => true,
                        'qr_code' => true,
                        'conference_info' => true,
                        'session_info' => true,
                    ],
                    'dimensions' => [
                        'width' => '3.375',
                        'height' => '2.125',
                    ],
                ],
                'background_color' => '#f8f9fa',
                'text_color' => '#212529',
                'accent_color' => '#6f42c1',
                'include_qr_code' => true,
                'include_photo' => true,
                'is_active' => true,
            ]
        );

        // Create VIP participant card template
        IdCard::firstOrCreate(
            ['name' => 'VIP Participant Card'],
            [
                'type' => 'participant',
                'conference_id' => $conference ? $conference->id : null,
                'template_config' => [
                    'layout' => 'vip',
                    'fields' => [
                        'name' => true,
                        'role' => true,
                        'organization' => true,
                        'photo' => true,
                        'qr_code' => true,
                        'conference_info' => true,
                        'session_info' => true,
                    ],
                    'dimensions' => [
                        'width' => '3.375',
                        'height' => '2.125',
                    ],
                ],
                'background_color' => '#fff3cd',
                'text_color' => '#856404',
                'accent_color' => '#ffc107',
                'include_qr_code' => true,
                'include_photo' => true,
                'is_active' => true,
            ]
        );

        // Create executive company worker card template
        IdCard::firstOrCreate(
            ['name' => 'Executive Company Worker Card'],
            [
                'type' => 'company_worker',
                'conference_id' => null,
                'template_config' => [
                    'layout' => 'executive',
                    'fields' => [
                        'name' => true,
                        'role' => true,
                        'organization' => true,
                        'photo' => true,
                        'qr_code' => true,
                        'conference_info' => false,
                        'session_info' => false,
                    ],
                    'dimensions' => [
                        'width' => '3.375',
                        'height' => '2.125',
                    ],
                ],
                'background_color' => '#e9ecef',
                'text_color' => '#495057',
                'accent_color' => '#dc3545',
                'include_qr_code' => true,
                'include_photo' => true,
                'is_active' => true,
            ]
        );
    }
}
