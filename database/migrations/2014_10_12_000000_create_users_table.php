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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('organization')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('resume')->nullable();
            $table->string('dietary_needs')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
        
        // Add unique index with length limit to avoid MySQL key length issues
        \DB::statement('ALTER TABLE users ADD UNIQUE users_email_unique (email(191))');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
