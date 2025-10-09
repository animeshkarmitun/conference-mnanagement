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
            $table->foreignId('room_type_id')->nullable()->after('hotel_id')->constrained('room_types')->onDelete('set null');
            $table->integer('floor_number')->nullable()->after('room_number');
            $table->integer('max_occupancy')->default(2)->after('floor_number');
            $table->json('amenities')->nullable()->after('max_occupancy'); // Room-specific amenities
            $table->string('view_type')->nullable()->after('amenities'); // City view, Garden view, etc.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['room_type_id']);
            $table->dropColumn([
                'room_type_id',
                'floor_number',
                'max_occupancy',
                'amenities',
                'view_type'
            ]);
        });
    }
};
