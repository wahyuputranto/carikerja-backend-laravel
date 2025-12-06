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
        Schema::create('candidate_computer_skills', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('candidate_id')->constrained('candidates')->onDelete('cascade');
            $table->string('skill_name'); // e.g. MS Office, Photoshop
            $table->string('proficiency_level')->default('Fair'); // Fair, Good, Fluent/Expert
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_computer_skills');
    }
};
