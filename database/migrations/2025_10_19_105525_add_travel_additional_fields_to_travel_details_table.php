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
            $table->string('itineraries_status')->nullable()->after('extra_nights');
            $table->string('takeoff_airport')->nullable()->after('itineraries_status');
            $table->text('flight_info_details')->nullable()->after('takeoff_airport');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('travel_details', function (Blueprint $table) {
            $table->dropColumn(['itineraries_status', 'takeoff_airport', 'flight_info_details']);
        });
    }
};
