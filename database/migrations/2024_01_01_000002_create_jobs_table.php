<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained()->onDelete('cascade');
            
            // Basic job info
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('assignment_overview')->nullable();
            
            // Location
            $table->string('location')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            
            // Compensation & Requirements
            $table->decimal('salary', 10, 2)->nullable();
            $table->string('experience')->nullable(); // entry, intermediate, senior
            $table->string('category')->nullable(); // IT, Finance, Marketing, etc.
            
            // Management & Tracking
            $table->date('date_posted')->nullable();
            $table->string('managed_by')->nullable();
            
            // Assets
            $table->string('company_logo')->nullable();
            $table->string('campaign_documents')->nullable();
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};