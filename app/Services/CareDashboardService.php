<?php

namespace App\Services;

use App\Enums\AutopsyReferralStatus;
use App\Enums\HealthCaseFollowUpKind;
use App\Enums\HealthCaseStatus;
use App\Enums\MortalityCaseStatus;
use App\Enums\OperationalNoteStatus;
use App\Enums\ReceivingTaskType;
use App\Enums\TreatmentReferralStatus;
use App\Models\Animal;
use App\Models\AutopsyReferral;
use App\Models\CareNotification;
use App\Models\HealthCase;
use App\Models\HealthCaseNotification;
use App\Models\MortalityCase;
use App\Models\OperationalNote;
use App\Models\OperationalNoteNotification;
use App\Models\ReceivingTask;
use App\Models\TreatmentReferral;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CareDashboardService
{
    public function __construct(
        private PortalDashboardService $portalDashboard,
    ) {}

    /** @return array<string, int> */
    public function stats(): array
    {
        return [
            'new_health_cases' => HealthCase::query()
                ->where('status', HealthCaseStatus::New)
                ->count(),
            'health_cases_needing_referral' => HealthCase::query()
                ->where('status', HealthCaseStatus::New)
                ->where('follow_up_kind', HealthCaseFollowUpKind::NeedsReferral)
                ->count(),
            'new_mortality_cases' => MortalityCase::query()
                ->where('status', MortalityCaseStatus::New)
                ->count(),
            'births_under_follow_up' => Animal::underBirthFollowUp()->count(),
            'new_operational_notes' => OperationalNote::query()
                ->where('status', OperationalNoteStatus::New)
                ->count(),
        ];
    }

    /** @return Collection<int, array<string, mixed>> */
    public function reviewItems(int $limit = 8): Collection
    {
        $items = collect();

        HealthCase::query()
            ->with('animal')
            ->where('status', HealthCaseStatus::New)
            ->where('follow_up_kind', HealthCaseFollowUpKind::NeedsReferral)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->each(function (HealthCase $case) use ($items) {
                $items->push([
                    'sort_at' => $case->created_at,
                    'type' => 'health',
                    'type_label' => 'حالة صحية',
                    'badge_class' => 'badge-health',
                    'animal_name' => $case->animal?->name ?: $case->animal?->species,
                    'animal_code' => $case->animal?->code,
                    'group' => $case->group,
                    'description' => Str::limit($case->description, 80),
                    'date' => $case->created_at?->format('Y-m-d'),
                    'url' => '/care/health?case='.$case->case_number,
                ]);
            });

        MortalityCase::query()
            ->with('animal')
            ->where('status', MortalityCaseStatus::New)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->each(function (MortalityCase $case) use ($items) {
                $items->push([
                    'sort_at' => $case->created_at,
                    'type' => 'mortality',
                    'type_label' => 'حالة نفوق',
                    'badge_class' => 'badge-mortality',
                    'animal_name' => $case->animal?->name ?: $case->animal?->species,
                    'animal_code' => $case->animal?->code,
                    'group' => $case->group,
                    'description' => Str::limit($case->death_cause ?: $case->notes ?: 'حالة نفوق جديدة', 80),
                    'date' => $case->created_at?->format('Y-m-d'),
                    'url' => '/care/mortality?case='.$case->case_number,
                ]);
            });

        OperationalNote::query()
            ->where('status', OperationalNoteStatus::New)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->each(function (OperationalNote $note) use ($items) {
                $items->push([
                    'sort_at' => $note->created_at,
                    'type' => 'note',
                    'type_label' => 'ملاحظة تشغيلية',
                    'badge_class' => 'badge-note',
                    'animal_name' => null,
                    'animal_code' => null,
                    'group' => $note->group,
                    'description' => Str::limit($note->summary ?: $note->details, 80),
                    'date' => $note->noted_at?->format('Y-m-d') ?? $note->created_at?->format('Y-m-d'),
                    'url' => '/care/notes?note='.$note->note_number,
                ]);
            });

        $nearFollowUpEnd = now()->subDays(BirthRegistrationService::FOLLOW_UP_DAYS - 5)->toDateString();

        Animal::underBirthFollowUp()
            ->with('birthRegistration')
            ->whereHas('birthRegistration', fn ($query) => $query->whereDate('birth_date', '<=', $nearFollowUpEnd))
            ->orderByDesc('registered_at')
            ->limit($limit)
            ->get()
            ->each(function (Animal $animal) use ($items) {
                $items->push([
                    'sort_at' => $animal->registered_at ?? $animal->created_at,
                    'type' => 'birth',
                    'type_label' => 'مولود',
                    'badge_class' => 'badge-birth',
                    'animal_name' => $animal->name ?: $animal->species,
                    'animal_code' => $animal->code,
                    'group' => $animal->group,
                    'description' => 'قريب من إكمال مدة المتابعة الأولية',
                    'date' => $animal->birthRegistration?->birth_date?->format('Y-m-d')
                        ?? $animal->registered_at?->format('Y-m-d'),
                    'url' => '/care/births?animal='.$animal->code,
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
                'url' => '/care/referrals/treatment',
            ],
            [
                'label' => 'إحالات العلاج',
                'status_label' => TreatmentReferralStatus::Rejected->label(),
                'badge_class' => 'badge-rejected',
                'count' => TreatmentReferral::query()->where('status', TreatmentReferralStatus::Rejected)->count(),
                'url' => '/care/referrals/treatment',
            ],
            [
                'label' => 'إحالات التشريح',
                'status_label' => AutopsyReferralStatus::Pending->label(),
                'badge_class' => 'badge-pending',
                'count' => AutopsyReferral::query()->where('status', AutopsyReferralStatus::Pending)->count(),
                'url' => '/care/referrals/autopsy',
            ],
            [
                'label' => 'إحالات التشريح',
                'status_label' => AutopsyReferralStatus::Documented->label(),
                'badge_class' => 'badge-approved',
                'count' => AutopsyReferral::query()->where('status', AutopsyReferralStatus::Documented)->count(),
                'url' => '/care/referrals/autopsy',
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
                'url' => '/care/decisions/'.$task->task_number,
                'icon_style' => 'color: #d97706; background: #fffbeb; border-color: #fde68a;',
                'icon_svg' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
                'text' => 'تعذر استلام الحيوان '.($animal?->code ?? '—').' مؤقتًا',
            ]);
        }

        TreatmentReferral::query()
            ->with('animal')
            ->where('status', TreatmentReferralStatus::Rejected)
            ->orderByDesc('updated_at')
            ->limit(3)
            ->get()
            ->each(function (TreatmentReferral $referral) use ($alerts) {
                $code = $referral->animal?->code ?? '—';
                $alerts->push([
                    'at' => $referral->updated_at,
                    'url' => '/care/referrals/treatment?referral='.$referral->referral_number,
                    'icon_style' => 'color: #e11d48; background: #fff1f2; border-color: #fecdd3;',
                    'icon_svg' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>',
                    'text' => 'تم رفض إحالة علاج للحيوان '.$code,
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
                    'url' => '/care/referrals/autopsy/'.$referral->referral_number,
                    'icon_style' => 'color: #15803d; background: #f0fdf4; border-color: #bbf7d0;',
                    'icon_svg' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
                    'text' => 'تم توثيق نتيجة تشريح للحيوان '.$code,
                ]);
            });

        ReceivingTask::query()
            ->with('animal')
            ->orderByDesc('decision_date')
            ->orderByDesc('id')
            ->limit(3)
            ->get()
            ->each(function (ReceivingTask $task) use ($alerts) {
                $code = $task->animal?->code ?? '—';
                $label = $task->task_type === ReceivingTaskType::AfterHealthRelease
                    ? 'صدر قرار إفراج صحي للحيوان '.$code
                    : 'صدر قرار طبي للحيوان '.$code;

                $alerts->push([
                    'at' => Carbon::parse($task->decision_date)->endOfDay(),
                    'url' => '/care/decisions/'.$task->task_number,
                    'icon_style' => 'color: #2563eb; background: #eff6ff; border-color: #bfdbfe;',
                    'icon_svg' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>',
                    'text' => $label,
                ]);
            });

        if ($user) {
            CareNotification::query()
                ->where('user_id', $user->id)
                ->latest()
                ->limit(3)
                ->get()
                ->each(function (CareNotification $notification) use ($alerts) {
                    $taskNumber = $notification->receivingTask?->task_number;
                    $alerts->push([
                        'at' => $notification->created_at,
                        'url' => $taskNumber ? '/care/decisions/'.$taskNumber : '/care/decisions',
                        'icon_style' => 'color: #2563eb; background: #eff6ff; border-color: #bfdbfe;',
                        'icon_svg' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>',
                        'text' => $notification->title,
                    ]);
                });

            HealthCaseNotification::query()
                ->where('user_id', $user->id)
                ->latest()
                ->limit(2)
                ->get()
                ->each(function (HealthCaseNotification $notification) use ($alerts) {
                    $caseNumber = $notification->healthCase?->case_number;
                    $alerts->push([
                        'at' => $notification->created_at,
                        'url' => $caseNumber ? '/care/health?case='.$caseNumber : '/care/health',
                        'icon_style' => 'color: #2563eb; background: #eff6ff; border-color: #bfdbfe;',
                        'icon_svg' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>',
                        'text' => $notification->title,
                    ]);
                });

            OperationalNoteNotification::query()
                ->where('user_id', $user->id)
                ->latest()
                ->limit(2)
                ->get()
                ->each(function (OperationalNoteNotification $notification) use ($alerts) {
                    $noteNumber = $notification->operationalNote?->note_number;
                    $alerts->push([
                        'at' => $notification->created_at,
                        'url' => $noteNumber ? '/care/notes?note='.$noteNumber : '/care/notes',
                        'icon_style' => 'color: #ea580c; background: #fff7ed; border-color: #fed7aa;',
                        'icon_svg' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>',
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
