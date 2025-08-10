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
            // Drop the existing enum column
            $table->dropColumn('type');
        });

        Schema::table('notifications', function (Blueprint $table) {
            // Add the new enum column with TaskUpdate included
            $table->enum('type', ['MissingDocuments', 'SessionUpdate', 'TravelUpdate', 'TaskUpdate', 'General'])->after('message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Drop the new enum column
            $table->dropColumn('type');
        });

        Schema::table('notifications', function (Blueprint $table) {
            // Restore the original enum column
            $table->enum('type', ['MissingDocuments', 'SessionUpdate', 'TravelUpdate', 'General'])->after('message');
        });
    }
};
