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
            $table->text('hotel_info')->nullable()->after('departure_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('travel_details', function (Blueprint $table) {
            $table->dropColumn('hotel_info');
        });
    }
};
