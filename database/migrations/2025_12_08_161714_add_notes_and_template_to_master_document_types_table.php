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
        Schema::table('master_document_types', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('slug');
            $table->string('template')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_document_types', function (Blueprint $table) {
            $table->dropColumn(['notes', 'template']);
        });
    }
};
