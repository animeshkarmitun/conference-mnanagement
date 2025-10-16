<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\EmailSettings;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create the session notification email template
        EmailSettings::create([
            'email_type' => EmailSettings::TYPE_SESSION_NOTIFICATION,
            'subject_template' => 'Session Update - {conference_name}',
            'greeting_template' => 'Dear {first_name},',
            'body_template' => '
                <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
                    <h2 style="color: #1f2937; margin-bottom: 20px;">{email_title}</h2>
                    
                    <p>A {session_action} has been added to <strong>{conference_name}</strong>:</p>
                    
                    <div style="background-color: #f0fdf4; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #22c55e;">
                        <h3 style="color: #374151; margin-top: 0;">Session Details:</h3>
                        <p><strong>Title:</strong> {session_title}</p>
                        <p><strong>Date & Time:</strong> {session_date}</p>
                        <p><strong>Location:</strong> {session_location}</p>
                        <p><strong>Description:</strong> {session_description}</p>
                    </div>
                    
                    <p>Please check the session details and register if you\'re interested in participating.</p>
                    
                    <p style="color: #6b7280; font-size: 14px; margin-top: 30px;">
                        Thank you for your participation in {conference_name}. We look forward to seeing you at the conference!
                    </p>
                    
                    <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;">
                    <p style="color: #9ca3af; font-size: 12px; text-align: center;">
                        This is an automated notification from the Conference Management System.
                    </p>
                </div>
            ',
            'closing_template' => 'Best regards,',
            'system_signature' => 'Conference Team',
            'is_active' => true,
            'created_by' => 1, // Assuming admin user ID is 1
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        EmailSettings::where('email_type', EmailSettings::TYPE_SESSION_NOTIFICATION)->delete();
    }
};