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
            // Remove unused columns from participants table
            $table->dropColumn([
                'serial_number',
                'approved',
                'dietary_needs',
                'dietary_needs_other',
                'hashtags'
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            // Remove unused columns from users table
            $table->dropColumn([
                'nationality',
                'profession',
                'current_designation',
                'resume',
                'dietary_needs'
            ]);
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