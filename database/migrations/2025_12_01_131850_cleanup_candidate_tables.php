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
        Schema::table('candidate_profiles', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['interested_job_category_id']);
            $table->dropColumn('interested_job_category_id');
            $table->dropColumn('last_education');
        });

        Schema::table('candidate_personal_details', function (Blueprint $table) {
            $table->dropColumn('computer_skills');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidate_profiles', function (Blueprint $table) {
            $table->foreignId('interested_job_category_id')
                  ->nullable()
                  ->constrained('master_job_categories')
                  ->nullOnDelete();
            $table->string('last_education')->nullable();
        });

        Schema::table('candidate_personal_details', function (Blueprint $table) {
            $table->text('computer_skills')->nullable();
        });
    }
};
