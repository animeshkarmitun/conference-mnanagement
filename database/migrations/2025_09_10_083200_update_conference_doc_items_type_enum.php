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
        // Update the ENUM to include MediaFile
        DB::statement("ALTER TABLE conference_doc_items MODIFY COLUMN type ENUM('SessionLink', 'Contact', 'CityGuide', 'MediaFile') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original ENUM values
        DB::statement("ALTER TABLE conference_doc_items MODIFY COLUMN type ENUM('SessionLink', 'Contact', 'CityGuide') NOT NULL");
    }
};
