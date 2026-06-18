<?php

namespace App\Services;

use App\Enums\AnimalStatus;
use App\Enums\UserRole;
use App\Models\Animal;
use App\Models\BirthRegistration;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BirthRegistrationService
{
    public const FOLLOW_UP_DAYS = 30;

    public function __construct(
        private BirthRegistrationNumberGenerator $numbers,
        private AnimalCodeGenerator $animalCodes,
        private BirthRegistrationNotificationService $notifier,
    ) {}

    /** @return Collection<int, Animal> */
    public function mothersForSupervisor(User $supervisor): Collection
    {
        if (! $supervisor->assigned_group) {
            return collect();
        }

        return Animal::withQuarantine()
            ->where('group', $supervisor->assigned_group)
            ->where('gender', 'أنثى')
            ->where('status', AnimalStatus::Active->value)
            ->whereNotNull('registered_at')
            ->orderBy('code')
            ->get();
    }

    /** @return Collection<int, Animal> */
    public function newbornsForSupervisor(User $supervisor): Collection
    {
        if (! $supervisor->assigned_group) {
            return collect();
        }

        $this->promoteAllExpired();

        return Animal::underBirthFollowUp()
            ->where('group', $supervisor->assigned_group)
            ->orderBy('code')
            ->get();
    }

    public function findMotherForSupervisor(User $supervisor, string $motherCode): ?Animal
    {
        return $this->mothersForSupervisor($supervisor)
            ->firstWhere('code', $motherCode);
    }

    public function findNewbornForGroup(string $code, ?string $group): ?Animal
    {
        if (! $group) {
            return null;
        }

        $this->promoteAllExpired();

        return Animal::underBirthFollowUp()
            ->where('code', $code)
            ->where('group', $group)
            ->first();
    }

    /**
     * @param  list<array{gender: string, distinguishing_mark?: string|null, note?: string|null, photo_path?: string|null}>  $newborns
     */
    public function register(
        User $supervisor,
        Animal $mother,
        string $birthDate,
        array $newborns,
    ): BirthRegistration {
        if ($mother->group !== $supervisor->assigned_group) {
            throw ValidationException::withMessages([
                'mother_code' => 'الأم لا تنتمي لمجموعتك.',
            ]);
        }

        if ($mother->gender !== 'أنثى' || $mother->status !== AnimalStatus::Active->value) {
            throw ValidationException::withMessages([
                'mother_code' => 'الأم المحددة غير مؤهلة لتسجيل ولادة.',
            ]);
        }

        $registration = null;

        DB::transaction(function () use ($supervisor, $mother, $birthDate, $newborns, &$registration) {
            $registration = BirthRegistration::create([
                'registration_number' => $this->numbers->next(),
                'mother_id' => $mother->id,
                'supervisor_id' => $supervisor->id,
                'group' => $mother->group,
                'birth_date' => $birthDate,
                'birth_count' => count($newborns),
            ]);

            foreach ($newborns as $newbornData) {
                Animal::withoutGlobalScopes()->create([
                    'code' => $this->animalCodes->nextForGroup($mother->group),
                    'species' => $mother->species,
                    'group' => $mother->group,
                    'gender' => $this->normalizeGender($newbornData['gender']),
                    'distinguishing_marks' => $this->nullableString($newbornData['distinguishing_mark'] ?? null),
                    'registration_note' => $this->nullableString($newbornData['note'] ?? null),
                    'photo_path' => $newbornData['photo_path'] ?? null,
                    'age_method' => 'birth',
                    'birth_date' => $birthDate,
                    'status' => AnimalStatus::UnderBirthFollowUp->value,
                    'registered_at' => now()->toDateString(),
                    'mother_id' => $mother->id,
                    'birth_registration_id' => $registration->id,
                ]);
            }
        });

        $fresh = $registration->fresh(['mother', 'supervisor', 'newborns']);
        $this->notifier->notifyNewRegistration($fresh);

        return $fresh;
    }

    public function promoteAllExpired(): int
    {
        return Animal::underBirthFollowUp()
            ->whereNotNull('birth_date')
            ->whereDate('birth_date', '<=', now()->subDays(self::FOLLOW_UP_DAYS)->toDateString())
            ->update(['status' => AnimalStatus::Active->value]);
    }

    public function followUpHasExpired(Animal $newborn): bool
    {
        if (! $newborn->birth_date) {
            return false;
        }

        return (int) $newborn->birth_date->diffInDays(now()) >= self::FOLLOW_UP_DAYS;
    }

    public function daysRemaining(Animal $newborn): ?int
    {
        if (! $newborn->birth_date) {
            return null;
        }

        $elapsed = (int) $newborn->birth_date->diffInDays(now());
        $remaining = self::FOLLOW_UP_DAYS - $elapsed;

        return max(0, $remaining);
    }

    public function isMonitoring(Animal $newborn): bool
    {
        return $newborn->status === AnimalStatus::UnderBirthFollowUp->value;
    }

    public function careHeadUser(): User
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->role !== UserRole::CareHead->value) {
            abort(403, 'هذا الإجراء مخصص لرئيس قسم الرعاية والتغذية.');
        }

        return $user;
    }

    private function normalizeGender(string $gender): string
    {
        return match (strtolower(trim($gender))) {
            'male', 'ذكر' => 'ذكر',
            'female', 'أنثى' => 'أنثى',
            default => throw ValidationException::withMessages([
                'newborns' => 'جنس المولود غير صالح.',
            ]),
        };
    }

    private function nullableString(?string $value): ?string
    {
        $trimmed = trim((string) $value);

        return $trimmed !== '' ? $trimmed : null;
    }
}
