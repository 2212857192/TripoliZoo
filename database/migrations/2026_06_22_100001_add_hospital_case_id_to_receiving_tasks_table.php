<?php

use App\Enums\HospitalCaseStatus;
use App\Enums\ReceivingTaskSource;
use App\Enums\ReceivingTaskStatus;
use App\Models\HospitalCase;
use App\Models\ReceivingTask;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('hospital_cases')) {
            return;
        }

        if (! Schema::hasColumn('receiving_tasks', 'hospital_case_id')) {
            Schema::table('receiving_tasks', function (Blueprint $table) {
                $table->foreignId('hospital_case_id')
                    ->nullable()
                    ->after('quarantine_id')
                    ->constrained('hospital_cases')
                    ->nullOnDelete();
            });
        }

        $this->linkHospitalTasks();
        $this->closeReceivedHospitalCases();
    }

    public function down(): void
    {
        if (Schema::hasColumn('receiving_tasks', 'hospital_case_id')) {
            Schema::table('receiving_tasks', function (Blueprint $table) {
                $table->dropConstrainedForeignId('hospital_case_id');
            });
        }
    }

    private function linkHospitalTasks(): void
    {
        ReceivingTask::query()
            ->where('source', ReceivingTaskSource::Hospital)
            ->whereNull('hospital_case_id')
            ->orderBy('id')
            ->each(function (ReceivingTask $task) {
                $case = HospitalCase::query()
                    ->where('animal_id', $task->animal_id)
                    ->orderByDesc('id')
                    ->first();

                if ($case) {
                    $task->update(['hospital_case_id' => $case->id]);
                }
            });
    }

    private function closeReceivedHospitalCases(): void
    {
        $openStatuses = array_map(
            fn (HospitalCaseStatus $status) => $status->value,
            [
                HospitalCaseStatus::PendingHandover,
                HospitalCaseStatus::HandoverDelayed,
                HospitalCaseStatus::ReadyForDischarge,
            ],
        );

        ReceivingTask::query()
            ->with('hospitalCase')
            ->where('source', ReceivingTaskSource::Hospital)
            ->where('status', ReceivingTaskStatus::Received)
            ->orderBy('id')
            ->each(function (ReceivingTask $task) use ($openStatuses) {
                $case = $task->hospitalCase;

                if (! $case) {
                    $case = HospitalCase::query()
                        ->where('animal_id', $task->animal_id)
                        ->whereIn('status', $openStatuses)
                        ->orderByDesc('id')
                        ->first();
                }

                if (! $case || ! in_array($case->status->value, $openStatuses, true)) {
                    return;
                }

                $case->update([
                    'status' => HospitalCaseStatus::Discharged,
                    'closed_at' => $task->received_at ?? now(),
                    'closing_outcome' => $task->receipt_note
                        ? trim($task->receipt_note)
                        : 'تم استلام الحيوان في المجموعة.',
                ]);
            });
    }
};
