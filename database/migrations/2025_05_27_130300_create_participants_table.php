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
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('conference_id')->constrained()->onDelete('cascade');
            $table->foreignId('participant_type_id')->constrained('participant_types');
            $table->enum('visa_status', ['required', 'not_required', 'pending'])->default('pending');
            $table->boolean('travel_form_submitted')->default(false);
            $table->text('bio')->nullable();
            $table->boolean('approved')->default(false);
            $table->string('organization')->nullable();
            $table->string('dietary_needs')->nullable();
            $table->boolean('travel_intent')->default(false);
            $table->enum('registration_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
