<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('map_path_nodes', function (Blueprint $table) {
            $table->string('node_key', 16)->nullable()->unique()->after('id');
        });

        Schema::table('map_path_edges', function (Blueprint $table) {
            $table->json('geometry')->nullable()->after('distance_meters');
        });
    }

    public function down(): void
    {
        Schema::table('map_path_edges', function (Blueprint $table) {
            $table->dropColumn('geometry');
        });

        Schema::table('map_path_nodes', function (Blueprint $table) {
            $table->dropColumn('node_key');
        });
    }
};
