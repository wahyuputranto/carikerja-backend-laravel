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
        Schema::create('pre_interviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('candidate_id')->constrained('candidates')->cascadeOnDelete();
            $table->uuid('interviewer_id')->nullable();
            $table->timestamp('scheduled_at');
            $table->string('type'); // ONLINE, OFFLINE
            $table->string('meeting_link')->nullable();
            $table->text('location_address')->nullable();
            $table->string('result')->nullable(); // PASSED, FAILED
            $table->text('feedback_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pre_interviews');
    }
};
