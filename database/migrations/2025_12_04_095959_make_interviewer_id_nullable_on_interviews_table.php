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
        Schema::table('interviews', function (Blueprint $table) {
            // Make interviewer_id nullable to allow client-scheduled interviews
            // where interviewer_id references users/admins table which doesn't include clients
            $table->uuid('interviewer_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interviews', function (Blueprint $table) {
            // Revert back to NOT NULL
            $table->uuid('interviewer_id')->nullable(false)->change();
        });
    }
};
