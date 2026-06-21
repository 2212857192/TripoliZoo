<?php

namespace App\Services;

use App\Models\AutopsyReferralNotification;
use App\Models\CareNotification;
use App\Models\HealthCaseNotification;
use App\Models\HospitalCaseNotification;
use App\Models\OperationalNoteNotification;
use App\Models\QuarantineNotification;
use App\Models\TreatmentReferralNotification;
use App\Models\User;
use App\Models\VetNotification;
use Illuminate\Support\Collection;

class PortalNotificationFeedService
{
    private const FEED_LIMIT = 30;

    /** @return array{unread_count: int, html: string|null, version: string} */
    public function forVet(User $user): array
    {
        $feed = $this->buildVetFeed($user);
        $unreadCount = $this->vetUnreadCount($user);

        $html = $feed->isEmpty()
            ? null
            : view('partials.vet-notification-feed', [
                'vetNotificationFeed' => $feed,
            ])->render();

        return [
            'unread_count' => $unreadCount,
            'html' => $html,
            'version' => $this->versionFromItems(
                $feed->map(fn (array $item) => [
                    'kind' => $item['kind'],
                    'reference' => $item['reference'],
                    'is_unread' => $item['is_unread'],
                    'created_timestamp' => $item['created_timestamp'],
                ]),
                $unreadCount,
            ),
        ];
    }

    /** @return array{unread_count: int, html: string|null, version: string} */
    public function forCare(User $user): array
    {
        $feed = $this->buildCareFeed($user);
        $unreadCount = $this->careUnreadCount($user);

        $html = $feed->isEmpty()
            ? null
            : view('partials.care-notification-feed', [
                'careNotificationFeed' => $feed,
            ])->render();

        return [
            'unread_count' => $unreadCount,
            'html' => $html,
            'version' => $this->versionFromItems(
                $feed->map(fn (array $item) => [
                    'kind' => $item['kind'],
                    'reference' => $item['reference'],
                    'is_unread' => $item['is_unread'],
                    'created_timestamp' => $item['created_timestamp'],
                ]),
                $unreadCount,
            ),
        ];
    }

    /** @return Collection<int, array<string, mixed>> */
    public function buildCareFeed(User $user): Collection
    {
        $items = collect();

        CareNotification::query()
            ->where('user_id', $user->id)
            ->with(['receivingTask.animal'])
            ->latest()
            ->limit(self::FEED_LIMIT)
            ->get()
            ->each(function (CareNotification $notification) use ($items): void {
                $taskNumber = $notification->receivingTask?->task_number;
                if (! $taskNumber) {
                    return;
                }

                $items->push($this->feedItem(
                    kind: 'receiving',
                    icon: '📋',
                    notification: $notification,
                    reference: $taskNumber,
                ));
            });

        HealthCaseNotification::query()
            ->where('user_id', $user->id)
            ->with(['healthCase.animal'])
            ->latest()
            ->limit(self::FEED_LIMIT)
            ->get()
            ->each(function (HealthCaseNotification $notification) use ($items): void {
                $caseNumber = $notification->healthCase?->case_number;
                if (! $caseNumber) {
                    return;
                }

                $items->push($this->feedItem(
                    kind: 'health',
                    icon: '🩺',
                    notification: $notification,
                    reference: $caseNumber,
                ));
            });

        OperationalNoteNotification::query()
            ->where('user_id', $user->id)
            ->with(['operationalNote.supervisor'])
            ->latest()
            ->limit(self::FEED_LIMIT)
            ->get()
            ->each(function (OperationalNoteNotification $notification) use ($items): void {
                $noteNumber = $notification->operationalNote?->note_number;
                if (! $noteNumber) {
                    return;
                }

                $items->push($this->feedItem(
                    kind: 'operational_note',
                    icon: '📝',
                    notification: $notification,
                    reference: $noteNumber,
                ));
            });

        return $items
            ->sortByDesc(fn (array $item) => $item['created_at'])
            ->take(self::FEED_LIMIT)
            ->values();
    }

    /** @return Collection<int, array<string, mixed>> */
    public function buildVetFeed(User $user): Collection
    {
        $items = collect();

        QuarantineNotification::query()
            ->where('user_id', $user->id)
            ->with(['quarantine.animal'])
            ->latest()
            ->limit(self::FEED_LIMIT)
            ->get()
            ->each(function (QuarantineNotification $notification) use ($items): void {
                $caseNumber = $notification->quarantine?->case_number;
                if (! $caseNumber) {
                    return;
                }

                $items->push($this->feedItem(
                    kind: 'quarantine',
                    icon: '🔒',
                    notification: $notification,
                    reference: $caseNumber,
                ));
            });

        VetNotification::query()
            ->where('user_id', $user->id)
            ->with(['receivingTask.animal'])
            ->latest()
            ->limit(self::FEED_LIMIT)
            ->get()
            ->each(function (VetNotification $notification) use ($items): void {
                $taskNumber = $notification->receivingTask?->task_number;
                if (! $taskNumber) {
                    return;
                }

                $items->push($this->feedItem(
                    kind: 'receiving',
                    icon: '📋',
                    notification: $notification,
                    reference: $taskNumber,
                ));
            });

        TreatmentReferralNotification::query()
            ->where('user_id', $user->id)
            ->with(['treatmentReferral.animal'])
            ->latest()
            ->limit(self::FEED_LIMIT)
            ->get()
            ->each(function (TreatmentReferralNotification $notification) use ($items): void {
                $referralNumber = $notification->treatmentReferral?->referral_number;
                if (! $referralNumber) {
                    return;
                }

                $items->push($this->feedItem(
                    kind: 'treatment_referral',
                    icon: '🏥',
                    notification: $notification,
                    reference: $referralNumber,
                ));
            });

        AutopsyReferralNotification::query()
            ->where('user_id', $user->id)
            ->with(['autopsyReferral.animal'])
            ->latest()
            ->limit(self::FEED_LIMIT)
            ->get()
            ->each(function (AutopsyReferralNotification $notification) use ($items): void {
                $referralNumber = $notification->autopsyReferral?->referral_number;
                if (! $referralNumber) {
                    return;
                }

                $items->push($this->feedItem(
                    kind: 'autopsy_referral',
                    icon: '🔬',
                    notification: $notification,
                    reference: $referralNumber,
                ));
            });

        HospitalCaseNotification::query()
            ->where('user_id', $user->id)
            ->with(['hospitalCase.animal'])
            ->latest()
            ->limit(self::FEED_LIMIT)
            ->get()
            ->each(function (HospitalCaseNotification $notification) use ($items): void {
                $caseNumber = $notification->hospitalCase?->case_number;
                if (! $caseNumber) {
                    return;
                }

                $items->push($this->feedItem(
                    kind: 'hospital_case',
                    icon: '🏨',
                    notification: $notification,
                    reference: $caseNumber,
                ));
            });

        return $items
            ->sortByDesc(fn (array $item) => $item['created_at'])
            ->take(self::FEED_LIMIT)
            ->values();
    }

    public function vetUnreadCount(User $user): int
    {
        return QuarantineNotification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->count()
            + VetNotification::query()
                ->where('user_id', $user->id)
                ->whereNull('read_at')
                ->count()
            + TreatmentReferralNotification::query()
                ->where('user_id', $user->id)
                ->whereNull('read_at')
                ->count()
            + AutopsyReferralNotification::query()
                ->where('user_id', $user->id)
                ->whereNull('read_at')
                ->count()
            + HospitalCaseNotification::query()
                ->where('user_id', $user->id)
                ->whereNull('read_at')
                ->count();
    }

    public function careUnreadCount(User $user): int
    {
        return CareNotification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->count()
            + HealthCaseNotification::query()
                ->where('user_id', $user->id)
                ->whereNull('read_at')
                ->count()
            + OperationalNoteNotification::query()
                ->where('user_id', $user->id)
                ->whereNull('read_at')
                ->count();
    }

    /**
     * @param  QuarantineNotification|VetNotification|TreatmentReferralNotification|AutopsyReferralNotification|HospitalCaseNotification|CareNotification|HealthCaseNotification|OperationalNoteNotification  $notification
     * @return array<string, mixed>
     */
    private function feedItem(string $kind, string $icon, object $notification, string $reference): array
    {
        return [
            'kind' => $kind,
            'icon' => $icon,
            'title' => $notification->title,
            'message' => $notification->message,
            'reference' => $reference,
            'is_unread' => $notification->read_at === null,
            'created_at' => $notification->created_at,
            'created_timestamp' => $notification->created_at?->timestamp ?? 0,
            'time_label' => $notification->created_at?->diffForHumans(),
        ];
    }

    /**
     * @param  Collection<int, array{kind: string, reference: string, is_unread: bool, created_timestamp: int}>  $items
     */
    private function versionFromItems(Collection $items, int $unreadCount): string
    {
        $payload = $unreadCount.'|'.$items
            ->map(fn (array $item) => implode(':', [
                $item['kind'],
                $item['reference'],
                $item['is_unread'] ? '1' : '0',
                (string) ($item['created_timestamp'] ?? 0),
            ]))
            ->implode('|');

        return hash('sha256', $payload);
    }
}
