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
            $table->dateTime('interview_date')->nullable();
            $table->string('interview_location')->nullable(); // Bisa link meeting atau alamat fisik
            $table->text('interview_notes')->nullable(); // Catatan khusus untuk interview
            $table->text('admin_notes')->nullable(); // Catatan umum dari admin (misal alasan reject)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn(['interview_date', 'interview_location', 'interview_notes', 'admin_notes']);
        });
    }
};
