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
            // Core fields
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->string('recipient_email', 191)->nullable()->after('user_id');
            $table->string('recipient_name', 191)->nullable()->after('recipient_email');
            $table->string('subject', 255)->nullable()->after('recipient_name');
            $table->longText('body')->nullable()->after('subject');
            $table->string('status', 32)->default('pending')->after('body');
            $table->string('email_type', 64)->nullable()->after('status');

            // Polymorphic relation
            $table->string('related_model_type', 191)->nullable()->after('email_type');
            $table->unsignedBigInteger('related_model_id')->nullable()->after('related_model_type');

            // Tracking timestamps
            $table->timestamp('sent_at')->nullable()->after('related_model_id');
            $table->timestamp('delivered_at')->nullable()->after('sent_at');
            $table->timestamp('opened_at')->nullable()->after('delivered_at');
            $table->timestamp('clicked_at')->nullable()->after('opened_at');
            $table->timestamp('bounced_at')->nullable()->after('clicked_at');

            // Error and message identifiers
            $table->text('error_message')->nullable()->after('bounced_at');
            $table->string('message_id', 191)->nullable()->after('error_message');
            $table->string('template_name', 128)->nullable()->after('message_id');

            // Conference association and metadata
            $table->unsignedBigInteger('conference_id')->nullable()->after('template_name');
            $table->json('metadata')->nullable()->after('conference_id');

            // Conversation fields
            $table->string('direction', 16)->nullable()->after('metadata'); // outgoing | incoming
            $table->string('thread_id', 191)->nullable()->after('direction');
            $table->unsignedBigInteger('parent_email_id')->nullable()->after('thread_id');
            $table->string('in_reply_to', 191)->nullable()->after('parent_email_id');
            $table->string('sender_email', 191)->nullable()->after('in_reply_to');
            $table->string('sender_name', 191)->nullable()->after('sender_email');
            $table->timestamp('received_at')->nullable()->after('sender_name');

            // Indexes for common queries
            $table->index('sent_at');
            $table->index('status');
            $table->index('email_type');
            $table->index('conference_id');
            $table->index('thread_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('emails', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex(['sent_at']);
            $table->dropIndex(['status']);
            $table->dropIndex(['email_type']);
            $table->dropIndex(['conference_id']);
            $table->dropIndex(['thread_id']);

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
                'direction',
                'thread_id',
                'parent_email_id',
                'in_reply_to',
                'sender_email',
                'sender_name',
                'received_at',
            ]);
        });
    }
};


