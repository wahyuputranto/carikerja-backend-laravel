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
        // Client Profiles
        Schema::create('client_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->string('company_name');
            $table->string('industry')->nullable();
            $table->text('address')->nullable();
            $table->string('website')->nullable();
            $table->string('pic_name')->nullable();
            $table->string('pic_phone')->nullable();
            $table->timestamps();
        });

        // Vendor Profiles
        Schema::create('vendor_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->string('company_name');
            $table->string('service_type');
            $table->text('address')->nullable();
            $table->string('website')->nullable();
            $table->string('pic_name')->nullable();
            $table->string('pic_phone')->nullable();
            $table->timestamps();
        });

        // Candidates
        Schema::create('candidates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->string('hiring_status')->default('AVAILABLE');
            $table->foreignId('interested_job_category_id')->nullable()->constrained('master_job_categories')->nullOnDelete();
            $table->timestamps();
        });

        // Candidate Profiles
        Schema::create('candidate_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('candidate_id')->unique()->constrained('candidates')->onDelete('cascade');
            $table->string('nik')->unique()->nullable();
            $table->date('birth_date')->nullable();
            $table->string('birth_place')->nullable();
            $table->string('gender')->nullable(); // M, F
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('postal_code')->nullable();
            $table->text('about_me')->nullable();
            $table->string('photo_url')->nullable();
            $table->string('cv_url')->nullable();
            $table->timestamps();
        });

        // Candidate Personal Details
        Schema::create('candidate_personal_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('candidate_id')->unique()->constrained('candidates')->onDelete('cascade');
            $table->string('fathers_name')->nullable();
            $table->string('mothers_name')->nullable();
            $table->integer('height')->nullable();
            $table->integer('weight')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('citizenship')->default('IDN');
            $table->string('religion')->nullable();
            $table->timestamps();
        });

        // Candidate Passports
        Schema::create('candidate_passports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('candidate_id')->constrained('candidates')->onDelete('cascade');
            $table->string('passport_number');
            $table->date('issue_date')->nullable();
            $table->string('issued_by')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('file_path')->nullable();
            $table->timestamps();
        });

        // Candidate Emergency Contacts
        Schema::create('candidate_emergency_contacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('candidate_id')->constrained('candidates')->onDelete('cascade');
            $table->string('name');
            $table->string('contact_number');
            $table->string('relation')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        // Candidate Educations
        Schema::create('candidate_educations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('candidate_id')->constrained('candidates')->onDelete('cascade');
            $table->string('institution_name');
            $table->string('degree')->nullable();
            $table->string('major')->nullable();
            $table->integer('start_year')->nullable();
            $table->integer('end_year')->nullable();
            $table->boolean('is_current')->default(false);
            $table->decimal('gpa', 3, 2)->nullable();
            $table->timestamps();
        });

        // Candidate Non Formal Educations
        Schema::create('candidate_non_formal_educations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('candidate_id')->constrained('candidates')->onDelete('cascade');
            $table->string('year')->nullable();
            $table->string('name');
            $table->string('subject')->nullable();
            $table->string('country')->default('IDN');
            $table->timestamps();
        });

        // Candidate Experiences
        Schema::create('candidate_experiences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('candidate_id')->constrained('candidates')->onDelete('cascade');
            $table->string('company_name');
            $table->string('position');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->default(false);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Candidate Languages
        Schema::create('candidate_languages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('candidate_id')->constrained('candidates')->onDelete('cascade');
            $table->string('language');
            $table->string('speaking')->nullable();
            $table->string('reading')->nullable();
            $table->string('writing')->nullable();
            $table->timestamps();
        });

        // Candidate Skills
        Schema::create('candidate_skills', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('candidate_id')->constrained('candidates')->onDelete('cascade');
            $table->string('skill_name');
            $table->string('proficiency_level')->default('Beginner');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_skills');
        Schema::dropIfExists('candidate_languages');
        Schema::dropIfExists('candidate_experiences');
        Schema::dropIfExists('candidate_non_formal_educations');
        Schema::dropIfExists('candidate_educations');
        Schema::dropIfExists('candidate_emergency_contacts');
        Schema::dropIfExists('candidate_passports');
        Schema::dropIfExists('candidate_personal_details');
        Schema::dropIfExists('candidate_profiles');
        Schema::dropIfExists('candidates');
        Schema::dropIfExists('vendor_profiles');
        Schema::dropIfExists('client_profiles');
    }
};
