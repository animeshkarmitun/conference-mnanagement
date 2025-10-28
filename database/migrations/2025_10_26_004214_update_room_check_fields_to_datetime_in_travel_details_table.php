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
        Schema::table('travel_details', function (Blueprint $table) {
            $table->datetime('room_check_in')->nullable()->change();
            $table->datetime('room_check_out')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('travel_details', function (Blueprint $table) {
            $table->date('room_check_in')->nullable()->change();
            $table->date('room_check_out')->nullable()->change();
        });
    }
};
