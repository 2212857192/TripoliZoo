<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('animal_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('animal_id')->unique()->constrained()->cascadeOnDelete();
            $table->text('description');
            $table->string('image_path')->nullable();
            $table->string('scientific_name')->nullable();
            $table->string('display_code')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('animal_profiles');
    }
};
