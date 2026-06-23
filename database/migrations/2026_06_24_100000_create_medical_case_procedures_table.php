<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('field_cases', 'closing_note')) {
            Schema::table('field_cases', function (Blueprint $table) {
                $table->text('closing_note')->nullable()->after('closed_at');
            });
        }

        if (! Schema::hasTable('medical_case_procedures')) {
            Schema::create('medical_case_procedures', function (Blueprint $table) {
                $table->id();
                $table->morphs('caseable');
                $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
                $table->text('diagnosis');
                $table->text('treatment');
                $table->text('note')->nullable();
                $table->string('case_result');
                $table->dateTime('recorded_at');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('medical_nutrition_recommendations')) {
            Schema::create('medical_nutrition_recommendations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('medical_case_procedure_id');
                $table->text('recommendation_text');
                $table->date('start_date');
                $table->date('end_date');
                $table->text('note')->nullable();
                $table->timestamps();

                $table->unique('medical_case_procedure_id', 'med_nutrition_proc_id_unique');
                $table->foreign('medical_case_procedure_id', 'med_nutrition_proc_fk')
                    ->references('id')
                    ->on('medical_case_procedures')
                    ->cascadeOnDelete();
            });
        }

        if (! Schema::hasTable('hospital_case_notifications')) {
            Schema::create('hospital_case_notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('hospital_case_id')->constrained()->cascadeOnDelete();
                $table->string('title');
                $table->text('message');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'hospital_case_id'], 'hosp_case_notif_user_case_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('hospital_case_notifications');
        Schema::dropIfExists('medical_nutrition_recommendations');
        Schema::dropIfExists('medical_case_procedures');

        if (Schema::hasColumn('field_cases', 'closing_note')) {
            Schema::table('field_cases', function (Blueprint $table) {
                $table->dropColumn('closing_note');
            });
        }
    }
};
