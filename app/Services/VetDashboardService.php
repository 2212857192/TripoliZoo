<?php

namespace App\Services;

use App\Enums\AutopsyReferralStatus;
use App\Enums\HospitalCaseStatus;
use App\Enums\QuarantineStatus;
use App\Enums\ReceivingTaskStatus;
use App\Enums\TreatmentReferralStatus;
use App\Models\AutopsyReferral;
use App\Models\AutopsyReferralNotification;
use App\Models\HospitalCase;
use App\Models\HospitalCaseNotification;
use App\Models\Quarantine;
use App\Models\ReceivingTask;
use App\Models\TreatmentReferral;
use App\Models\TreatmentReferralNotification;
use App\Models\User;
use App\Models\VetNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class VetDashboardService
{
    public function __construct(
        private PortalDashboardService $portalDashboard,
    ) {}

    /** @return array<string, int> */
    public function stats(): array
    {
        $activeHospitalValues = array_map(
            fn (HospitalCaseStatus $status) => $status->value,
            HospitalCaseStatus::active(),
        );

        return [
            'treatment_referrals_pending' => TreatmentReferral::query()
                ->where('status', TreatmentReferralStatus::Pending)
                ->count(),
            'treatment_referrals_needing_followup' => TreatmentReferral::query()
                ->where('status', TreatmentReferralStatus::Approved)
                ->whereHas('hospitalCase', fn ($query) => $query->whereIn('status', $activeHospitalValues))
                ->count(),
            'autopsy_referrals_pending' => AutopsyReferral::query()
                ->where('status', AutopsyReferralStatus::Pending)
                ->count(),
            'hospital_cases_active' => HospitalCase::query()
                ->whereIn('status', $activeHospitalValues)
                ->count(),
            'quarantine_under_followup' => Quarantine::query()
                ->where('status', QuarantineStatus::UnderFollowUp)
                ->count(),
        ];
    }

    /** @return Collection<int, array<string, mixed>> */
    public function recentReferrals(int $limit = 8): Collection
    {
        $items = collect();

        TreatmentReferral::query()
            ->with('animal')
            ->orderByDesc('referred_at')
            ->limit($limit)
            ->get()
            ->each(function (TreatmentReferral $referral) use ($items) {
                $animal = $referral->animal;
                $items->push([
                    'sort_at' => $referral->referred_at ?? $referral->created_at,
                    'type_label' => 'إحالة علاج',
                    'badge_class' => 'badge-treatment',
                    'animal_name' => $animal?->displayLabel(),
                    'animal_code' => $animal?->code,
                    'group' => $referral->group,
                    'date' => $referral->referred_at?->format('Y-m-d'),
                    'status_label' => $referral->status->label(),
                    'status_badge_class' => match ($referral->status) {
                        TreatmentReferralStatus::Pending => 'badge-pending',
                        TreatmentReferralStatus::Approved => 'badge-approved',
                        TreatmentReferralStatus::Rejected => 'badge-rejected',
                    },
                    'url' => '/vet/referrals/treatment?referral='.$referral->referral_number,
                ]);
            });

        AutopsyReferral::query()
            ->with('animal')
            ->orderByDesc('referred_at')
            ->limit($limit)
            ->get()
            ->each(function (AutopsyReferral $referral) use ($items) {
                $animal = $referral->animal;
                $items->push([
                    'sort_at' => $referral->referred_at ?? $referral->created_at,
                    'type_label' => 'إحالة تشريح',
                    'badge_class' => 'badge-autopsy',
                    'animal_name' => $animal?->displayLabel(),
                    'animal_code' => $animal?->code,
                    'group' => $referral->group,
                    'date' => $referral->referred_at?->format('Y-m-d'),
                    'status_label' => $referral->status === AutopsyReferralStatus::Pending
                        ? 'انتظار التقرير'
                        : $referral->status->label(),
                    'status_badge_class' => $referral->status === AutopsyReferralStatus::Pending
                        ? 'badge-pending'
                        : 'badge-approved',
                    'url' => '/vet/referrals/autopsy/'.$referral->referral_number,
                ]);
            });

        return $items
            ->sortByDesc(fn (array $item) => $item['sort_at'] ?? now())
            ->take($limit)
            ->values();
    }

    /** @return Collection<int, array<string, mixed>> */
    public function urgentCases(int $limit = 8): Collection
    {
        $items = collect();

        HospitalCase::query()
            ->with('animal')
            ->whereIn('status', [
                HospitalCaseStatus::PendingHandover,
                HospitalCaseStatus::PendingDischargeApproval,
                HospitalCaseStatus::PendingSlaughterApproval,
                HospitalCaseStatus::HandoverDelayed,
            ])
            ->orderByDesc('admitted_at')
            ->limit($limit)
            ->get()
            ->each(function (HospitalCase $case) use ($items) {
                $animal = $case->animal;
                $items->push([
                    'sort_at' => $case->updated_at ?? $case->admitted_at,
                    'type_label' => 'حالة مستشفى',
                    'badge_class' => 'badge-hospital',
                    'animal_name' => $animal?->displayLabel(),
                    'animal_code' => $animal?->code,
                    'situation' => Str::limit($case->chief_complaint, 80),
                    'date' => $case->admitted_at?->format('Y-m-d'),
                    'action_label' => match ($case->status) {
                        HospitalCaseStatus::PendingHandover => 'بانتظار استلام المشرف',
                        HospitalCaseStatus::PendingDischargeApproval,
                        HospitalCaseStatus::PendingSlaughterApproval => 'قيد مراجعة رئيس القسم',
                        HospitalCaseStatus::HandoverDelayed => 'تعذر الاستلام مؤقتاً',
                        default => $case->status->label(),
                    },
                    'action_badge_class' => match ($case->status) {
                        HospitalCaseStatus::PendingHandover => 'badge-handover',
                        HospitalCaseStatus::HandoverDelayed => 'badge-pending',
                        default => 'badge-review',
                    },
                    'url' => '/vet/cases/hospital/'.$case->case_number,
                ]);
            });

        TreatmentReferral::query()
            ->with(['animal', 'healthCase'])
            ->where('status', TreatmentReferralStatus::Pending)
            ->orderByDesc('referred_at')
            ->limit($limit)
            ->get()
            ->each(function (TreatmentReferral $referral) use ($items) {
                $animal = $referral->animal;
                $items->push([
                    'sort_at' => $referral->referred_at ?? $referral->created_at,
                    'type_label' => 'إحالة علاج',
                    'badge_class' => 'badge-treatment',
                    'animal_name' => $animal?->displayLabel(),
                    'animal_code' => $animal?->code,
                    'situation' => Str::limit($referral->healthCase?->description ?? 'إحالة علاج بانتظار المراجعة', 80),
                    'date' => $referral->referred_at?->format('Y-m-d'),
                    'action_label' => 'قيد مراجعة رئيس القسم',
                    'action_badge_class' => 'badge-review',
                    'url' => '/vet/referrals/treatment?referral='.$referral->referral_number,
                ]);
            });

        AutopsyReferral::query()
            ->with(['animal', 'mortalityCase'])
            ->where('status', AutopsyReferralStatus::Pending)
            ->orderByDesc('referred_at')
            ->limit($limit)
            ->get()
            ->each(function (AutopsyReferral $referral) use ($items) {
                $animal = $referral->animal;
                $items->push([
                    'sort_at' => $referral->referred_at ?? $referral->created_at,
                    'type_label' => 'إحالة تشريح',
                    'badge_class' => 'badge-autopsy',
                    'animal_name' => $animal?->displayLabel(),
                    'animal_code' => $animal?->code,
                    'situation' => Str::limit(
                        $referral->transfer_reason ?? $referral->mortalityCase?->death_cause ?? 'بانتظار إجراء التشريح وتوثيق التقرير',
                        80,
                    ),
                    'date' => $referral->referred_at?->format('Y-m-d'),
                    'action_label' => 'بانتظار التوثيق',
                    'action_badge_class' => 'badge-pending',
                    'url' => '/vet/referrals/autopsy/'.$referral->referral_number,
                ]);
            });

        Quarantine::query()
            ->with(['animal', 'vaccines'])
            ->where('status', QuarantineStatus::UnderFollowUp)
            ->whereHas('vaccines')
            ->orderByDesc('entry_date')
            ->limit($limit)
            ->get()
            ->each(function (Quarantine $quarantine) use ($items) {
                $animal = $quarantine->animal;
                $items->push([
                    'sort_at' => $quarantine->updated_at ?? Carbon::parse($quarantine->entry_date),
                    'type_label' => 'حجر صحي',
                    'badge_class' => 'badge-quarantine',
                    'animal_name' => $animal?->displayLabel(),
                    'animal_code' => $animal?->code,
                    'situation' => Str::limit($quarantine->initial_health_status, 80),
                    'date' => $quarantine->entry_date?->format('Y-m-d'),
                    'action_label' => 'جاهز للإفراج الصحي',
                    'action_badge_class' => 'badge-ready',
                    'url' => '/vet/quarantine?open='.$quarantine->case_number,
                ]);
            });

        return $items
            ->sortByDesc(fn (array $item) => $item['sort_at'] ?? now())
            ->take($limit)
            ->values();
    }

    /** @return list<array<string, mixed>> */
    public function referralSummary(): array
    {
        return [
            [
                'label' => 'إحالات العلاج',
                'status_label' => TreatmentReferralStatus::Pending->label(),
                'badge_class' => 'badge-pending',
                'count' => TreatmentReferral::query()->where('status', TreatmentReferralStatus::Pending)->count(),
                'url' => '/vet/referrals/treatment',
            ],
            [
                'label' => 'إحالات العلاج',
                'status_label' => TreatmentReferralStatus::Approved->label(),
                'badge_class' => 'badge-approved',
                'count' => TreatmentReferral::query()->where('status', TreatmentReferralStatus::Approved)->count(),
                'url' => '/vet/referrals/treatment',
            ],
            [
                'label' => 'إحالات التشريح',
                'status_label' => AutopsyReferralStatus::Pending->label(),
                'badge_class' => 'badge-pending',
                'count' => AutopsyReferral::query()->where('status', AutopsyReferralStatus::Pending)->count(),
                'url' => '/vet/referrals/autopsy',
            ],
            [
                'label' => 'إحالات التشريح',
                'status_label' => AutopsyReferralStatus::Documented->label(),
                'badge_class' => 'badge-approved',
                'count' => AutopsyReferral::query()->where('status', AutopsyReferralStatus::Documented)->count(),
                'url' => '/vet/referrals/autopsy',
            ],
        ];
    }

    /** @return Collection<int, array<string, mixed>> */
    public function recentAlerts(?User $user, int $limit = 6): Collection
    {
        $alerts = collect();

        foreach ($this->portalDashboard->recentReceivingDelays(3) as $task) {
            $animal = $task->animal;
            $alerts->push([
                'at' => $task->delay_recorded_at ?? $task->updated_at,
                'url' => '/vet/decisions/'.$task->task_number,
                'icon_style' => 'color: #d97706; background: #fffbeb; border-color: #fde68a;',
                'icon_svg' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
                'text' => 'تعذر استلام الحيوان '.($animal?->code ?? '—').' مؤقتًا من الرعاية',
            ]);
        }

        TreatmentReferral::query()
            ->with('animal')
            ->where('status', TreatmentReferralStatus::Rejected)
            ->orderByDesc('updated_at')
            ->limit(2)
            ->get()
            ->each(function (TreatmentReferral $referral) use ($alerts) {
                $code = $referral->animal?->code ?? '—';
                $alerts->push([
                    'at' => $referral->updated_at,
                    'url' => '/vet/referrals/treatment?referral='.$referral->referral_number,
                    'icon_style' => 'color: #e11d48; background: #fff1f2; border-color: #fecdd3;',
                    'icon_svg' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>',
                    'text' => 'إحالة علاج مرفوضة سابقاً تحتاج مراجعة — '.$code,
                ]);
            });

        AutopsyReferral::query()
            ->with('animal')
            ->where('status', AutopsyReferralStatus::Documented)
            ->orderByDesc('updated_at')
            ->limit(3)
            ->get()
            ->each(function (AutopsyReferral $referral) use ($alerts) {
                $code = $referral->animal?->code ?? '—';
                $alerts->push([
                    'at' => $referral->updated_at,
                    'url' => '/vet/referrals/autopsy/'.$referral->referral_number,
                    'icon_style' => 'color: #15803d; background: #f0fdf4; border-color: #bbf7d0;',
                    'icon_svg' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
                    'text' => 'تم توثيق نتيجة تشريح للحيوان '.$code,
                ]);
            });

        ReceivingTask::query()
            ->with('animal')
            ->where('status', '!=', ReceivingTaskStatus::Pending)
            ->orderByDesc('decision_date')
            ->orderByDesc('id')
            ->limit(3)
            ->get()
            ->each(function (ReceivingTask $task) use ($alerts) {
                $code = $task->animal?->code ?? '—';
                $alerts->push([
                    'at' => Carbon::parse($task->decision_date)->endOfDay(),
                    'url' => '/vet/decisions/'.$task->task_number,
                    'icon_style' => 'color: #2563eb; background: #eff6ff; border-color: #bfdbfe;',
                    'icon_svg' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>',
                    'text' => 'صدر قرار خروج بعد العلاج للحيوان '.$code,
                ]);
            });

        if ($user) {
            VetNotification::query()
                ->where('user_id', $user->id)
                ->latest()
                ->limit(3)
                ->get()
                ->each(function (VetNotification $notification) use ($alerts) {
                    $taskNumber = $notification->receivingTask?->task_number;
                    $alerts->push([
                        'at' => $notification->created_at,
                        'url' => $taskNumber ? '/vet/decisions/'.$taskNumber : '/vet/decisions',
                        'icon_style' => 'color: #2563eb; background: #eff6ff; border-color: #bfdbfe;',
                        'icon_svg' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>',
                        'text' => $notification->title,
                    ]);
                });

            TreatmentReferralNotification::query()
                ->where('user_id', $user->id)
                ->latest()
                ->limit(2)
                ->get()
                ->each(function (TreatmentReferralNotification $notification) use ($alerts) {
                    $referralNumber = $notification->treatmentReferral?->referral_number;
                    $alerts->push([
                        'at' => $notification->created_at,
                        'url' => $referralNumber
                            ? '/vet/referrals/treatment?referral='.$referralNumber
                            : '/vet/referrals/treatment',
                        'icon_style' => 'color: #2563eb; background: #eff6ff; border-color: #bfdbfe;',
                        'icon_svg' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>',
                        'text' => $notification->title,
                    ]);
                });

            AutopsyReferralNotification::query()
                ->where('user_id', $user->id)
                ->latest()
                ->limit(2)
                ->get()
                ->each(function (AutopsyReferralNotification $notification) use ($alerts) {
                    $referralNumber = $notification->autopsyReferral?->referral_number;
                    $alerts->push([
                        'at' => $notification->created_at,
                        'url' => $referralNumber
                            ? '/vet/referrals/autopsy/'.$referralNumber
                            : '/vet/referrals/autopsy',
                        'icon_style' => 'color: #dc2626; background: #fef2f2; border-color: #fecaca;',
                        'icon_svg' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><path d="M8 12h8"></path></svg>',
                        'text' => $notification->title,
                    ]);
                });

            HospitalCaseNotification::query()
                ->where('user_id', $user->id)
                ->latest()
                ->limit(2)
                ->get()
                ->each(function (HospitalCaseNotification $notification) use ($alerts) {
                    $caseNumber = $notification->hospitalCase?->case_number;
                    $alerts->push([
                        'at' => $notification->created_at,
                        'url' => $caseNumber ? '/vet/cases/hospital/'.$caseNumber : '/vet/cases/hospital',
                        'icon_style' => 'color: #16a34a; background: #f0fdf4; border-color: #bbf7d0;',
                        'icon_svg' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>',
                        'text' => $notification->title,
                    ]);
                });
        }

        return $alerts
            ->sortByDesc(fn (array $alert) => $alert['at'] ?? now())
            ->take($limit)
            ->values();
    }
}
