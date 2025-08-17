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
        Schema::create('id_cards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['participant', 'company_worker']);
            $table->foreignId('conference_id')->nullable()->constrained()->onDelete('cascade');
            $table->json('template_config')->nullable(); // Store card layout configuration
            $table->string('background_color')->default('#ffffff');
            $table->string('text_color')->default('#000000');
            $table->string('accent_color')->default('#007bff');
            $table->boolean('include_qr_code')->default(true);
            $table->boolean('include_photo')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('id_cards');
    }
};
