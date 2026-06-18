<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quarantines', function (Blueprint $table) {
            $table->id();
            $table->string('case_number')->unique();
            $table->foreignId('animal_id')->constrained()->cascadeOnDelete();
            $table->string('reason');
            $table->string('initial_health_status');
            $table->string('status')->default('under_followup');
            $table->date('entry_date');
            $table->date('released_at')->nullable();
            $table->date('closed_at')->nullable();
            $table->string('close_reason')->nullable();
            $table->text('initial_notes')->nullable();
            $table->foreignId('responsible_vet_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quarantines');
    }
};
