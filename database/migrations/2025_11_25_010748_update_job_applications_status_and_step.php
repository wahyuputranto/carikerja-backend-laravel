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
        Schema::table('job_applications', function (Blueprint $table) {
            // Drop existing status column to redefine it
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
            
            $table->integer('current_step')->default(1)->after('status'); // 1-12
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn('current_step');
            $table->dropColumn('status');
        });

        Schema::table('job_applications', function (Blueprint $table) {
             // Restore previous status enum
             $table->enum('status', [
                'PENDING',
                'APPLIED', 
                'REVIEWED', 
                'INTERVIEW', 
                'REJECTED', 
                'ACCEPTED',
                'REVISION_REQUESTED'
            ])->default('PENDING')->after('candidate_id');
        });
    }
};
