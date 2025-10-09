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
        Schema::table('notifications', function (Blueprint $table) {
            // Add participant_id column
            $table->foreignId('participant_id')->nullable()->after('user_id')->constrained()->onDelete('cascade');
            
            // Add index for better performance
            $table->index(['participant_id', 'read_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['participant_id', 'read_status']);
            $table->dropForeign(['participant_id']);
            $table->dropColumn('participant_id');
        });
    }
};