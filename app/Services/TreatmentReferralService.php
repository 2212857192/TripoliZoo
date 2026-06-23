<?php

namespace App\Services;

use App\Enums\HealthCaseStatus;
use App\Enums\TreatmentReferralStatus;
use App\Enums\UserRole;
use App\Models\TreatmentReferral;
use App\Models\User;
use Illuminate\Http\Request;

class TreatmentReferralService
{
    public function __construct(
        private TreatmentReferralNotificationService $notifier,
        private HospitalCaseService $hospitalCases,
        private AnimalLifecycleService $animalLifecycle,
    ) {}

    public function approve(TreatmentReferral $referral, User $vetHead): TreatmentReferral
    {
        $referral->loadMissing('animal');
        if ($referral->animal) {
            $this->animalLifecycle->assertAnimalCanReceiveActions($referral->animal);
        }

        if ($referral->status !== TreatmentReferralStatus::Pending) {
            return $referral;
        }

        $referral->update([
            'status' => TreatmentReferralStatus::Approved,
            'reviewed_by' => $vetHead->id,
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);

        $this->notifier->markAsReadForUser($referral, $vetHead);
        $this->hospitalCases->createFromReferral($referral, $vetHead);

        return $referral->fresh(['animal', 'healthCase.supervisor', 'referrer', 'reviewer', 'hospitalCase']);
    }

    public function reject(TreatmentReferral $referral, User $vetHead, string $reason): TreatmentReferral
    {
        if ($referral->status !== TreatmentReferralStatus::Pending) {
            return $referral;
        }

        $referral->update([
            'status' => TreatmentReferralStatus::Rejected,
            'reviewed_by' => $vetHead->id,
            'reviewed_at' => now(),
            'rejection_reason' => $reason,
        ]);

        $referral->loadMissing('healthCase');
        if ($referral->healthCase?->status === HealthCaseStatus::Referred) {
            $referral->healthCase->update([
                'status' => HealthCaseStatus::Reviewed,
                'reviewed_by' => $vetHead->id,
                'reviewed_at' => now(),
            ]);
        }

        $this->notifier->markAsReadForUser($referral, $vetHead);

        return $referral->fresh(['animal', 'healthCase.supervisor', 'referrer', 'reviewer']);
    }

    /** @return array<string, mixed> */
    public function indexViewData(Request $request, string $portalBase, bool $readOnly = false): array
    {
        $query = TreatmentReferral::query()
            ->with(['animal', 'healthCase.supervisor', 'referrer', 'reviewer', 'hospitalCase'])
            ->orderByDesc('referred_at');

        if ($group = $request->query('group')) {
            $query->where('group', $group);
        }

        if ($status = $request->query('status')) {
            if (in_array($status, array_column(TreatmentReferralStatus::cases(), 'value'), true)) {
                $query->where('status', $status);
            }
        }

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('referral_number', 'like', "%{$search}%")
                    ->orWhereHas('animal', function ($animalQuery) use ($search) {
                        $animalQuery->where('code', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%")
                            ->orWhere('species', 'like', "%{$search}%");
                    })
                    ->orWhereHas('healthCase', function ($caseQuery) use ($search) {
                        $caseQuery->where('case_number', 'like', "%{$search}%");
                    });
            });
        }

        $referrals = $query->paginate(15)->withQueryString();

        return [
            'referrals' => $referrals,
            'readOnly' => $readOnly,
            'canAct' => ! $readOnly,
            'portalBase' => $portalBase,
            'highlightReferral' => $request->query('referral'),
            'referralsForJs' => $this->referralsForJs($referrals),
            'filters' => [
                'q' => $request->query('q', ''),
                'group' => $request->query('group', ''),
                'status' => $request->query('status', ''),
            ],
        ];
    }

    public function vetHeadUser(): User
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->role !== UserRole::VetHead->value) {
            abort(403, 'هذا الإجراء مخصص لرئيس قسم المستشفى البيطري.');
        }

        return $user;
    }

    /** @return array<string, array<string, mixed>> */
    private function referralsForJs($referrals): array
    {
        return $referrals->getCollection()->mapWithKeys(function (TreatmentReferral $referral) {
            $animal = $referral->animal;
            $healthCase = $referral->healthCase;

            return [$referral->referral_number => [
                'referral_number' => $referral->referral_number,
                'status' => $referral->status->value,
                'status_label' => $referral->status->label(),
                'animal_code' => $animal?->code,
                'animal_name' => $animal?->name,
                'animal_species' => $animal?->species,
                'animal_gender' => $animal?->gender,
                'animal_age' => $animal?->formattedAge(),
                'group' => $referral->group,
                'date' => $referral->referred_at?->format('Y-m-d'),
                'case_number' => $healthCase?->case_number,
                'case_date' => $healthCase?->created_at?->format('Y-m-d'),
                'supervisor' => $healthCase?->supervisor?->name,
                'description' => $healthCase?->description,
                'animal_notes' => $healthCase?->animal_notes,
                'reviewed_at' => $referral->reviewed_at?->format('Y-m-d'),
                'rejection_reason' => $referral->rejection_reason,
                'hospital_case_number' => $referral->hospitalCase?->case_number,
            ]];
        })->all();
    }
}
