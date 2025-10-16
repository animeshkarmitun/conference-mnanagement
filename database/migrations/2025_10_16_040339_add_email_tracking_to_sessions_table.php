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
        Schema::table('sessions', function (Blueprint $table) {
            $table->integer('email_send_count')->default(0)->after('status');
            $table->timestamp('last_email_sent_at')->nullable()->after('email_send_count');
            $table->json('email_recipients')->nullable()->after('last_email_sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropColumn(['email_send_count', 'last_email_sent_at', 'email_recipients']);
        });
    }
};
