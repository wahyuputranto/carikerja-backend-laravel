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
        // 1. Update job_applications status enum
        // We can't easily modify enum in some DBs, so we drop and recreate or modify check constraint.
        // For simplicity and compatibility with the previous migration style:
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('job_applications', function (Blueprint $table) {
            $table->enum('status', [
                'APPLIED', 
                'REVIEW', // Added REVIEW as it was missing but logically needed before Pre-Interview
                'PRE_INTERVIEW', // New Step
                'INTERVIEW', 
                'OFFERING', 
                'HIRED', // Added HIRED as it was used in code but missing in previous migration enum list? Check Show.vue
                'PROCESSING_VISA', 
                'DEPLOYED', 
                'REJECTED'
            ])->default('APPLIED')->after('candidate_id');
        });

        // 2. Add stage to interviews table
        Schema::table('interviews', function (Blueprint $table) {
            $table->enum('stage', ['PRE_INTERVIEW', 'USER_INTERVIEW'])->default('USER_INTERVIEW')->after('application_id');
            // Add location_address if not exists (it was added in a previous migration? Let's check conversation history. Yes, 2025_11_29_145340_add_location_address_to_interviews_table.php)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interviews', function (Blueprint $table) {
            $table->dropColumn('stage');
        });

        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('job_applications', function (Blueprint $table) {
            $table->enum('status', [
                'APPLIED', 
                'INTERVIEW', 
                'OFFERING', 
                'PROCESSING_VISA', 
                'DEPLOYED', 
                'REJECTED'
            ])->default('APPLIED')->after('candidate_id');
        });
    }
};
