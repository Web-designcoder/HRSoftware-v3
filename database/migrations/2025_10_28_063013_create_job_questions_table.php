<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('job_contacts')) {
            Schema::create('job_contacts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('job_id');
                $table->unsignedBigInteger('user_id');
                $table->timestamps();

                $table->unique(['job_id', 'user_id']);
                $table->foreign('job_id')->references('id')->on('jobs')->cascadeOnDelete();
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('job_contacts');
    }
};
