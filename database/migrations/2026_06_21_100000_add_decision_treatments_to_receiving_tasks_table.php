<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('receiving_tasks', function (Blueprint $table) {
            $table->json('decision_treatments')->nullable()->after('decision_notes');
        });
    }

    public function down(): void
    {
        Schema::table('receiving_tasks', function (Blueprint $table) {
            $table->dropColumn('decision_treatments');
        });
    }
};
