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
        Schema::create('interviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('application_id')->constrained('job_applications')->onDelete('cascade');
            $table->foreignUuid('interviewer_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('scheduled_at');
            $table->enum('type', ['ONLINE', 'OFFLINE']);
            $table->string('meeting_link')->nullable();
            $table->enum('result', ['PASSED', 'FAILED'])->nullable();
            $table->text('feedback_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interviews');
    }
};
