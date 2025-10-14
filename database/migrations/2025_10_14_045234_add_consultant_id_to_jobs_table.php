<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('jobs', function (Blueprint $table) {
            $table->foreignId('consultant_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->after('employer_id');
        });
    }

    public function down(): void {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('consultant_id');
        });
    }
};
