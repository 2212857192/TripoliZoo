<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('map_path_edges', function (Blueprint $table) {
            $table->string('edge_key', 16)->nullable()->after('id');
            $table->boolean('is_active')->default(true)->after('geometry');
            $table->boolean('is_accessible')->default(true)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('map_path_edges', function (Blueprint $table) {
            $table->dropColumn(['edge_key', 'is_active', 'is_accessible']);
        });
    }
};
