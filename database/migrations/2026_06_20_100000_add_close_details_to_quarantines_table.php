<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quarantines', function (Blueprint $table) {
            $table->text('close_notes')->nullable()->after('close_reason');
            $table->string('close_documentation_path')->nullable()->after('close_notes');
        });
    }

    public function down(): void
    {
        Schema::table('quarantines', function (Blueprint $table) {
            $table->dropColumn(['close_notes', 'close_documentation_path']);
        });
    }
};
