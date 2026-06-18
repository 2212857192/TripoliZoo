<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hospital_cases', function (Blueprint $table) {
            $table->id();
            $table->string('case_number')->unique();
            $table->foreignId('treatment_referral_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('health_case_id')->constrained()->cascadeOnDelete();
            $table->foreignId('animal_id')->constrained()->cascadeOnDelete();
            $table->string('group');
            $table->text('chief_complaint');
            $table->string('status')->default('under_treatment');
            $table->foreignId('admitted_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('admitted_at');
            $table->timestamp('closed_at')->nullable();
            $table->string('closing_outcome')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hospital_cases');
    }
};
