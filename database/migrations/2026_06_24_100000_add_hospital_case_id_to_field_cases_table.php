<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('field_cases', 'hospital_case_id')) {
            return;
        }

        Schema::table('field_cases', function (Blueprint $table) {
            $table->foreignId('hospital_case_id')
                ->nullable()
                ->after('health_report_id')
                ->constrained('hospital_cases')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('field_cases', 'hospital_case_id')) {
            return;
        }

        Schema::table('field_cases', function (Blueprint $table) {
            $table->dropConstrainedForeignId('hospital_case_id');
        });
    }
};
