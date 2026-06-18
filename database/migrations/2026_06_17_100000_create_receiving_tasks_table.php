<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receiving_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('task_number')->unique();
            $table->foreignId('animal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quarantine_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('supervisor_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->string('task_type');
            $table->string('source');
            $table->date('decision_date');
            $table->foreignId('decision_issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('decision_notes')->nullable();
            $table->string('delay_reason')->nullable();
            $table->text('delay_extra_note')->nullable();
            $table->timestamp('delay_recorded_at')->nullable();
            $table->text('receipt_note')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receiving_tasks');
    }
};
