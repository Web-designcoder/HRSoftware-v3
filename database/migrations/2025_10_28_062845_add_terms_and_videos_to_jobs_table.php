<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('jobs', function (Blueprint $table) {
            $table->longText('terms_candidate')->nullable()->after('campaign_documents');
            $table->longText('terms_employer')->nullable()->after('terms_candidate');
            $table->string('employer_intro_video')->nullable()->after('terms_employer');
            $table->string('candidate_assessment_video')->nullable()->after('employer_intro_video');
            $table->string('status')->nullable()->after('candidate_assessment_video');
            if (!Schema::hasColumn('jobs', 'primary_contact_id')) {
                $table->unsignedBigInteger('primary_contact_id')->nullable()->after('status');
            }
        });
    }

    public function down(): void {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn([
                'terms_candidate',
                'terms_employer',
                'employer_intro_video',
                'candidate_assessment_video',
                'status',
                'primary_contact_id',
            ]);
        });
    }
};
