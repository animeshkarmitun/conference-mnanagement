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
        Schema::create('conference_kit_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kit_id')->constrained('conference_kits')->onDelete('cascade');
            $table->enum('type', ['SessionLink', 'Contact', 'CityGuide']);
            $table->json('content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conference_kit_items');
    }
};
