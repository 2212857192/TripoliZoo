<?php

namespace App\Services;

use App\Enums\AnimalStatus;
use App\Enums\AutopsyReferralStatus;
use App\Enums\FieldCaseStatus;
use App\Enums\HealthCaseFollowUpKind;
use App\Enums\HealthCaseStatus;
use App\Enums\HealthReportStatus;
use App\Enums\HospitalCaseStatus;
use App\Enums\MortalityCaseStatus;
use App\Enums\QuarantineStatus;
use App\Enums\ReceivingTaskStatus;
use App\Enums\ReceivingTaskType;
use App\Enums\TreatmentReferralStatus;
use App\Models\AdminActivityLog;
use App\Models\Animal;
use App\Models\AutopsyReferral;
use App\Models\BirthRegistration;
use App\Models\FieldCase;
use App\Models\HealthCase;
use App\Models\HealthReport;
use App\Models\HospitalCase;
use App\Models\MedicalCaseProcedure;
use App\Models\MedicalNutritionRecommendation;
use App\Models\MortalityCase;
use App\Models\Quarantine;
use App\Models\ReceivingTask;
use App\Models\TicketSale;
use App\Models\TreatmentReferral;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DirectorDashboardService
{
    private const GROUP_COLORS = [
        '#1a4a2e', '#16a34a', '#22c55e', '#86efac', '#65a30d',
        '#4ade80', '#a3e635', '#bef264', '#15803d', '#14532d',
    ];

    /** @return array<string, mixed> */
    public function overviewStats(): array
    {
        $activeHospitalValues = array_map(
            fn (HospitalCaseStatus $status) => $status->value,
            HospitalCaseStatus::active(),
        );
        $awaitingDecisionValues = array_map(
            fn (HospitalCaseStatus $status) => $status->value,
            [
                ...HospitalCaseStatus::awaitingVetHeadDecision(),
                HospitalCaseStatus::ReadyForDischarge,
            ],
        );

        $animalsInZoo = Animal::query()->insideZooOfficially()->count();

        $birthsUnderFollowUp = Animal::underBirthFollowUp()->count();
        $newHealthCases = HealthCase::query()->where('status', HealthCaseStatus::New)->count();
        $healthNeedingReferral = HealthCase::query()
            ->where('status', HealthCaseStatus::New)
            ->where('follow_up_kind', HealthCaseFollowUpKind::NeedsReferral)
            ->count();

        $hospitalActive = HospitalCase::query()->whereIn('status', $activeHospitalValues)->count();
        $hospitalAwaitingDecision = HospitalCase::query()->whereIn('status', $awaitingDecisionValues)->count();

        $fieldActive = FieldCase::query()->where('status', FieldCaseStatus::Active)->count();
        $quarantineActive = Quarantine::query()->where('status', QuarantineStatus::UnderFollowUp)->count();
        $quarantineReady = Quarantine::query()
            ->where('status', QuarantineStatus::UnderFollowUp)
            ->whereHas('vaccines')
            ->count();

        $birthsNearCompletion = Animal::underBirthFollowUp()
            ->whereHas('birthRegistration', fn ($query) => $query->whereDate(
                'birth_date',
                '<=',
                now()->subDays(BirthRegistrationService::FOLLOW_UP_DAYS - 3)->toDateString(),
            ))
            ->count();

        $mortalityLast7Days = MortalityCase::query()
            ->where('death_date', '>=', now()->subDays(7)->toDateString())
            ->count();

        $mortalityPendingAutopsy = MortalityCase::query()
            ->whereHas('autopsyReferral', fn ($query) => $query->where('status', AutopsyReferralStatus::Pending))
            ->count();

        $slaughterThisMonth = HospitalCase::query()
            ->where('status', HospitalCaseStatus::Slaughtered)
            ->where('closed_at', '>=', now()->startOfMonth())
            ->count();

        $ticketsToday = (int) TicketSale::query()->whereDate('sold_at', today())->sum('quantity');

        return [
            'expected_visitors_today' => $ticketsToday,
            'total_animals_in_zoo' => $animalsInZoo,
            'births_under_follow_up' => $birthsUnderFollowUp,
            'new_health_cases' => $newHealthCases,
            'health_needing_referral' => $healthNeedingReferral,
            'hospital_cases_active' => $hospitalActive,
            'hospital_awaiting_decision' => $hospitalAwaitingDecision,
            'field_cases_active' => $fieldActive,
            'quarantine_active' => $quarantineActive,
            'quarantine_ready_release' => $quarantineReady,
            'new_births' => BirthRegistration::query()
                ->where('birth_date', '>=', now()->startOfMonth()->toDateString())
                ->count(),
            'births_near_completion' => $birthsNearCompletion,
            'mortality_last_7_days' => $mortalityLast7Days,
            'mortality_pending_autopsy' => $mortalityPendingAutopsy,
            'exited_animals' => 0,
            'exits_this_month' => 0,
            'slaughter_this_month' => $slaughterThisMonth,
        ];
    }

    /** @return array<string, int> */
    public function todaySummary(): array
    {
        return [
            'health_cases' => HealthCase::query()->whereDate('created_at', today())->count(),
            'health_reports' => HealthReport::query()->whereDate('created_at', today())->count(),
            'births' => BirthRegistration::query()->whereDate('birth_date', today())->count(),
            'mortality' => MortalityCase::query()->whereDate('death_date', today())->count(),
            'medical_procedures' => MedicalCaseProcedure::query()->whereDate('recorded_at', today())->count(),
            'nutrition_recommendations' => MedicalNutritionRecommendation::query()->whereDate('created_at', today())->count(),
            'receiving_tasks' => ReceivingTask::query()->whereDate('created_at', today())->count(),
            'tickets_sold' => (int) TicketSale::query()->whereDate('sold_at', today())->sum('quantity'),
        ];
    }

    /** @return array<string, mixed> */
    public function visits(): array
    {
        $ticketsToday = (int) TicketSale::query()->whereDate('sold_at', today())->sum('quantity');
        $ticketsYesterday = (int) TicketSale::query()->whereDate('sold_at', today()->subDay())->sum('quantity');
        $revenueToday = (float) TicketSale::query()->whereDate('sold_at', today())->sum('total_amount');

        $changePct = null;
        if ($ticketsYesterday > 0) {
            $changePct = (int) round((($ticketsToday - $ticketsYesterday) / $ticketsYesterday) * 100);
        }

        $ticketTypes = TicketSale::query()
            ->with('ticketType')
            ->whereDate('sold_at', today())
            ->get()
            ->groupBy(fn (TicketSale $sale) => $sale->ticketType?->name ?? 'أخرى')
            ->map(fn (Collection $sales, string $name) => [
                'name' => $name,
                'count' => (int) $sales->sum('quantity'),
            ])
            ->sortByDesc('count')
            ->values()
            ->all();

        $weekly = collect(range(6, 0))->map(function (int $daysAgo) {
            $date = today()->subDays($daysAgo);
            $count = (int) TicketSale::query()->whereDate('sold_at', $date)->sum('quantity');

            return [
                'label' => $this->arabicDayShort($date),
                'count' => $count,
            ];
        });

        $maxWeekly = max($weekly->max('count'), 1);

        return [
            'tickets_today' => $ticketsToday,
            'tickets_change_pct' => $changePct,
            'revenue_today' => $revenueToday,
            'ticket_types' => $ticketTypes,
            'weekly_chart' => $weekly
                ->map(fn (array $day) => array_merge($day, [
                    'height_pct' => (int) round(($day['count'] / $maxWeekly) * 100),
                ]))
                ->all(),
        ];
    }

    /** @return array<string, mixed> */
    public function operations(): array
    {
        return [
            'active_health_cases' => HealthCase::query()
                ->whereIn('status', [HealthCaseStatus::New, HealthCaseStatus::Reviewed])
                ->count(),
            'urgent_health_reports' => HealthReport::query()
                ->where('is_urgent', true)
                ->where('status', '!=', HealthReportStatus::Closed)
                ->count(),
            'pending_treatment_referrals' => TreatmentReferral::query()
                ->where('status', TreatmentReferralStatus::Pending)
                ->count(),
            'pending_receiving_tasks' => ReceivingTask::query()
                ->where('status', ReceivingTaskStatus::Pending)
                ->count(),
            'delayed_receiving_tasks' => ReceivingTask::query()
                ->where('status', ReceivingTaskStatus::TemporarilyUnable)
                ->count(),
            'slaughter_this_month' => HospitalCase::query()
                ->where('status', HospitalCaseStatus::Slaughtered)
                ->where('closed_at', '>=', now()->startOfMonth())
                ->count(),
        ];
    }

    /** @return Collection<int, array<string, mixed>> */
    public function recentDecisions(int $limit = 5): Collection
    {
        $items = collect();

        ReceivingTask::query()
            ->with('animal')
            ->orderByDesc('decision_date')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->each(function (ReceivingTask $task) use ($items) {
                $animal = $task->animal;

                $items->push([
                    'sort_at' => Carbon::parse($task->decision_date)->endOfDay(),
                    'label' => $task->task_type->careDecisionLabel(),
                    'badge_class' => $task->task_type === ReceivingTaskType::AfterHealthRelease
                        ? 'badge-green'
                        : 'badge-blue',
                    'animal_label' => $animal?->displayLabel() ?? '—',
                    'date' => $task->decision_date?->format('Y-m-d'),
                ]);
            });

        HospitalCase::query()
            ->with('animal')
            ->where('status', HospitalCaseStatus::Slaughtered)
            ->orderByDesc('closed_at')
            ->limit($limit)
            ->get()
            ->each(function (HospitalCase $case) use ($items) {
                $items->push([
                    'sort_at' => $case->closed_at ?? $case->updated_at,
                    'label' => 'ذبح اضطراري',
                    'badge_class' => 'badge-orange',
                    'animal_label' => $case->animal?->displayLabel() ?? '—',
                    'date' => $case->closed_at?->format('Y-m-d'),
                ]);
            });

        return $items
            ->sortByDesc(fn (array $item) => $item['sort_at'] ?? now())
            ->take($limit)
            ->values()
            ->map(fn (array $item) => collect($item)->except('sort_at')->all());
    }

    /** @return array<string, mixed> */
    public function charts(): array
    {
        $healthWeekly = collect(range(3, 0))->map(function (int $weeksAgo) {
            $start = now()->startOfWeek()->subWeeks($weeksAgo);
            $end = $start->copy()->endOfWeek();
            $count = HealthCase::query()
                ->whereBetween('created_at', [$start, $end])
                ->count();

            return [
                'label' => 'أس'.(4 - $weeksAgo),
                'count' => $count,
            ];
        });
        $maxHealth = max($healthWeekly->max('count'), 1);

        $groupCounts = Animal::query()
            ->insideZooOfficially()
            ->selectRaw('`group`, count(*) as total')
            ->groupBy('group')
            ->orderByDesc('total')
            ->get();

        $activeHospitalValues = array_map(
            fn (HospitalCaseStatus $status) => $status->value,
            HospitalCaseStatus::active(),
        );

        $medicalFacility = [
            ['label' => 'مستشفى', 'count' => HospitalCase::query()->whereIn('status', $activeHospitalValues)->count()],
            ['label' => 'ميداني', 'count' => FieldCase::query()->where('status', FieldCaseStatus::Active)->count()],
            ['label' => 'حجر', 'count' => Quarantine::query()->where('status', QuarantineStatus::UnderFollowUp)->count()],
            ['label' => 'جاهز إفراج', 'count' => Quarantine::query()
                ->where('status', QuarantineStatus::UnderFollowUp)
                ->whereHas('vaccines')
                ->count()],
        ];
        $maxMedical = max(collect($medicalFacility)->max('count'), 1);

        $birthsMonth = BirthRegistration::query()
            ->where('birth_date', '>=', now()->startOfMonth()->toDateString())
            ->count();
        $mortalityMonth = MortalityCase::query()
            ->where('death_date', '>=', now()->startOfMonth()->toDateString())
            ->count();

        $birthMortality = [
            ['label' => 'ولادات', 'count' => $birthsMonth, 'class' => ''],
            ['label' => 'نفوق', 'count' => $mortalityMonth, 'class' => 'red'],
            ['label' => 'ولادة نافقة', 'count' => 0, 'class' => 'orange'],
        ];
        $maxBirthMortality = max(collect($birthMortality)->max('count'), 1);

        return [
            'health_weekly' => $healthWeekly
                ->map(fn (array $item) => array_merge($item, [
                    'height_pct' => (int) round(($item['count'] / $maxHealth) * 100),
                ]))
                ->all(),
            'animals_by_group' => $groupCounts->values()->map(function ($row, int $index) {
                return [
                    'group' => $row->group,
                    'count' => (int) $row->total,
                    'color' => self::GROUP_COLORS[$index % count(self::GROUP_COLORS)],
                ];
            })->all(),
            'medical_facility' => collect($medicalFacility)
                ->map(fn (array $item) => array_merge($item, [
                    'height_pct' => (int) round(($item['count'] / $maxMedical) * 100),
                    'class' => $item['label'] === 'جاهز إفراج' ? 'orange' : 'blue',
                ]))
                ->all(),
            'birth_mortality_month' => collect($birthMortality)
                ->map(fn (array $item) => array_merge($item, [
                    'height_pct' => (int) round(($item['count'] / $maxBirthMortality) * 100),
                ]))
                ->all(),
        ];
    }

    /** @return Collection<int, array<string, mixed>> */
    public function feedEvents(int $limit = 10): Collection
    {
        $events = collect();

        HealthCase::query()->with('animal')->latest()->limit(5)->get()
            ->each(function (HealthCase $case) use ($events) {
                $events->push([
                    'at' => $case->created_at,
                    'date' => $case->created_at?->format('Y-m-d'),
                    'type_label' => 'صحية',
                    'badge_class' => 'badge-blue',
                    'details' => 'تسجيل حالة صحية — '.($case->animal?->code ?? $case->case_number),
                    'department' => 'الرعاية',
                ]);
            });

        MedicalCaseProcedure::query()->latest('recorded_at')->limit(5)->get()
            ->each(function (MedicalCaseProcedure $procedure) use ($events) {
                $events->push([
                    'at' => $procedure->recorded_at ?? $procedure->created_at,
                    'date' => ($procedure->recorded_at ?? $procedure->created_at)?->format('Y-m-d'),
                    'type_label' => 'إجراء طبي',
                    'badge_class' => 'badge-green',
                    'details' => 'تسجيل إجراء طبي — '.Str::limit($procedure->diagnosis ?: 'متابعة', 40),
                    'department' => 'البيطري',
                ]);
            });

        BirthRegistration::query()->latest()->limit(5)->get()
            ->each(function (BirthRegistration $birth) use ($events) {
                $events->push([
                    'at' => $birth->created_at,
                    'date' => $birth->birth_date?->format('Y-m-d') ?? $birth->created_at?->format('Y-m-d'),
                    'type_label' => 'ولادة',
                    'badge_class' => 'badge-green',
                    'details' => 'تسجيل ولادة جديدة — '.$birth->registration_number,
                    'department' => 'الرعاية',
                ]);
            });

        ReceivingTask::query()->with('animal')->orderByDesc('decision_date')->limit(5)->get()
            ->each(function (ReceivingTask $task) use ($events) {
                $events->push([
                    'at' => Carbon::parse($task->decision_date)->endOfDay(),
                    'date' => $task->decision_date?->format('Y-m-d'),
                    'type_label' => 'قرار طبي',
                    'badge_class' => 'badge-orange',
                    'details' => 'إصدار قرار '.$task->task_type->careDecisionLabel(),
                    'department' => 'البيطري',
                ]);
            });

        TicketSale::query()->latest('sold_at')->limit(3)->get()
            ->each(function (TicketSale $sale) use ($events) {
                $events->push([
                    'at' => $sale->sold_at ?? $sale->created_at,
                    'date' => $sale->sold_at?->format('Y-m-d'),
                    'type_label' => 'تذكرة',
                    'badge_class' => 'badge-gray',
                    'details' => 'بيع '.$sale->quantity.' تذكرة',
                    'department' => 'الإدارة',
                ]);
            });

        MortalityCase::query()->with('animal')->latest()->limit(3)->get()
            ->each(function (MortalityCase $case) use ($events) {
                $code = $case->animal?->code ?? $case->subject_code ?? '—';
                $events->push([
                    'at' => $case->created_at,
                    'date' => $case->death_date?->format('Y-m-d') ?? $case->created_at?->format('Y-m-d'),
                    'type_label' => 'نفوق',
                    'badge_class' => 'badge-red',
                    'details' => 'تسجيل نفوق — '.$code,
                    'department' => 'الرعاية',
                ]);
            });

        TreatmentReferral::query()->with('animal')->latest('referred_at')->limit(3)->get()
            ->each(function (TreatmentReferral $referral) use ($events) {
                $events->push([
                    'at' => $referral->referred_at ?? $referral->created_at,
                    'date' => $referral->referred_at?->format('Y-m-d'),
                    'type_label' => 'إحالة',
                    'badge_class' => 'badge-orange',
                    'details' => 'إحالة علاج — '.($referral->animal?->code ?? $referral->referral_number),
                    'department' => 'الرعاية',
                ]);
            });

        AdminActivityLog::query()->latest()->limit(3)->get()
            ->each(function (AdminActivityLog $log) use ($events) {
                $events->push([
                    'at' => $log->created_at,
                    'date' => $log->created_at?->format('Y-m-d'),
                    'type_label' => 'سجل',
                    'badge_class' => 'badge-green',
                    'details' => Str::limit($log->summary, 60),
                    'department' => 'الإدارة',
                ]);
            });

        return $events
            ->sortByDesc(fn (array $event) => $event['at'] ?? now())
            ->take($limit)
            ->values();
    }

    /** @return Collection<int, array<string, mixed>> */
    public function feedAlerts(): Collection
    {
        $alerts = collect();

        $urgentReport = HealthReport::query()
            ->where('is_urgent', true)
            ->where('status', '!=', HealthReportStatus::Closed)
            ->latest()
            ->first();

        if ($urgentReport) {
            $alerts->push([
                'level' => 'high',
                'title' => 'بلاغ صحي عاجل لم يُغلق',
                'subtitle' => 'مستوى التنبيه: عالي — الرعاية والتغذية',
                'url' => '/director/care/health',
                'action' => 'عرض ←',
            ]);
        }

        $pendingReferrals = TreatmentReferral::query()
            ->where('status', TreatmentReferralStatus::Pending)
            ->count();

        if ($pendingReferrals > 0) {
            $alerts->push([
                'level' => 'medium',
                'title' => $pendingReferrals.' إحالات علاج بانتظار الاعتماد',
                'subtitle' => 'مستوى التنبيه: متوسط — البيطري',
                'url' => '/director/vet/referrals/treatment',
                'action' => 'عرض الإحالات ←',
            ]);
        }

        $pendingAutopsy = AutopsyReferral::query()
            ->where('status', AutopsyReferralStatus::Pending)
            ->count();

        if ($pendingAutopsy > 0) {
            $alerts->push([
                'level' => 'medium',
                'title' => $pendingAutopsy.' حالات نفوق بانتظار نتيجة التشريح',
                'subtitle' => 'مستوى التنبيه: متوسط',
                'url' => '/director/care/mortality',
                'action' => 'عرض ←',
            ]);
        }

        $pendingReceiving = ReceivingTask::query()
            ->where('status', ReceivingTaskStatus::Pending)
            ->count();

        if ($pendingReceiving > 0) {
            $alerts->push([
                'level' => 'medium',
                'title' => $pendingReceiving.' مهام استلام معلقة',
                'subtitle' => 'بانتظار تأكيد الاستلام في المجموعات',
                'url' => '/director/vet/quarantine',
                'action' => 'عرض ←',
            ]);
        }

        $nearBirth = Animal::underBirthFollowUp()
            ->whereHas('birthRegistration', fn ($query) => $query->whereDate(
                'birth_date',
                '<=',
                now()->subDays(BirthRegistrationService::FOLLOW_UP_DAYS - 3)->toDateString(),
            ))
            ->first();

        if ($nearBirth) {
            $alerts->push([
                'level' => 'low',
                'title' => 'مولود قريب من إكمال مدة المتابعة — '.$nearBirth->code,
                'subtitle' => 'مستوى التنبيه: متوسط',
                'url' => '/director/care/births?animal='.$nearBirth->code,
                'action' => 'عرض المواليد ←',
            ]);
        }

        return $alerts->values();
    }

    private function arabicDayShort(Carbon $date): string
    {
        return match ($date->dayOfWeek) {
            Carbon::SUNDAY => 'أحد',
            Carbon::MONDAY => 'إثن',
            Carbon::TUESDAY => 'ثل',
            Carbon::WEDNESDAY => 'أرب',
            Carbon::THURSDAY => 'خم',
            Carbon::FRIDAY => 'جم',
            Carbon::SATURDAY => 'سب',
        };
    }
}
