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
        Schema::table('participant_types', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->string('category')->default('general')->after('description');
            $table->boolean('requires_approval')->default(false)->after('category');
            $table->boolean('has_special_privileges')->default(false)->after('requires_approval');
            $table->integer('display_order')->default(0)->after('has_special_privileges');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participant_types', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'category',
                'requires_approval',
                'has_special_privileges',
                'display_order'
            ]);
        });
    }
};
