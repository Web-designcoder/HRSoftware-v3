<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         |--------------------------------------------------------------------------
         | EMPLOYERS (COMPANIES)
         |--------------------------------------------------------------------------
         */
        Schema::create('employers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('city')->nullable();
            $table->string('postcode')->nullable();
            $table->string('country')->default('Australia');
            $table->string('industry')->nullable();
            $table->timestamps();
        });

        /*
         |--------------------------------------------------------------------------
         | EMPLOYER CONTACTS (PIVOT)
         |--------------------------------------------------------------------------
         */
        Schema::create('employer_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained('employers')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('position')->nullable();
            $table->string('permission_level')->nullable();
            $table->timestamps();
            $table->unique(['employer_id', 'user_id']);
        });

        /*
         |--------------------------------------------------------------------------
         | CANDIDATE PROFILES
         |--------------------------------------------------------------------------
         */
        Schema::create('candidate_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('cv')->nullable();
            $table->string('medical_check')->nullable();
            $table->string('police_clearance')->nullable();
            $table->json('qualifications')->nullable();
            $table->json('other_files')->nullable();
            $table->timestamps();
        });
        // ⚠ job_user is *not* created here because jobs table doesn’t exist yet.
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_profiles');
        Schema::dropIfExists('employer_user');
        Schema::dropIfExists('employers');
    }
};
