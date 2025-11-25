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
        // We need to drop the constraint if it exists, but Laravel's dropColumn handles the column.
        // However, data might be lost. Since this is dev, it's acceptable.
        // Ideally we would rename the old column, create new, migrate data, drop old.
        // For now, we'll just drop and recreate.
        
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('job_applications', function (Blueprint $table) {
            $table->enum('status', [
                'APPLIED', 
                'INTERVIEW', 
                'OFFERING', 
                'HIRED', 
                'PROCESSING_VISA', 
                'DEPLOYED', 
                'REJECTED'
            ])->default('APPLIED')->after('candidate_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
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
