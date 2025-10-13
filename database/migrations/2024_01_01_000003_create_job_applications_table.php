<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Candidate snapshot (preserved even if user updates profile)
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('city')->nullable();
            $table->string('postcode')->nullable();

            // Uploads specific to this application
            $table->string('cv_path')->nullable();
            $table->string('video_intro')->nullable();

            // Assessment questions
            $table->text('attention_to_detail')->nullable();
            $table->text('customer_management')->nullable();
            $table->text('market_understanding')->nullable();
            $table->text('sales_and_business_development')->nullable();
            $table->text('ambition')->nullable();
            $table->text('leadership_skills')->nullable();
            $table->text('risk_assessment')->nullable();

            // Application status
            $table->enum('status', ['pending', 'reviewing', 'accepted', 'rejected', 'withdrawn'])
                ->default('pending');

            $table->timestamps();
            
            // Prevent duplicate applications
            $table->unique(['job_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};