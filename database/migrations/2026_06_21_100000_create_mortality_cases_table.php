<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mortality_cases', function (Blueprint $table) {
            $table->id();
            $table->string('case_number')->unique();
            $table->foreignId('animal_id')->nullable()->constrained()->nullOnDelete();
            $table->string('subject_code');
            $table->string('subject_type')->nullable();
            $table->foreignId('supervisor_id')->constrained('users')->cascadeOnDelete();
            $table->string('group');
            $table->string('victim_kind');
            $table->text('death_cause')->nullable();
            $table->text('notes')->nullable();
            $table->date('death_date');
            $table->boolean('has_attachment')->default(false);
            $table->string('attachment_path')->nullable();
            $table->string('status')->default('new');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('autopsy_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('mortality_case_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mortality_case_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('message');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'mortality_case_id'], 'mort_case_notif_user_case_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mortality_case_notifications');
        Schema::dropIfExists('mortality_cases');
    }
};
