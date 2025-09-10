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
        Schema::table('conference_doc_items', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('content');
            $table->string('file_name')->nullable()->after('file_path');
            $table->bigInteger('file_size')->nullable()->after('file_name');
            $table->string('mime_type')->nullable()->after('file_size');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null')->after('mime_type');
            $table->boolean('is_public')->default(false)->after('uploaded_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conference_doc_items', function (Blueprint $table) {
            $table->dropColumn(['file_path', 'file_name', 'file_size', 'mime_type', 'uploaded_by', 'is_public']);
        });
    }
};
