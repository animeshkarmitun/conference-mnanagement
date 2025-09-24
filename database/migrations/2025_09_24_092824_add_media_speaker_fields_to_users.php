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
        Schema::table('users', function (Blueprint $table) {
            // Media-specific
            $table->enum('media_type', ['print', 'television', 'online_portal'])->nullable()->after('profession');
            $table->enum('media_designation', ['reporter', 'camera_crew', 'photographer'])->nullable()->after('media_type');

            // Speaker-specific contacts and dietary
            $table->enum('other_contact_type', ['whatsapp', 'telegram', 'signal'])->nullable()->after('whatsapp_no');
            $table->string('other_contact_no', 50)->nullable()->after('other_contact_type');
            $table->enum('dietary_requirements', ['veg', 'non_veg', 'vegan', 'others'])->nullable()->after('dietary_needs');
            $table->string('dietary_requirements_other')->nullable()->after('dietary_requirements');

            // Speaker professional information
            $table->enum('sector', ['academia','government','international_organization','media','ngo','private','think_tank'])->nullable()->after('organization');
            $table->string('current_designation')->nullable()->after('sector');
            $table->text('biography')->nullable()->after('current_designation');
            $table->text('areas_of_expertise')->nullable()->after('biography');
            $table->text('preferred_topic')->nullable()->after('areas_of_expertise');

            // Social links
            $table->string('linkedin_link')->nullable()->after('preferred_topic');
            $table->string('twitter_link')->nullable()->after('linkedin_link');
            $table->string('facebook_link')->nullable()->after('twitter_link');

            // Passport/Visa info
            $table->boolean('has_valid_passport')->nullable()->after('facebook_link');
            $table->boolean('had_visa_issue_bd')->nullable()->after('has_valid_passport');
            $table->text('visa_issue_explanation')->nullable()->after('had_visa_issue_bd');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'media_type',
                'media_designation',
                'other_contact_type',
                'other_contact_no',
                'dietary_requirements',
                'dietary_requirements_other',
                'sector',
                'current_designation',
                'biography',
                'areas_of_expertise',
                'preferred_topic',
                'linkedin_link',
                'twitter_link',
                'facebook_link',
                'has_valid_passport',
                'had_visa_issue_bd',
                'visa_issue_explanation',
            ]);
        });
    }
};
