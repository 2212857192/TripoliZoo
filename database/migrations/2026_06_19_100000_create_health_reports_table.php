<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('health_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_number')->unique();
            $table->foreignId('animal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supervisor_id')->constrained('users')->cascadeOnDelete();
            $table->string('group');
            $table->text('description');
            $table->boolean('is_urgent')->default(false);
            $table->boolean('has_attachment')->default(false);
            $table->string('attachment_path')->nullable();
            $table->string('status')->default('sent');
            $table->foreignId('assigned_vet_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('doctor_note')->nullable();
            $table->timestamp('doctor_updated_at')->nullable();
            $table->boolean('field_case_opened')->default(false);
            $table->timestamps();
        });

        Schema::create('health_report_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('health_report_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('message');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'health_report_id'], 'health_rep_notif_user_rep_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_report_notifications');
        Schema::dropIfExists('health_reports');
    }
};
