<?php

namespace App\Services;

use App\Enums\AnimalStatus;
use App\Enums\AutopsyReferralStatus;
use App\Enums\UserRole;
use App\Models\Animal;
use App\Models\AutopsyReferral;
use App\Models\MortalityCase;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AutopsyReferralService
{
    public function __construct(
        private AutopsyReferralNumberGenerator $numbers,
        private AutopsyReferralNotificationService $notifier,
    ) {}

    public function createFromMortalityCase(
        MortalityCase $mortalityCase,
        User $careHead,
        ?string $transferReason = null,
    ): AutopsyReferral {
        $referral = AutopsyReferral::create([
            'referral_number' => $this->numbers->next(),
            'mortality_case_id' => $mortalityCase->id,
            'animal_id' => $mortalityCase->animal_id,
            'group' => $mortalityCase->group,
            'status' => AutopsyReferralStatus::Pending,
            'referred_by' => $careHead->id,
            'referred_at' => now(),
            'transfer_reason' => $transferReason,
        ]);

        $fresh = $referral->fresh(['animal', 'mortalityCase.supervisor', 'referrer']);
        $this->notifier->notifyNewReferral($fresh);

        return $fresh;
    }

    public function document(
        AutopsyReferral $referral,
        User $vetHead,
        string $finalDeathCause,
        ?string $autopsyNotes = null,
        ?string $reportPath = null,
        ?string $documentedAt = null,
    ): AutopsyReferral {
        if (! $referral->canBeDocumented()) {
            return $referral;
        }

        DB::transaction(function () use ($referral, $vetHead, $finalDeathCause, $autopsyNotes, $reportPath, $documentedAt) {
            $referral->update([
                'status' => AutopsyReferralStatus::Documented,
                'documented_by' => $vetHead->id,
                'documented_at' => $documentedAt ? Carbon::parse($documentedAt) : now(),
                'final_death_cause' => $finalDeathCause,
                'autopsy_notes' => $autopsyNotes,
                'report_path' => $reportPath,
            ]);

            if ($referral->animal_id) {
                Animal::withoutGlobalScopes()
                    ->whereKey($referral->animal_id)
                    ->update(['status' => AnimalStatus::Dead->value]);
            }
        });

        $this->notifier->markAsReadForUser($referral, $vetHead);

        return $referral->fresh([
            'animal',
            'mortalityCase.supervisor',
            'referrer',
            'documenter',
        ]);
    }

    /** @return array<string, mixed> */
    public function indexViewData(Request $request, string $portalBase, bool $readOnly = false): array
    {
        $query = AutopsyReferral::query()
            ->with(['animal', 'mortalityCase.supervisor', 'referrer', 'documenter'])
            ->orderByDesc('referred_at');

        if ($group = $request->query('group')) {
            $query->where('group', $group);
        }

        if ($status = $request->query('status')) {
            if (AutopsyReferralStatus::tryFrom($status) !== null) {
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
                    ->orWhereHas('mortalityCase', function ($caseQuery) use ($search) {
                        $caseQuery->where('case_number', 'like', "%{$search}%")
                            ->orWhere('subject_code', 'like', "%{$search}%");
                    });
            });
        }

        $referrals = $query->paginate(15)->withQueryString();
        $isVetPortal = str_contains($portalBase, '/vet');

        return [
            'referrals' => $referrals,
            'readOnly' => $readOnly,
            'canAct' => ! $readOnly && $isVetPortal,
            'portalBase' => $portalBase,
            'isVetPortal' => $isVetPortal,
            'highlightReferral' => $request->query('referral'),
            'referralsForJs' => $this->referralsForJs($referrals, $portalBase),
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
    private function referralsForJs($referrals, string $portalBase): array
    {
        return $referrals->getCollection()->mapWithKeys(function (AutopsyReferral $referral) use ($portalBase) {
            $animal = $referral->animal;
            $mortalityCase = $referral->mortalityCase;

            return [$referral->referral_number => [
                'referral_number' => $referral->referral_number,
                'status' => $referral->status->value,
                'status_label' => $referral->status->label(),
                'animal_code' => $animal?->code ?? $mortalityCase?->subject_code,
                'animal_name' => $animal?->name,
                'animal_species' => $animal?->species ?? $mortalityCase?->subject_type,
                'animal_gender' => $animal?->gender,
                'animal_age' => $animal?->formattedAge(),
                'group' => $referral->group,
                'date' => $referral->referred_at?->format('Y-m-d'),
                'mortality_case_number' => $mortalityCase?->case_number,
                'death_date' => $mortalityCase?->death_date?->format('Y-m-d'),
                'supervisor' => $mortalityCase?->supervisor?->name,
                'death_cause' => $mortalityCase?->displayCause(),
                'notes' => $mortalityCase?->notes,
                'transfer_reason' => $referral->transfer_reason,
                'final_death_cause' => $referral->final_death_cause,
                'autopsy_notes' => $referral->autopsy_notes,
                'documented_at' => $referral->documented_at?->format('Y-m-d'),
                'documenter' => $referral->documenter?->name,
                'report_url' => $referral->report_path
                    ? $portalBase.'/referrals/autopsy/'.$referral->referral_number.'/report'
                    : null,
                'mortality_attachment_url' => $mortalityCase?->has_attachment
                    ? $portalBase.'/mortality/'.$mortalityCase->case_number.'/attachment'
                    : null,
            ]];
        })->all();
    }
}
