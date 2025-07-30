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
        Schema::table('participants', function (Blueprint $table) {
            // First, drop the existing enum column
            $table->dropColumn('visa_status');
        });

        Schema::table('participants', function (Blueprint $table) {
            // Recreate the enum column with new values
            $table->enum('visa_status', ['required', 'not_required', 'pending', 'approved', 'issue'])->default('pending')->after('participant_type_id');
            
            // Add visa issue description field
            $table->text('visa_issue_description')->nullable()->after('visa_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn('visa_issue_description');
            
            // Revert visa_status to original values
            $table->dropColumn('visa_status');
            $table->enum('visa_status', ['required', 'not_required', 'pending'])->default('pending')->after('participant_type_id');
        });
    }
};
