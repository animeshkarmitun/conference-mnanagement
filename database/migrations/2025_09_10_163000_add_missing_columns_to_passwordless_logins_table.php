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
        Schema::table('passwordless_logins', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('passwordless_logins', 'user_id')) {
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('passwordless_logins', 'token')) {
                $table->string('token', 255)->unique();
            }
            if (!Schema::hasColumn('passwordless_logins', 'expires_at')) {
                $table->timestamp('expires_at');
            }
            if (!Schema::hasColumn('passwordless_logins', 'used_at')) {
                $table->timestamp('used_at')->nullable();
            }
            if (!Schema::hasColumn('passwordless_logins', 'ip_address')) {
                $table->string('ip_address', 45)->nullable();
            }
            if (!Schema::hasColumn('passwordless_logins', 'user_agent')) {
                $table->text('user_agent')->nullable();
            }
            
            // Add indexes if they don't exist
            if (!Schema::hasIndex('passwordless_logins', 'passwordless_logins_token_index')) {
                $table->index('token');
            }
            if (!Schema::hasIndex('passwordless_logins', 'passwordless_logins_expires_at_index')) {
                $table->index('expires_at');
            }
            if (!Schema::hasIndex('passwordless_logins', 'passwordless_logins_user_id_expires_at_index')) {
                $table->index(['user_id', 'expires_at']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('passwordless_logins', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'token', 'expires_at', 'used_at', 'ip_address', 'user_agent']);
        });
    }
};
