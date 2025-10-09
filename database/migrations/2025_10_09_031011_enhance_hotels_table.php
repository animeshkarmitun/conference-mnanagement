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
        Schema::table('hotels', function (Blueprint $table) {
            $table->string('contact_email')->nullable()->after('address');
            $table->string('contact_phone')->nullable()->after('contact_email');
            $table->string('website')->nullable()->after('contact_phone');
            $table->json('amenities')->nullable()->after('website'); // WiFi, Pool, Gym, etc.
            $table->time('check_in_time')->default('15:00')->after('amenities');
            $table->time('check_out_time')->default('11:00')->after('check_in_time');
            $table->boolean('is_active')->default(true)->after('check_out_time');
            $table->text('description')->nullable()->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn([
                'contact_email',
                'contact_phone', 
                'website',
                'amenities',
                'check_in_time',
                'check_out_time',
                'is_active',
                'description'
            ]);
        });
    }
};
