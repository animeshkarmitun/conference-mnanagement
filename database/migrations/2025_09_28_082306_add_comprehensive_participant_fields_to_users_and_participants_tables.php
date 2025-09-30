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
        // Add only missing fields to users table
        Schema::table('users', function (Blueprint $table) {
            // Add date_of_birth if it doesn't exist
            if (!Schema::hasColumn('users', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('whatsapp_no');
            }
        });

        // Add only missing fields to participants table
        Schema::table('participants', function (Blueprint $table) {
            // Add only fields that don't exist
            if (!Schema::hasColumn('participants', 'dietary_needs_other')) {
                $table->text('dietary_needs_other')->nullable()->after('dietary_needs');
            }
            if (!Schema::hasColumn('participants', 'hashtags')) {
                $table->string('hashtags')->nullable()->after('dietary_needs');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove fields from users table
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'date_of_birth')) {
                $table->dropColumn('date_of_birth');
            }
        });

        // Remove fields from participants table
        Schema::table('participants', function (Blueprint $table) {
            if (Schema::hasColumn('participants', 'dietary_needs_other')) {
                $table->dropColumn('dietary_needs_other');
            }
            if (Schema::hasColumn('participants', 'hashtags')) {
                $table->dropColumn('hashtags');
            }
        });
    }
};
