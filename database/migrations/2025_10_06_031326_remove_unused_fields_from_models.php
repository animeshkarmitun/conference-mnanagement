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
        // Remove unused fields from users table
        Schema::table('users', function (Blueprint $table) {
            // These fields were removed from forms but still exist in database
            if (Schema::hasColumn('users', 'biography')) {
                $table->dropColumn('biography');
            }
            if (Schema::hasColumn('users', 'current_designation')) {
                $table->dropColumn('current_designation');
            }
            if (Schema::hasColumn('users', 'media_designation')) {
                $table->dropColumn('media_designation');
            }
            if (Schema::hasColumn('users', 'photo')) {
                $table->dropColumn('photo');
            }
            if (Schema::hasColumn('users', 'resume')) {
                $table->dropColumn('resume');
            }
        });

        // Remove unused fields from participants table
        Schema::table('participants', function (Blueprint $table) {
            // This field was replaced by travel_intent
            if (Schema::hasColumn('participants', 'travel_form_submitted')) {
                $table->dropColumn('travel_form_submitted');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add fields to users table
        Schema::table('users', function (Blueprint $table) {
            $table->text('biography')->nullable()->after('current_designation');
            $table->string('current_designation')->nullable()->after('sector');
            $table->enum('media_designation', ['reporter', 'camera_crew', 'photographer'])->nullable()->after('media_type');
            $table->string('photo')->nullable()->after('profile_picture');
            $table->string('resume')->nullable()->after('photo');
        });

        // Re-add fields to participants table
        Schema::table('participants', function (Blueprint $table) {
            $table->boolean('travel_form_submitted')->default(false)->after('travel_intent');
        });
    }
};