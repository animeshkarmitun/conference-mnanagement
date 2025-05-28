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
        Schema::create('communications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('conference_id')->constrained()->onDelete('cascade');
            $table->json('recipient_group')->nullable();
            $table->string('subject');
            $table->text('content');
            $table->enum('status', ['sent', 'delivered', 'responded', 'failed'])->default('sent');
            $table->string('attachment')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communications');
    }
};
