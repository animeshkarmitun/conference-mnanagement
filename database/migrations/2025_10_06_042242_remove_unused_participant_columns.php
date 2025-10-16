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
            // Remove unused columns from participants table if they exist
            $columnsToDrop = [];
            if (Schema::hasColumn('participants', 'serial_number')) $columnsToDrop[] = 'serial_number';
            if (Schema::hasColumn('participants', 'approved')) $columnsToDrop[] = 'approved';
            if (Schema::hasColumn('participants', 'dietary_needs')) $columnsToDrop[] = 'dietary_needs';
            if (Schema::hasColumn('participants', 'dietary_needs_other')) $columnsToDrop[] = 'dietary_needs_other';
            if (Schema::hasColumn('participants', 'hashtags')) $columnsToDrop[] = 'hashtags';
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });

        Schema::table('users', function (Blueprint $table) {
            // Remove unused columns from users table if they exist
            $columnsToDrop = [];
            if (Schema::hasColumn('users', 'nationality')) $columnsToDrop[] = 'nationality';
            if (Schema::hasColumn('users', 'profession')) $columnsToDrop[] = 'profession';
            if (Schema::hasColumn('users', 'current_designation')) $columnsToDrop[] = 'current_designation';
            if (Schema::hasColumn('users', 'resume')) $columnsToDrop[] = 'resume';
            if (Schema::hasColumn('users', 'dietary_needs')) $columnsToDrop[] = 'dietary_needs';
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            // Restore participants table columns
            $table->string('serial_number')->nullable();
            $table->boolean('approved')->default(false);
            $table->string('dietary_needs')->nullable();
            $table->string('dietary_needs_other')->nullable();
            $table->text('hashtags')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            // Restore users table columns
            $table->string('nationality')->nullable();
            $table->string('profession')->nullable();
            $table->string('current_designation')->nullable();
            $table->string('resume')->nullable();
            $table->string('dietary_needs')->nullable();
        });
    }
};