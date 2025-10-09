<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix passwordless_login related_model_type
        DB::table('emails')
            ->where('related_model_type', 'passwordless_login')
            ->update(['related_model_type' => 'App\\Models\\PasswordlessLogin']);

        // Fix participant related_model_type
        DB::table('emails')
            ->where('related_model_type', 'participant')
            ->update(['related_model_type' => 'App\\Models\\Participant']);

        // Fix task related_model_type
        DB::table('emails')
            ->where('related_model_type', 'task')
            ->update(['related_model_type' => 'App\\Models\\Task']);

        // Fix session related_model_type
        DB::table('emails')
            ->where('related_model_type', 'session')
            ->update(['related_model_type' => 'App\\Models\\Session']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert passwordless_login related_model_type
        DB::table('emails')
            ->where('related_model_type', 'App\\Models\\PasswordlessLogin')
            ->update(['related_model_type' => 'passwordless_login']);

        // Revert participant related_model_type
        DB::table('emails')
            ->where('related_model_type', 'App\\Models\\Participant')
            ->update(['related_model_type' => 'participant']);

        // Revert task related_model_type
        DB::table('emails')
            ->where('related_model_type', 'App\\Models\\Task')
            ->update(['related_model_type' => 'task']);

        // Revert session related_model_type
        DB::table('emails')
            ->where('related_model_type', 'App\\Models\\Session')
            ->update(['related_model_type' => 'session']);
    }
};