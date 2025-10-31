<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('job_questions', function (Blueprint $table) {
            if (!Schema::hasColumn('job_questions', 'heading')) {
                $table->string('heading')->nullable()->after('job_id');
            }
            if (Schema::hasColumn('job_questions', 'question') && !Schema::hasColumn('job_questions', 'body')) {
                $table->renameColumn('question', 'body');
            }
        });
    }

    public function down(): void {
        Schema::table('job_questions', function (Blueprint $table) {
            if (Schema::hasColumn('job_questions', 'heading')) {
                $table->dropColumn('heading');
            }
            if (Schema::hasColumn('job_questions', 'body') && !Schema::hasColumn('job_questions', 'question')) {
                $table->renameColumn('body', 'question');
            }
        });
    }
};
