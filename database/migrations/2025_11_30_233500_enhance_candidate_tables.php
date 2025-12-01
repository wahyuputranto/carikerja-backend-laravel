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
        // 1. Add interested_job_category_id to candidates table
        Schema::table('candidates', function (Blueprint $table) {
            $table->foreignId('interested_job_category_id')
                  ->nullable()
                  ->constrained('master_job_categories')
                  ->nullOnDelete();
        });

        // 2. Create candidate_personal_details table
        Schema::create('candidate_personal_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('candidate_id')->constrained('candidates')->onDelete('cascade');
            $table->string('fathers_name')->nullable();
            $table->string('mothers_name')->nullable();
            $table->integer('height')->nullable(); // in cm
            $table->integer('weight')->nullable(); // in kg
            $table->string('marital_status')->nullable();
            $table->string('citizenship')->default('IDN');
            $table->string('religion')->nullable();
            $table->text('computer_skills')->nullable(); // Added here as per grouping
            $table->timestamps();
        });

        // 3. Create candidate_passports table
        Schema::create('candidate_passports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('candidate_id')->constrained('candidates')->onDelete('cascade');
            $table->string('passport_number');
            $table->date('issue_date')->nullable();
            $table->string('issued_by')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('file_path')->nullable(); // passport_scanned
            $table->timestamps();
        });

        // 4. Create candidate_emergency_contacts table
        Schema::create('candidate_emergency_contacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('candidate_id')->constrained('candidates')->onDelete('cascade');
            $table->string('name');
            $table->string('contact_number');
            $table->string('relation')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        // 5. Create candidate_non_formal_educations table
        Schema::create('candidate_non_formal_educations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('candidate_id')->constrained('candidates')->onDelete('cascade');
            $table->string('year')->nullable();
            $table->string('name'); // Institution/Course Name
            $table->string('subject')->nullable();
            $table->string('country')->default('IDN');
            $table->timestamps();
        });

        // 6. Create candidate_languages table
        Schema::create('candidate_languages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('candidate_id')->constrained('candidates')->onDelete('cascade');
            $table->string('language');
            $table->string('speaking')->nullable();
            $table->string('reading')->nullable();
            $table->string('writing')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_languages');
        Schema::dropIfExists('candidate_non_formal_educations');
        Schema::dropIfExists('candidate_emergency_contacts');
        Schema::dropIfExists('candidate_passports');
        Schema::dropIfExists('candidate_personal_details');

        Schema::table('candidates', function (Blueprint $table) {
            $table->dropForeign(['interested_job_category_id']);
            $table->dropColumn('interested_job_category_id');
        });
    }
};
