<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('field_cases', function (Blueprint $table) {
            $table->id();
            $table->string('case_number')->unique();
            $table->foreignId('animal_id')->constrained()->cascadeOnDelete();
            $table->string('group');
            $table->text('open_reason');
            $table->text('initial_note')->nullable();
            $table->string('status')->default('active');
            $table->foreignId('opened_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('health_report_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('field_cases');
    }
};
