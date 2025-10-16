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
        // Check if the index exists and drop it if it does
        $indexes = DB::select("SHOW INDEX FROM users WHERE Key_name LIKE '%email%'");
        
        if (!empty($indexes)) {
            // Find the email unique index
            $emailIndex = collect($indexes)->firstWhere('Non_unique', 0);
            if ($emailIndex) {
                DB::statement("ALTER TABLE users DROP INDEX {$emailIndex->Key_name}");
            }
        }
        
        // Create the index with a specific length limit (191 characters)
        DB::statement('ALTER TABLE users ADD UNIQUE users_email_unique (email(191))');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore the original unique index
        DB::statement('ALTER TABLE users DROP INDEX users_email_unique');
        DB::statement('ALTER TABLE users ADD UNIQUE users_email_unique (email)');
    }
};