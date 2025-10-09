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
            $table->unsignedBigInteger('room_id')->nullable()->after('hotel_id');
            $table->string('travel_intent')->default('domestic')->after('extra_nights');
            $table->date('room_check_in')->nullable()->after('travel_intent');
            $table->date('room_check_out')->nullable()->after('room_check_in');
            
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('travel_details', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropColumn(['room_id', 'travel_intent', 'room_check_in', 'room_check_out']);
        });
    }
};