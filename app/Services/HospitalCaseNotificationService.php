<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\HospitalCase;
use App\Models\HospitalCaseNotification;
use App\Models\User;

class HospitalCaseNotificationService
{
    public function notifyVetHeads(HospitalCase $hospitalCase, string $title, string $message): void
    {
        $vetHeads = User::query()
            ->where('role', UserRole::VetHead->value)
            ->where('status', 'active')
            ->get();

        foreach ($vetHeads as $vetHead) {
            HospitalCaseNotification::create([
                'user_id' => $vetHead->id,
                'hospital_case_id' => $hospitalCase->id,
                'title' => $title,
                'message' => $message,
            ]);
        }
    }

    public function notifyPendingDischarge(HospitalCase $hospitalCase, User $vet): void
    {
        $hospitalCase->loadMissing('animal');
        $animal = $hospitalCase->animal;
        $label = $animal?->code ?? '—';

        $this->notifyVetHeads(
            $hospitalCase,
            'طلب اعتماد خروج — '.$hospitalCase->case_number,
            "سجّل د. {$vet->name} أن الحيوان {$label} جاهز للخروج ويحتاج اعتماد رئيس القسم.",
        );
    }

    public function notifyPendingSlaughter(HospitalCase $hospitalCase, User $vet): void
    {
        $hospitalCase->loadMissing('animal');
        $animal = $hospitalCase->animal;
        $label = $animal?->code ?? '—';

        $this->notifyVetHeads(
            $hospitalCase,
            'طلب اعتماد ذبح — '.$hospitalCase->case_number,
            "سجّل د. {$vet->name} أن الحيوان {$label} لا يستجيب للعلاج ويحتاج مراجعة ذبح اضطراري.",
        );
    }

    public function markAsReadForUser(HospitalCase $hospitalCase, User $user): void
    {
        HospitalCaseNotification::query()
            ->where('user_id', $user->id)
            ->where('hospital_case_id', $hospitalCase->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}
