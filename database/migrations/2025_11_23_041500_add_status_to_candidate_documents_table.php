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
        Schema::table('candidate_documents', function (Blueprint $table) {
            $table->enum('status', ['PENDING', 'UPLOADED', 'VALID', 'INVALID'])->default('PENDING');
            $table->text('rejection_note')->nullable();
            $table->timestamp('uploaded_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidate_documents', function (Blueprint $table) {
            $table->dropColumn(['status', 'rejection_note', 'uploaded_at']);
        });
    }
};
