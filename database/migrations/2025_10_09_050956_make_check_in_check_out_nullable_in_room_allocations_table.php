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
        Schema::table('room_allocations', function (Blueprint $table) {
            $table->dateTime('check_in')->nullable()->change();
            $table->dateTime('check_out')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_allocations', function (Blueprint $table) {
            $table->dateTime('check_in')->nullable(false)->change();
            $table->dateTime('check_out')->nullable(false)->change();
        });
    }
};
