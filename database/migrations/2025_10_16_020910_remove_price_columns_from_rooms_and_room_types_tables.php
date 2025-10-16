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
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('price_per_night');
        });
        
        Schema::table('room_types', function (Blueprint $table) {
            $table->dropColumn('base_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->decimal('price_per_night', 10, 2)->nullable()->after('beds');
        });
        
        Schema::table('room_types', function (Blueprint $table) {
            $table->decimal('base_price', 10, 2)->nullable()->after('default_beds');
        });
    }
};
