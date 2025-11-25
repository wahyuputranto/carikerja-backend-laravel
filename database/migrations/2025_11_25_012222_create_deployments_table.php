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
        Schema::create('deployments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('application_id')->unique()->constrained('job_applications')->onDelete('cascade');
            
            // Contract
            $table->string('contract_number')->nullable();
            $table->date('signed_at')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            // Medical
            $table->enum('medical_status', ['FIT', 'UNFIT', 'PENDING'])->default('PENDING');
            $table->date('check_date')->nullable();

            // Legal
            $table->string('reference_code')->nullable(); // Step 7
            $table->string('visa_number')->nullable(); // Step 9
            $table->date('visa_expiry')->nullable();

            // E-PMI
            $table->string('epmi_id')->nullable(); // Step 10
            $table->string('epmi_status')->nullable();

            // Ticketing
            $table->string('flight_airline')->nullable();
            $table->string('flight_number')->nullable();
            $table->timestamp('departure_at')->nullable();
            $table->timestamp('arrival_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deployments');
    }
};
