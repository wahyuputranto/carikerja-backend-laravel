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
        Schema::table('jobs', function (Blueprint $table) {
            $table->uuid('client_batch_id')->nullable()->after('client_profile_id');
            // Check if constraint exists effectively or just add index if you want
            // Since it's uuid, we usually don't assume bigIncrements.
            // Let's assume standard foreign key if possible, or just index for now to avoid issues if tables created out of order (though here it's fine).
            // Actually, client_batches uses primary key 'id' (uuid).
            // $table->foreign('client_batch_id')->references('id')->on('client_batches')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('client_batch_id');
        });
    }
};
