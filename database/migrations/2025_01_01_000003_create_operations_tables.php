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
        // Jobs
        Schema::create('jobs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('client_profile_id')->constrained('client_profiles');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('requirements')->nullable();
            $table->foreignId('job_category_id')->constrained('master_job_categories');
            $table->foreignId('location_id')->constrained('master_locations');
            $table->decimal('salary_min', 15, 2)->nullable();
            $table->decimal('salary_max', 15, 2)->nullable();
            $table->integer('quota')->default(1);
            $table->timestamp('deadline')->nullable();
            $table->string('status')->default('DRAFT'); // DRAFT, PUBLISHED, CLOSED
            $table->timestamps();
        });

        // Job Applications
        Schema::create('job_applications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('job_id')->constrained('jobs');
            $table->foreignUuid('candidate_id')->constrained('candidates');
            $table->timestamp('applied_at')->useCurrent();
            $table->timestamp('reviewed_at')->nullable();
            $table->uuid('reviewed_by')->nullable(); // User ID
            $table->text('rejection_reason')->nullable();
            $table->text('revision_notes')->nullable();
            $table->integer('current_step')->default(1);
            $table->timestamp('interview_date')->nullable();
            $table->string('interview_location')->nullable();
            $table->text('interview_address')->nullable();
            $table->text('interview_notes')->nullable();
            $table->text('interview_feedback')->nullable();
            $table->text('admin_notes')->nullable();
            $table->text('cover_letter')->nullable();
            $table->text('notes')->nullable();
            $table->string('offering_letter_path')->nullable();
            $table->string('status')->default('APPLIED'); // APPLIED, REVIEW, PRE_INTERVIEW, INTERVIEW, OFFERING, HIRED, PROCESSING_VISA, DEPLOYED, REJECTED
            $table->timestamps();
        });

        // Interviews
        Schema::create('interviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('application_id')->nullable(); // Nullable for Pre-Interview
            $table->foreignUuid('candidate_id')->nullable()->constrained('candidates')->cascadeOnDelete();
            $table->foreignUuid('interviewer_id')->constrained('users');
            $table->string('stage')->default('USER_INTERVIEW'); // PRE_INTERVIEW, USER_INTERVIEW
            $table->timestamp('scheduled_at');
            $table->string('type'); // ONLINE, OFFLINE
            $table->string('meeting_link')->nullable();
            $table->text('location_address')->nullable();
            $table->string('result')->nullable(); // PASSED, FAILED
            $table->text('feedback_notes')->nullable();
            $table->timestamps();
        });

        // Deployments
        Schema::create('deployments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('application_id')->unique()->constrained('job_applications');
            $table->string('contract_number')->nullable();
            $table->date('signed_at')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('medical_status')->default('PENDING'); // FIT, UNFIT, PENDING
            $table->date('check_date')->nullable();
            $table->string('reference_code')->nullable();
            $table->string('visa_number')->nullable();
            $table->date('visa_expiry')->nullable();
            $table->string('epmi_id')->nullable();
            $table->string('epmi_status')->nullable();
            $table->string('flight_airline')->nullable();
            $table->string('flight_number')->nullable();
            $table->timestamp('departure_at')->nullable();
            $table->timestamp('arrival_at')->nullable();
            $table->timestamps();
        });

        // Candidate Documents
        Schema::create('candidate_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('candidate_id')->constrained('candidates');
            $table->foreignId('document_type_id')->constrained('master_document_types');
            $table->uuid('application_id')->nullable(); // Optional link to application
            $table->string('file_path');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->string('status')->default('PENDING'); // PENDING, UPLOADED, VALID, INVALID
            $table->text('rejection_note')->nullable();
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamps();
        });

        // Profile Views
        Schema::create('profile_views', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('candidate_id')->constrained('candidates');
            $table->uuid('viewer_id')->nullable(); // User ID
            $table->timestamp('viewed_at')->useCurrent();
            $table->timestamps();
        });

        // Notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id'); // Can be User or Candidate UUID
            $table->string('title');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->string('type');
            $table->uuid('related_id')->nullable();
            $table->timestamps();
        });

        // Admin Notifications
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('candidate_id')->constrained('candidates')->cascadeOnDelete();
            $table->string('type');
            $table->string('title');
            $table->text('message');
            $table->string('url')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_notifications');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('profile_views');
        Schema::dropIfExists('candidate_documents');
        Schema::dropIfExists('deployments');
        Schema::dropIfExists('interviews');
        Schema::dropIfExists('job_applications');
        Schema::dropIfExists('jobs');
    }
};
