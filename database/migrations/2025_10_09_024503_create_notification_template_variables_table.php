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
        Schema::create('notification_template_variables', function (Blueprint $table) {
            $table->id();
            $table->string('variable_name', 100);
            $table->string('notification_type', 50);
            $table->text('variable_description');
            $table->string('example_value', 255);
            $table->boolean('is_system_variable')->default(false);
            $table->timestamps();
            
            $table->index(['notification_type', 'variable_name'], 'ntv_type_var_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_template_variables');
    }
};