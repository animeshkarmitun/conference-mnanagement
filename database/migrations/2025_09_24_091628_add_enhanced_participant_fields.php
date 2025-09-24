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
            // Add new fields for enhanced participant registration
            $table->string('photo')->nullable()->after('profile_picture')->comment('Participant photo (1200x800px, 400kb)');
            $table->enum('pronoun', ['he_him', 'she_her', 'they_them'])->nullable()->after('gender');
            $table->string('contact_no')->nullable()->after('pronoun');
            $table->string('whatsapp_no')->nullable()->after('contact_no');
            $table->string('field_of_work_study')->nullable()->after('whatsapp_no');
            $table->string('designation')->nullable()->after('field_of_work_study');
            $table->string('organization_institution')->nullable()->after('designation');
            $table->boolean('is_student')->nullable()->after('organization_institution')->comment('Skip section if student');
            $table->enum('year', ['honors_final_year', 'masters'])->nullable()->after('is_student');
            $table->string('department_name')->nullable()->after('year');
            $table->string('institution_name')->nullable()->after('department_name');
            $table->text('address')->nullable()->after('institution_name');
            $table->string('home_district')->nullable()->after('address');
            $table->string('nid_passport_birth_certificate')->nullable()->after('home_district')->comment('Photo max 300kb');
            $table->enum('how_found_bobc', ['social_media', 'bobc_cgs_website', 'friend_teacher_department', 'traditional_media', 'other'])->nullable()->after('nid_passport_birth_certificate');
            $table->boolean('attended_previous_bobc')->nullable()->after('how_found_bobc');
            $table->text('expertise_interests')->nullable()->after('attended_previous_bobc')->comment('200 words max');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'photo',
                'pronoun',
                'contact_no',
                'whatsapp_no',
                'field_of_work_study',
                'designation',
                'organization_institution',
                'is_student',
                'year',
                'department_name',
                'institution_name',
                'address',
                'home_district',
                'nid_passport_birth_certificate',
                'how_found_bobc',
                'attended_previous_bobc',
                'expertise_interests'
            ]);
        });
    }
};
