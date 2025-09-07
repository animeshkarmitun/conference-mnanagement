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
        // Rename the column using raw SQL
        DB::statement('ALTER TABLE conference_doc_items CHANGE kit_id doc_id bigint(20) unsigned NOT NULL');
        
        // Add the new foreign key constraint
        Schema::table('conference_doc_items', function (Blueprint $table) {
            $table->foreign('doc_id')->references('id')->on('conference_docs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the foreign key constraint
        Schema::table('conference_doc_items', function (Blueprint $table) {
            $table->dropForeign(['doc_id']);
        });
        
        // Rename the column back using raw SQL
        DB::statement('ALTER TABLE conference_doc_items CHANGE doc_id kit_id bigint(20) unsigned NOT NULL');
        
        // Add the original foreign key constraint
        Schema::table('conference_doc_items', function (Blueprint $table) {
            $table->foreign('kit_id')->references('id')->on('conference_docs')->onDelete('cascade');
        });
    }
};
