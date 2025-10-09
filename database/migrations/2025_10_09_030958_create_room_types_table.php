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
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50); // Standard, Deluxe, Suite, Executive, Presidential
            $table->text('description')->nullable();
            $table->integer('default_beds')->default(1);
            $table->decimal('base_price', 10, 2)->nullable();
            $table->json('amenities')->nullable(); // WiFi, TV, Mini-bar, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_types');
    }
};
