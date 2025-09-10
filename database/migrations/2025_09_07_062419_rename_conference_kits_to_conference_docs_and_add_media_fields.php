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
        // Rename conference_kits table to conference_docs
        Schema::rename('conference_kits', 'conference_docs');
        
        // Rename conference_kit_items table to conference_doc_items
        Schema::rename('conference_kit_items', 'conference_doc_items');
        
        // Add media fields to conference_docs table
        Schema::table('conference_docs', function (Blueprint $table) {
            $table->string('media_type')->nullable();
            $table->string('media_url')->nullable();
            $table->string('media_thumbnail')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove media fields from conference_docs table
        Schema::table('conference_docs', function (Blueprint $table) {
            $table->dropColumn(['media_type', 'media_url', 'media_thumbnail']);
        });
        
        // Rename conference_doc_items table back to conference_kit_items
        Schema::rename('conference_doc_items', 'conference_kit_items');
        
        // Rename conference_docs table back to conference_kits
        Schema::rename('conference_docs', 'conference_kits');
    }
};
