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
            // Update status enum to include new statuses
            $table->dropColumn('status');
        });
        
        Schema::table('job_applications', function (Blueprint $table) {
            $table->enum('status', [
                'PENDING',
                'APPLIED', 
                'REVIEWED', 
                'INTERVIEW', 
                'REJECTED', 
                'ACCEPTED',
                'REVISION_REQUESTED'
            ])->default('PENDING')->after('candidate_id');
            
            // Review tracking
            $table->timestamp('reviewed_at')->nullable()->after('applied_at');
            $table->foreignUuid('reviewed_by')->nullable()->constrained('users')->after('reviewed_at');
            
            // Rejection and revision notes
            $table->text('rejection_reason')->nullable()->after('reviewed_by');
            $table->text('revision_notes')->nullable()->after('rejection_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn([
                'reviewed_at',
                'reviewed_by',
                'rejection_reason',
                'revision_notes',
            ]);
            $table->dropColumn('status');
        });
        
        Schema::table('job_applications', function (Blueprint $table) {
            $table->enum('status', ['APPLIED', 'REVIEWED', 'INTERVIEW', 'REJECTED', 'ACCEPTED'])->default('APPLIED');
        });
    }
};
