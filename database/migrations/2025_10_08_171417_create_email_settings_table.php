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
        Schema::create('email_settings', function (Blueprint $table) {
            $table->id();
            $table->string('email_type', 50)->unique();
            $table->text('subject_template');
            $table->text('greeting_template');
            $table->longText('body_template');
            $table->text('closing_template');
            $table->text('system_signature');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('email_type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_settings');
    }
};