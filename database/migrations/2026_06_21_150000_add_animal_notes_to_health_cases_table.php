<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('health_cases', function (Blueprint $table) {
            $table->text('animal_notes')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('health_cases', function (Blueprint $table) {
            $table->dropColumn('animal_notes');
        });
    }
};
