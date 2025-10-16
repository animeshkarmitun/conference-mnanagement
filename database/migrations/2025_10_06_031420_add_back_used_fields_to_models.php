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
        // Add back only the fields that are still being used
        Schema::table('users', function (Blueprint $table) {
            // These fields are still being used in forms
            if (!Schema::hasColumn('users', 'current_designation')) {
                $table->string('current_designation')->nullable();
            }
            if (!Schema::hasColumn('users', 'resume')) {
                $table->string('resume')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the fields again
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'current_designation')) {
                $table->dropColumn('current_designation');
            }
            if (Schema::hasColumn('users', 'resume')) {
                $table->dropColumn('resume');
            }
        });
    }
};