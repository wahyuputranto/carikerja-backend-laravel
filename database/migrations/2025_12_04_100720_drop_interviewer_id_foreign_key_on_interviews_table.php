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
            // Drop foreign key constraint to allow NULL values
            // This allows client-scheduled interviews where interviewer_id doesn't reference users table
            $table->dropForeign(['interviewer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interviews', function (Blueprint $table) {
            // Re-add foreign key constraint
            $table->foreign('interviewer_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }
};
