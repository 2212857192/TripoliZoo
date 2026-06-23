<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('autopsy_referrals', function (Blueprint $table) {
            $table->id();
            $table->string('referral_number')->unique();
            $table->foreignId('mortality_case_id')->constrained()->cascadeOnDelete();
            $table->foreignId('animal_id')->nullable()->constrained()->nullOnDelete();
            $table->string('group');
            $table->string('status')->default('pending');
            $table->foreignId('referred_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('referred_at');
            $table->text('transfer_reason')->nullable();
            $table->foreignId('documented_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('documented_at')->nullable();
            $table->text('final_death_cause')->nullable();
            $table->text('autopsy_notes')->nullable();
            $table->string('report_path')->nullable();
            $table->timestamps();
        });

        Schema::create('autopsy_referral_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('autopsy_referral_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('message');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'autopsy_referral_id'], 'autopsy_ref_notif_user_ref_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('autopsy_referral_notifications');
        Schema::dropIfExists('autopsy_referrals');
    }
};
