<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('map_path_nodes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->decimal('x', 8, 7);
            $table->decimal('y', 8, 7);
            $table->foreignId('map_location_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('map_path_edges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_node_id')->constrained('map_path_nodes')->cascadeOnDelete();
            $table->foreignId('to_node_id')->constrained('map_path_nodes')->cascadeOnDelete();
            $table->unsignedInteger('distance_meters');
            $table->timestamps();

            $table->unique(['from_node_id', 'to_node_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('map_path_edges');
        Schema::dropIfExists('map_path_nodes');
    }
};
