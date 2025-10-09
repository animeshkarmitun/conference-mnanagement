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
            // Add new fields for multi-profile support
            $table->string('profile_name')->nullable()->after('user_id');
            $table->enum('profile_type', ['personal', 'professional', 'academic', 'media', 'speaker'])->default('personal')->after('profile_name');
            $table->enum('status', ['active', 'inactive', 'archived'])->default('active')->after('profile_type');
            $table->text('profile_description')->nullable()->after('status');
            $table->boolean('is_primary')->default(false)->after('profile_description');
            
            // Add indexes for better performance
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'conference_id']);
            $table->index(['user_id', 'is_primary']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['user_id', 'conference_id']);
            $table->dropIndex(['user_id', 'is_primary']);
            
            $table->dropColumn([
                'profile_name',
                'profile_type', 
                'status',
                'profile_description',
                'is_primary'
            ]);
        });
    }
};
