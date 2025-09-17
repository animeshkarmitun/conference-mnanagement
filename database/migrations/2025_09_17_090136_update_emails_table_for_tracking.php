<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('emails', function (Blueprint $table) {
            // Add all required columns for email tracking
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('set null');
            $table->string('recipient_email')->after('user_id');
            $table->string('recipient_name')->nullable()->after('recipient_email');
            $table->string('subject')->after('recipient_name');
            $table->text('body')->after('subject');
            $table->string('status')->default('pending')->after('body');
            $table->string('email_type')->default('general')->after('status');
            $table->string('related_model_type')->nullable()->after('email_type');
            $table->unsignedBigInteger('related_model_id')->nullable()->after('related_model_type');
            $table->timestamp('sent_at')->nullable()->after('related_model_id');
            $table->timestamp('delivered_at')->nullable()->after('sent_at');
            $table->timestamp('opened_at')->nullable()->after('delivered_at');
            $table->timestamp('clicked_at')->nullable()->after('opened_at');
            $table->timestamp('bounced_at')->nullable()->after('clicked_at');
            $table->text('error_message')->nullable()->after('bounced_at');
            $table->string('message_id')->nullable()->after('error_message');
            $table->string('template_name')->nullable()->after('message_id');
            $table->foreignId('conference_id')->nullable()->after('template_name')->constrained()->onDelete('set null');
            $table->json('metadata')->nullable()->after('conference_id');
            
            // Add indexes for better performance (avoiding key length issues)
            $table->index('email_type');
            $table->index('status');
            $table->index('conference_id');
            $table->index('sent_at');
            $table->index('recipient_email');
            $table->index('message_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('emails', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex(['email_type']);
            $table->dropIndex(['status']);
            $table->dropIndex(['conference_id']);
            $table->dropIndex(['sent_at']);
            $table->dropIndex(['recipient_email']);
            $table->dropIndex(['message_id']);
            
            // Drop foreign key constraints
            $table->dropForeign(['conference_id']);
            $table->dropForeign(['user_id']);
            
            // Drop columns
            $table->dropColumn([
                'user_id',
                'recipient_email',
                'recipient_name',
                'subject',
                'body',
                'status',
                'email_type',
                'related_model_type',
                'related_model_id',
                'sent_at',
                'delivered_at',
                'opened_at',
                'clicked_at',
                'bounced_at',
                'error_message',
                'message_id',
                'template_name',
                'conference_id',
                'metadata',
            ]);
        });
    }
};