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
        Schema::table('conference_kits', function (Blueprint $table) {
            // Add participant_id column if it doesn't exist
            if (!Schema::hasColumn('conference_kits', 'participant_id')) {
                $table->foreignId('participant_id')->nullable()->constrained()->onDelete('cascade');
            }
            
            // Add index for better performance if it doesn't exist
            if (!Schema::hasIndex('conference_kits', 'conference_kits_participant_id_conference_id_index')) {
                $table->index(['participant_id', 'conference_id']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conference_kits', function (Blueprint $table) {
            $table->dropIndex(['participant_id', 'conference_id']);
            $table->dropForeign(['participant_id']);
            $table->dropColumn('participant_id');
        });
    }
};
