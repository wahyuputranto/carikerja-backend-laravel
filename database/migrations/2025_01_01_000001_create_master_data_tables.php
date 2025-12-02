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
        // Master Locations
        Schema::create('master_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('master_locations')->nullOnDelete();
            $table->string('name');
            $table->string('type'); // COUNTRY, PROVINCE, CITY
            $table->timestamps();
        });

        // Master Job Categories
        Schema::create('master_job_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Master Skills
        Schema::create('master_skills', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('category')->nullable();
            $table->timestamps();
        });

        // Master Document Types
        Schema::create('master_document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_mandatory')->default(false);
            $table->json('allowed_mimetypes')->nullable();
            $table->boolean('chunkable')->default(false);
            $table->string('stage')->default('REGISTRATION'); // REGISTRATION, POST_HIRING
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_document_types');
        Schema::dropIfExists('master_skills');
        Schema::dropIfExists('master_job_categories');
        Schema::dropIfExists('master_locations');
    }
};
