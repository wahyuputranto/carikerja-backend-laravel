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
            $table->text('interview_address')->nullable()->after('interview_location'); // Untuk alamat fisik jika Offline
            $table->text('interview_feedback')->nullable()->after('interview_notes'); // Untuk hasil interview
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn(['interview_address', 'interview_feedback']);
        });
    }
};
