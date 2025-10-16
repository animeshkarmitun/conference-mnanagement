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
            // Add all enhanced participant fields that are missing
            $table->string('pronoun', 20)->nullable()->after('last_name');
            $table->string('contact_no', 20)->nullable()->after('pronoun');
            $table->string('whatsapp_no', 20)->nullable()->after('contact_no');
            $table->string('messaging_type', 20)->nullable()->after('whatsapp_no');
            $table->string('messaging_number', 20)->nullable()->after('messaging_type');
            $table->string('field_of_work_study')->nullable()->after('messaging_number');
            $table->string('designation')->nullable()->after('field_of_work_study');
            $table->string('organization_institution')->nullable()->after('designation');
            $table->boolean('is_student')->nullable()->after('organization_institution');
            $table->string('year', 50)->nullable()->after('is_student');
            $table->string('department_name')->nullable()->after('year');
            $table->string('institution_name')->nullable()->after('department_name');
            $table->text('address')->nullable()->after('institution_name');
            $table->string('home_district', 100)->nullable()->after('address');
            $table->string('country', 100)->nullable()->after('home_district');
            $table->string('nid_passport_birth_certificate')->nullable()->after('country');
            $table->string('how_found_bobc', 50)->nullable()->after('nid_passport_birth_certificate');
            $table->boolean('attended_previous_bobc')->nullable()->after('how_found_bobc');
            $table->text('expertise_interests')->nullable()->after('attended_previous_bobc');
            
            // Media/Speaker fields
            $table->string('media_type', 50)->nullable()->after('expertise_interests');
            $table->string('other_contact_type', 50)->nullable()->after('media_type');
            $table->string('other_contact_no', 20)->nullable()->after('other_contact_type');
            $table->string('dietary_requirements', 50)->nullable()->after('other_contact_no');
            $table->string('dietary_requirements_other')->nullable()->after('dietary_requirements');
            $table->string('sector', 50)->nullable()->after('dietary_requirements_other');
            $table->string('current_designation')->nullable()->after('sector');
            $table->text('areas_of_expertise')->nullable()->after('current_designation');
            $table->string('preferred_topic')->nullable()->after('areas_of_expertise');
            $table->string('linkedin_link')->nullable()->after('preferred_topic');
            $table->string('twitter_link')->nullable()->after('linkedin_link');
            $table->string('facebook_link')->nullable()->after('twitter_link');
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
                'pronoun', 'contact_no', 'whatsapp_no', 'messaging_type', 'messaging_number',
                'field_of_work_study', 'designation', 'organization_institution', 'is_student',
                'year', 'department_name', 'institution_name', 'address', 'home_district',
                'country', 'nid_passport_birth_certificate', 'how_found_bobc', 'attended_previous_bobc',
                'expertise_interests', 'media_type', 'other_contact_type', 'other_contact_no',
                'dietary_requirements', 'dietary_requirements_other', 'sector', 'current_designation',
                'areas_of_expertise', 'preferred_topic', 'linkedin_link', 'twitter_link',
                'facebook_link', 'has_valid_passport', 'had_visa_issue_bd', 'visa_issue_explanation'
            ]);
        });
    }
};
