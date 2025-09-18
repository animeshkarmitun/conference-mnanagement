<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('emails', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('emails', 'direction')) {
                $table->enum('direction', ['outgoing', 'incoming'])->default('outgoing')->after('status');
            }
            if (!Schema::hasColumn('emails', 'thread_id')) {
                $table->string('thread_id', 36)->nullable()->after('direction');
            }
            if (!Schema::hasColumn('emails', 'parent_email_id')) {
                $table->unsignedBigInteger('parent_email_id')->nullable()->after('thread_id');
            }
            if (!Schema::hasColumn('emails', 'in_reply_to')) {
                $table->string('in_reply_to', 255)->nullable()->after('parent_email_id');
            }
            if (!Schema::hasColumn('emails', 'sender_email')) {
                $table->string('sender_email', 255)->nullable()->after('in_reply_to');
            }
            if (!Schema::hasColumn('emails', 'sender_name')) {
                $table->string('sender_name', 255)->nullable()->after('sender_email');
            }
            if (!Schema::hasColumn('emails', 'received_at')) {
                $table->timestamp('received_at')->nullable()->after('sender_name');
            }
        });
        
        // Add basic indexes first
        Schema::table('emails', function (Blueprint $table) {
            if (!Schema::hasIndex('emails', 'emails_thread_id_index')) {
                $table->index('thread_id');
            }
            if (!Schema::hasIndex('emails', 'emails_direction_index')) {
                $table->index('direction');
            }
            if (!Schema::hasIndex('emails', 'emails_parent_email_id_index')) {
                $table->index('parent_email_id');
            }
        });
        
        // Add sender_email index separately after columns are created
        if (!Schema::hasIndex('emails', 'sender_email_index')) {
            DB::statement('ALTER TABLE emails ADD INDEX sender_email_index (sender_email(191))');
        }
        
        // Add foreign key constraint
        Schema::table('emails', function (Blueprint $table) {
            if (!Schema::hasColumn('emails', 'parent_email_id')) {
                $table->foreign('parent_email_id')->references('id')->on('emails')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('emails', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['parent_email_id']);
            
            // Drop indexes
            $table->dropIndex(['thread_id']);
            $table->dropIndex(['direction']);
            DB::statement('ALTER TABLE emails DROP INDEX sender_email_index');
            $table->dropIndex(['parent_email_id']);
            
            // Drop columns
            $table->dropColumn([
                'direction',
                'thread_id',
                'parent_email_id',
                'in_reply_to',
                'sender_email',
                'sender_name',
                'received_at'
            ]);
        });
    }
};
