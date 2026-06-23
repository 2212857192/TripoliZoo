<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('treatment_referrals', 'reviewed_by')) {
            Schema::table('treatment_referrals', function (Blueprint $table) {
                $table->foreignId('reviewed_by')->nullable()->after('referred_at')->constrained('users')->nullOnDelete();
                $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
                $table->text('rejection_reason')->nullable()->after('reviewed_at');
            });
        }

        if (! Schema::hasTable('treatment_referral_notifications')) {
            Schema::create('treatment_referral_notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('treatment_referral_id')->constrained()->cascadeOnDelete();
                $table->string('title');
                $table->text('message');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'treatment_referral_id'], 'treat_ref_notif_user_ref_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('treatment_referral_notifications');

        Schema::table('treatment_referrals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropColumn(['reviewed_at', 'rejection_reason']);
        });
    }
};
