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
        Schema::create('email_template_variables', function (Blueprint $table) {
            $table->id();
            $table->string('variable_name', 100)->unique();
            $table->text('variable_description')->nullable();
            $table->string('example_value', 255)->nullable();
            $table->boolean('is_system_variable')->default(false);
            $table->timestamps();
            
            $table->index('variable_name');
            $table->index('is_system_variable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_template_variables');
    }
};