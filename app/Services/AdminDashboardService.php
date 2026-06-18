<?php

namespace App\Services;

use App\Models\AdminActivityLog;
use App\Models\Animal;
use App\Models\AnimalProfile;
use App\Models\MapLocation;
use App\Models\TicketSale;
use App\Models\TicketType;
use App\Models\User;
use App\Models\VisitSetting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class AdminDashboardService
{
    /** @return array<string, mixed> */
    public function viewData(): array
    {
        $visitSettings = VisitSetting::current();
        $employees = User::employees()->get(['id', 'status']);
        $ticketTypes = TicketType::query()->get(['id', 'is_active']);
        $profiles = AnimalProfile::listed()->get(['id', 'is_visible']);
        $mapLocations = MapLocation::query()->get(['id', 'is_active']);
        $animalsWithoutProfile = $this->animalsWithoutProfileCount();

        $ticketsToday = $this->ticketSalesForDate(today());
        $ticketsMonth = $this->ticketSalesForRange(now()->startOfMonth(), now());

        return [
            'visitSettings' => $visitSettings,
            'stats' => [
                'employees' => [
                    'total' => $employees->count(),
                    'active' => $employees->where('status', 'active')->count(),
                    'inactive' => $employees->where('status', 'inactive')->count(),
                ],
                'ticket_types' => [
                    'total' => $ticketTypes->count(),
                    'active' => $ticketTypes->where('is_active', true)->count(),
                    'inactive' => $ticketTypes->where('is_active', false)->count(),
                ],
                'profiles' => [
                    'total' => $profiles->count(),
                    'visible' => $profiles->where('is_visible', true)->count(),
                    'hidden' => $profiles->where('is_visible', false)->count(),
                ],
                'map_locations' => [
                    'total' => $mapLocations->count(),
                    'active' => $mapLocations->where('is_active', true)->count(),
                    'inactive' => $mapLocations->where('is_active', false)->count(),
                ],
                'animals_without_profile' => $animalsWithoutProfile,
                'tickets_today' => $ticketsToday,
                'tickets_month' => $ticketsMonth,
            ],
            'visitorAppRows' => $this->visitorAppRows(
                $visitSettings,
                $ticketTypes,
                $profiles,
                $mapLocations,
                $animalsWithoutProfile,
            ),
            'alerts' => $this->alerts(
                $visitSettings,
                $ticketTypes,
                $profiles,
                $mapLocations,
                $animalsWithoutProfile,
            ),
            'recentActivities' => $this->recentActivities(),
        ];
    }

    private function animalsWithoutProfileCount(): int
    {
        return Animal::query()
            ->insideZooOfficially()
            ->whereDoesntHave('profile')
            ->count();
    }

    /** @return array{count: int, quantity: int, revenue: float} */
    private function ticketSalesForDate(Carbon $date): array
    {
        return $this->ticketSalesForRange($date->copy()->startOfDay(), $date->copy()->endOfDay());
    }

    /** @return array{count: int, quantity: int, revenue: float} */
    private function ticketSalesForRange(Carbon $from, Carbon $to): array
    {
        $sales = TicketSale::query()
            ->whereBetween('sold_at', [$from, $to])
            ->get(['quantity', 'total_amount']);

        return [
            'count' => $sales->count(),
            'quantity' => (int) $sales->sum('quantity'),
            'revenue' => (float) $sales->sum('total_amount'),
        ];
    }

    /**
     * @param  Collection<int, TicketType>  $ticketTypes
     * @param  Collection<int, AnimalProfile>  $profiles
     * @param  Collection<int, MapLocation>  $mapLocations
     * @return list<array{label: string, status: string, tone: string}>
     */
    private function visitorAppRows(
        VisitSetting $visitSettings,
        Collection $ticketTypes,
        Collection $profiles,
        Collection $mapLocations,
        int $animalsWithoutProfile,
    ): array {
        $activeTickets = $ticketTypes->where('is_active', true)->count();
        $inactiveTickets = $ticketTypes->where('is_active', false)->count();
        $visibleProfiles = $profiles->where('is_visible', true)->count();
        $hiddenProfiles = $profiles->where('is_visible', false)->count();
        $activeLocations = $mapLocations->where('is_active', true)->count();

        return [
            [
                'label' => 'معلومات الزيارة',
                'status' => $visitSettings->status_visible
                    ? ($visitSettings->status_text ?: 'مفعّلة للزوار')
                    : 'مخفية عن الزوار',
                'tone' => $visitSettings->status_visible ? 'ok' : 'warn',
            ],
            [
                'label' => 'أوقات العمل',
                'status' => $this->workingHoursLabel($visitSettings),
                'tone' => 'neutral',
            ],
            [
                'label' => 'أنواع التذاكر',
                'status' => "{$activeTickets} مفعّلة / {$inactiveTickets} معطلة",
                'tone' => $activeTickets > 0 ? 'ok' : 'warn',
            ],
            [
                'label' => 'محتوى الحيوانات للزوار',
                'status' => "{$visibleProfiles} ظاهر / {$hiddenProfiles} مخفي",
                'tone' => $visibleProfiles > 0 ? 'ok' : 'warn',
            ],
            [
                'label' => 'مواقع الخريطة',
                'status' => "{$activeLocations} نشط / {$mapLocations->count()} إجمالي",
                'tone' => $activeLocations > 0 ? 'ok' : 'warn',
            ],
            [
                'label' => 'حيوانات بلا محتوى تعريفي',
                'status' => $animalsWithoutProfile > 0
                    ? "{$animalsWithoutProfile} حيوان يحتاج محتوى"
                    : 'كل الحيوانات المؤهلة لها محتوى',
                'tone' => $animalsWithoutProfile > 0 ? 'warn' : 'ok',
            ],
        ];
    }

    private function workingHoursLabel(VisitSetting $visitSettings): string
    {
        $days = collect($visitSettings->working_days ?? [])
            ->filter()
            ->count();

        $open = $visitSettings->open_time ? substr((string) $visitSettings->open_time, 0, 5) : '—';
        $close = $visitSettings->close_time ? substr((string) $visitSettings->close_time, 0, 5) : '—';

        return "{$days} أيام عمل — {$open} إلى {$close}";
    }

    /**
     * @param  Collection<int, TicketType>  $ticketTypes
     * @param  Collection<int, AnimalProfile>  $profiles
     * @param  Collection<int, MapLocation>  $mapLocations
     * @return list<array{message: string, url: string, tone: string}>
     */
    private function alerts(
        VisitSetting $visitSettings,
        Collection $ticketTypes,
        Collection $profiles,
        Collection $mapLocations,
        int $animalsWithoutProfile,
    ): array {
        $alerts = [];

        if ($animalsWithoutProfile > 0) {
            $alerts[] = [
                'message' => "{$animalsWithoutProfile} حيوان داخل الحديقة بلا محتوى تعريفي في تطبيق الزائر.",
                'url' => route('admin.animals.create'),
                'tone' => 'warn',
            ];
        }

        if ($ticketTypes->where('is_active', true)->isEmpty()) {
            $alerts[] = [
                'message' => 'لا توجد أنواع تذاكر مفعّلة حالياً.',
                'url' => route('admin.tickets.index'),
                'tone' => 'warn',
            ];
        }

        if ($profiles->where('is_visible', true)->isEmpty()) {
            $alerts[] = [
                'message' => 'لا يوجد محتوى حيوانات ظاهر للزوار في التطبيق.',
                'url' => route('admin.animals.index'),
                'tone' => 'warn',
            ];
        }

        if ($mapLocations->where('is_active', true)->isEmpty()) {
            $alerts[] = [
                'message' => 'لا توجد مواقع نشطة على الخريطة التفاعلية.',
                'url' => route('admin.map-locations.index'),
                'tone' => 'warn',
            ];
        }

        if (! $visitSettings->status_visible) {
            $alerts[] = [
                'message' => 'حالة الزيارة مخفية عن تطبيق الزائر.',
                'url' => route('admin.visit-info.show'),
                'tone' => 'warn',
            ];
        }

        return $alerts;
    }

    /** @return Collection<int, array<string, mixed>> */
    private function recentActivities(): Collection
    {
        return AdminActivityLog::query()
            ->with('user:id,name')
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn (AdminActivityLog $activity) => [
                'date' => $activity->created_at?->format('d/m/Y H:i'),
                'entity_label' => $this->entityLabel($activity->entity_type),
                'action_label' => $this->actionLabel($activity->action),
                'summary' => $activity->summary,
                'user_name' => $activity->user?->name ?? 'النظام',
            ]);
    }

    private function entityLabel(string $entityType): string
    {
        return match ($entityType) {
            'user' => 'حسابات الموظفين',
            'ticket_type' => 'التذاكر',
            'ticket_sale' => 'مبيعات التذاكر',
            'visit_settings' => 'معلومات الزيارة',
            'animal_profile' => 'محتوى الحيوانات',
            'map_location' => 'مواقع الخريطة',
            default => $entityType,
        };
    }

    private function actionLabel(string $action): string
    {
        return match ($action) {
            'created' => 'إضافة',
            'updated' => 'تعديل',
            'deleted' => 'حذف',
            'status' => 'تغيير حالة',
            'visibility' => 'إظهار/إخفاء',
            default => $action,
        };
    }
}
