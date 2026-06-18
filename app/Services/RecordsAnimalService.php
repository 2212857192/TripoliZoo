<?php

namespace App\Services;

use App\Enums\AnimalExitType;
use App\Enums\AnimalStatus;
use App\Enums\AutopsyReferralStatus;
use App\Enums\HospitalCaseStatus;
use App\Enums\MortalityCaseStatus;
use App\Enums\MortalityVictimKind;
use App\Models\Animal;
use App\Models\AnimalExit;
use App\Models\FieldCase;
use App\Models\HospitalCase;
use App\Models\MedicalNutritionRecommendation;
use App\Models\MortalityCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class RecordsAnimalService
{
    public function __construct(private AnimalCodeGenerator $animalCodes) {}

    /** @return array<string, mixed> */
    public function indexViewData(Request $request, string $portalBase, bool $readOnly = false): array
    {
        $query = Animal::query()
            ->insideZooOfficially()
            ->orderByDesc('registered_at')
            ->orderByDesc('id');

        if ($group = $request->query('group')) {
            $query->where('group', $group);
        }

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('species', 'like', "%{$search}%");
            });
        }

        return [
            'animals' => $query->get(),
            'portalBase' => $portalBase,
            'readOnly' => $readOnly,
            'filters' => [
                'q' => $request->query('q', ''),
                'group' => $request->query('group', ''),
            ],
        ];
    }

    /** @return array<string, mixed> */
    public function createViewData(string $portalBase, bool $readOnly = false): array
    {
        return [
            'portalBase' => $portalBase,
            'readOnly' => $readOnly,
            'nextAnimalCodes' => $this->animalCodes->peekNextByGroup(),
            'groupPrefixes' => animal_group_prefixes(),
        ];
    }

    public function register(Request $request): Animal
    {
        $groups = animal_groups();
        $data = $request->validate($this->validationRules($groups), $this->validationMessages());

        $photoPath = $request->file('photo')?->store('animals', 'public');
        $historyPath = $request->file('prior_history_file')?->store('animal-history', 'public');

        $animal = null;

        DB::transaction(function () use ($data, $photoPath, $historyPath, &$animal) {
            $animal = Animal::withoutGlobalScopes()->create([
                'code' => $this->animalCodes->nextForGroup($data['group']),
                'name' => $data['name'] ?? null,
                'species' => $data['species'],
                'group' => $data['group'],
                'gender' => $data['gender'],
                'distinguishing_marks' => $data['distinguishing_marks'] ?? null,
                'photo_path' => $photoPath,
                'age_method' => $data['age_method'],
                'birth_date' => $data['age_method'] === 'birth' ? $data['birth_date'] : null,
                'approx_age_value' => $data['age_method'] === 'approx' ? $data['approx_age_value'] : null,
                'approx_age_unit' => $data['age_method'] === 'approx' ? $data['approx_age_unit'] : null,
                'origin' => $data['origin'],
                'source' => 'records',
                'registration_note' => $data['animal_source'],
                'prior_history' => $data['prior_history'] ?? null,
                'prior_history_file' => $historyPath,
                'status' => AnimalStatus::Active->value,
                'registered_at' => now()->toDateString(),
            ]);
        });

        return $animal->fresh();
    }

    /** @return array<string, mixed> */
    public function showViewData(Animal $animal, string $portalBase, bool $readOnly = false): array
    {
        $animal->loadMissing(['birthRegistration', 'exitRecord']);

        $newborns = Animal::withQuarantine()
            ->where('mother_id', $animal->id)
            ->orderByDesc('registered_at')
            ->get();

        $mortalityCase = MortalityCase::query()
            ->with(['autopsyReferral', 'supervisor', 'reviewer'])
            ->where('animal_id', $animal->id)
            ->latest('death_date')
            ->first();

        $slaughterCase = HospitalCase::query()
            ->with(['admitter', 'procedures.recorder'])
            ->where('animal_id', $animal->id)
            ->where('status', HospitalCaseStatus::Slaughtered)
            ->latest('closed_at')
            ->first();

        $fieldCases = FieldCase::query()
            ->with(['opener', 'procedures.recorder', 'procedures.nutritionRecommendation'])
            ->where('animal_id', $animal->id)
            ->orderByDesc('opened_at')
            ->get();

        $hospitalCases = HospitalCase::query()
            ->with(['admitter', 'procedures.recorder', 'procedures.nutritionRecommendation'])
            ->where('animal_id', $animal->id)
            ->orderByDesc('admitted_at')
            ->get();

        $profile = $this->buildProfilePayload(
            $animal,
            $newborns,
            $mortalityCase,
            $slaughterCase,
            $fieldCases,
            $hospitalCases,
        );

        $profileLocked = $this->isProfileLocked($animal);

        return [
            'animal' => $animal,
            'portalBase' => $portalBase,
            'readOnly' => $readOnly || $profileLocked,
            'profileLocked' => $profileLocked,
            'lockMessage' => $this->profileLockMessage($animal),
            'canEdit' => ! $readOnly && $this->canModify($animal),
            'canExit' => ! $readOnly && $this->canDocumentExit($animal),
            'canExport' => true,
            'exitTypes' => AnimalExitType::options(),
            'animalProfiles' => [
                $animal->code => $profile,
            ],
        ];
    }

    /** @return array<string, mixed> */
    public function editViewData(Animal $animal, string $portalBase): array
    {
        abort_unless($this->canModify($animal), 403);

        return [
            'animal' => $animal,
            'portalBase' => $portalBase,
        ];
    }

    /** @return array<string, mixed> */
    public function exportViewData(Animal $animal): array
    {
        $show = $this->showViewData($animal, '/records');

        return [
            'animal' => $animal,
            'profile' => $show['animalProfiles'][$animal->code] ?? [],
        ];
    }

    public function update(Request $request, Animal $animal): Animal
    {
        abort_unless($this->canModify($animal), 403);

        $data = $request->validate($this->updateValidationRules(), $this->validationMessages());

        $photoPath = $animal->photo_path;
        if ($request->boolean('remove_photo') && $photoPath) {
            Storage::disk('public')->delete($photoPath);
            $photoPath = null;
        } elseif ($request->file('photo')) {
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $request->file('photo')->store('animals', 'public');
        }

        $historyPath = $animal->prior_history_file;
        if ($request->file('prior_history_file')) {
            if ($historyPath) {
                Storage::disk('public')->delete($historyPath);
            }
            $historyPath = $request->file('prior_history_file')->store('animal-history', 'public');
        }

        $animal->update([
            'name' => $data['name'] ?? null,
            'species' => $data['species'],
            'gender' => $data['gender'],
            'distinguishing_marks' => $data['distinguishing_marks'] ?? null,
            'photo_path' => $photoPath,
            'age_method' => $data['age_method'],
            'birth_date' => $data['age_method'] === 'birth' ? $data['birth_date'] : null,
            'approx_age_value' => $data['age_method'] === 'approx' ? $data['approx_age_value'] : null,
            'approx_age_unit' => $data['age_method'] === 'approx' ? $data['approx_age_unit'] : null,
            'origin' => $data['origin'],
            'registration_note' => $data['animal_source'],
            'prior_history' => $data['prior_history'] ?? null,
            'prior_history_file' => $historyPath,
        ]);

        return $animal->fresh();
    }

    public function documentExit(Request $request, Animal $animal): AnimalExit
    {
        abort_unless($this->canDocumentExit($animal), 403);

        $data = $request->validate([
            'exit_date' => ['required', 'date', 'before_or_equal:today'],
            'exit_type' => ['required', 'string', Rule::in(array_column(AnimalExitType::cases(), 'value'))],
            'recipient' => ['required', 'string', 'max:255'],
            'reason' => ['required', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ], [
            'exit_date.required' => 'تاريخ الخروج مطلوب.',
            'exit_type.required' => 'نوع الخروج مطلوب.',
            'recipient.required' => 'الجهة المستلمة مطلوبة.',
            'reason.required' => 'سبب الخروج مطلوب.',
        ]);

        $attachmentPath = $request->file('attachment')?->store('animal-exits', 'public');

        $exit = null;

        DB::transaction(function () use ($request, $animal, $data, $attachmentPath, &$exit) {
            $exit = AnimalExit::create([
                'animal_id' => $animal->id,
                'recorded_by' => $request->user()?->id,
                'exit_date' => $data['exit_date'],
                'exit_type' => $data['exit_type'],
                'recipient' => $data['recipient'],
                'reason' => $data['reason'],
                'notes' => $data['notes'] ?? null,
                'attachment_path' => $attachmentPath,
            ]);

            $animal->update(['status' => AnimalStatus::Exited->value]);
        });

        return $exit;
    }

    public function canModify(Animal $animal): bool
    {
        return $animal->status === AnimalStatus::Active->value;
    }

    public function canDocumentExit(Animal $animal): bool
    {
        return $animal->status === AnimalStatus::Active->value;
    }

    public function isProfileLocked(Animal $animal): bool
    {
        return $animal->status !== AnimalStatus::Active->value;
    }

    public function profileLockMessage(Animal $animal): ?string
    {
        return match ($animal->status) {
            AnimalStatus::Exited->value => 'خرج هذا الحيوان من الحديقة. الملف للعرض والتصدير فقط ولا يمكن تنفيذ أي إجراء عليه.',
            AnimalStatus::Quarantine->value => 'الحيوان ما زال تحت الحجر الصحي ولم يُسجَّل رسمياً داخل الحديقة.',
            AnimalStatus::PendingReceipt->value => 'الحيوان بانتظار تأكيد الاستلام ولم يُعتمد بعد كحيوان داخل الحديقة.',
            AnimalStatus::Dead->value => 'حيوان نافق. الملف للعرض والتصدير فقط.',
            AnimalStatus::UnderBirthFollowUp->value => 'مولود قيد المتابعة. التعديل يتم عبر مسار تسجيل الولادات.',
            default => null,
        };
    }

    public function findForRecords(string $code): Animal
    {
        return Animal::withQuarantine()
            ->where('code', $code)
            ->firstOrFail();
    }

    /** @return array<string, mixed> */
    private function buildProfilePayload(
        Animal $animal,
        $newborns,
        ?MortalityCase $mortalityCase,
        ?HospitalCase $slaughterCase,
        $fieldCases,
        $hospitalCases,
    ): array {
        $state = $this->recordState($animal, $mortalityCase, $slaughterCase);
        $source = $animal->source === 'quarantine' ? 'quarantine' : 'records';

        $payload = [
            'source' => $source,
            'state' => $state,
            'emoji' => '🦁',
            'displayId' => '#'.$animal->code,
            'name' => $animal->name ?: '—',
            'type' => $animal->species,
            'group' => $animal->group,
            'gender' => $animal->gender,
            'age' => $animal->formattedAge(),
            'regDate' => $animal->registered_at?->format('Y-m-d') ?? '—',
            'marks' => $animal->distinguishing_marks ?: '—',
            'manualEntry' => $animal->source === 'records',
            'photoUrl' => $animal->displayPhotoUrl(),
            'originInfo' => $this->buildOriginPayload($animal),
            'medical' => $this->buildMedicalPayload($animal, $fieldCases, $hospitalCases),
            'repro' => $newborns->isEmpty() ? null : $newborns->map(function (Animal $newborn) {
                $monitoring = $newborn->status === AnimalStatus::UnderBirthFollowUp->value;

                return [
                    'id' => '#'.$newborn->code,
                    'code' => $newborn->code,
                    'date' => $newborn->birthRegistration?->birth_date?->format('Y-m-d')
                        ?? $newborn->registered_at?->format('Y-m-d')
                        ?? '—',
                    'type' => $newborn->species,
                    'gender' => $newborn->gender,
                    'mark' => $newborn->distinguishing_marks ?: '—',
                    'status' => $monitoring ? 'قيد المتابعة' : 'مكتمل',
                    'statusClass' => $monitoring ? 'badge-gray' : 'badge-green',
                    'ref' => 'سجل الولادات',
                ];
            })->values()->all(),
        ];

        if ($animal->prior_history || $animal->prior_history_file) {
            $payload['historyAttachment'] = [
                'date' => $animal->registered_at?->format('Y-m-d') ?? '—',
                'fileName' => $animal->prior_history_file
                    ? basename($animal->prior_history_file)
                    : 'ملخص مكتوب',
                'url' => $animal->prior_history_file
                    ? Storage::url($animal->prior_history_file)
                    : null,
            ];
        }

        if ($state === 'dead') {
            $payload['mortality'] = $this->buildMortalityPayload($mortalityCase);
        }

        if ($state === 'stillborn') {
            $payload['stillborn'] = $this->buildStillbornPayload($animal, $mortalityCase);
        }

        if ($state === 'slaughter') {
            $payload['slaughter'] = $this->buildSlaughterPayload($slaughterCase);
        }

        if ($state === 'exited' && $animal->exitRecord) {
            $exit = $animal->exitRecord;
            $payload['exit'] = [
                'exitDate' => $exit->exit_date?->format('Y-m-d') ?? '—',
                'exitType' => $exit->exit_type->label(),
                'recipient' => $exit->recipient,
                'reason' => $exit->reason,
                'notes' => $exit->notes ?: '—',
                'exitFile' => $exit->attachment_path ? [
                    'fileName' => basename($exit->attachment_path),
                    'url' => $exit->attachmentUrl(),
                    'date' => $exit->exit_date?->format('Y-m-d') ?? '—',
                ] : null,
            ];
        }

        return $payload;
    }

    /** @return array<string, mixed> */
    private function buildOriginPayload(Animal $animal): array
    {
        $entryMethod = match ($animal->source) {
            'records' => 'إدخال يدوي بواسطة مسؤول السجلات',
            'quarantine' => 'دخول عبر الحجر الصحي',
            default => $animal->birth_registration_id
                ? 'تسجيل ولادة عبر مشرف المجموعة'
                : 'تسجيل نظامي',
        };

        $sourceNote = $animal->registration_note
            ?: ($animal->source === 'quarantine' ? 'مسار الحجر الصحي' : '—');

        return [
            'animalOrigin' => $animal->origin ?: '—',
            'source' => $sourceNote,
            'entryMethod' => $entryMethod,
            'regDate' => $animal->registered_at?->format('Y-m-d') ?? '—',
        ];
    }

    /** @return array<string, mixed> */
    private function buildMedicalPayload(Animal $animal, $fieldCases, $hospitalCases): array
    {
        $diagnoses = [];
        $treatments = [];
        $nutrition = [];

        foreach ($fieldCases as $case) {
            $this->appendCaseMedicalRows(
                $diagnoses,
                $treatments,
                $nutrition,
                $case->procedures,
                'حالة طبية ميدانية',
                'حالة طبية ميدانية رقم '.$case->case_number,
                $case->opener?->name,
            );
        }

        foreach ($hospitalCases as $case) {
            $this->appendCaseMedicalRows(
                $diagnoses,
                $treatments,
                $nutrition,
                $case->procedures,
                'حالة داخل المستشفى',
                'حالة داخل المستشفى رقم '.$case->case_number,
                $case->admitter?->name,
            );
        }

        return [
            'diagnoses' => collect($diagnoses)->sortByDesc('date')->values()->all(),
            'treatments' => collect($treatments)->sortByDesc('date')->values()->all(),
            'vaccinations' => [],
            'nutrition' => collect($nutrition)->sortByDesc('startDate')->values()->all(),
        ];
    }

    /** @return array<string, mixed> */
    private function buildMortalityPayload(?MortalityCase $mortalityCase): array
    {
        if (! $mortalityCase) {
            return [
                'caseNumber' => '—',
                'deathDate' => '—',
                'cause' => '—',
                'notes' => '—',
                'supervisor' => '—',
                'caseStatus' => '—',
                'autopsyReferral' => '—',
                'autopsyReason' => '—',
                'docDate' => '—',
                'reviewer' => '—',
                'reportFile' => null,
                'reportUrl' => null,
                'attachmentFile' => null,
                'attachmentUrl' => null,
            ];
        }

        $autopsy = $mortalityCase->autopsyReferral;

        return [
            'caseNumber' => $mortalityCase->case_number,
            'deathDate' => $mortalityCase->death_date?->format('Y-m-d') ?? '—',
            'cause' => $mortalityCase->death_cause ?: ($mortalityCase->notes ?: '—'),
            'notes' => $mortalityCase->notes ?: '—',
            'supervisor' => $mortalityCase->supervisor?->name ?? '—',
            'caseStatus' => $mortalityCase->status->label(),
            'autopsyReferral' => $mortalityCase->status === MortalityCaseStatus::ReferredForAutopsy
                || ($autopsy && $autopsy->status === AutopsyReferralStatus::Pending)
                ? 'نعم'
                : 'لا',
            'autopsyReason' => $mortalityCase->autopsy_reason ?: '—',
            'docDate' => $autopsy?->documented_at?->format('Y-m-d') ?? '—',
            'reviewer' => $mortalityCase->reviewer?->name ?? '—',
            'reportFile' => $autopsy?->report_path ? basename($autopsy->report_path) : null,
            'reportUrl' => $autopsy?->report_path ? Storage::url($autopsy->report_path) : null,
            'attachmentFile' => $mortalityCase->attachment_path ? basename($mortalityCase->attachment_path) : null,
            'attachmentUrl' => $mortalityCase->attachment_path ? Storage::url($mortalityCase->attachment_path) : null,
        ];
    }

    /** @return array<string, mixed> */
    private function buildStillbornPayload(Animal $animal, ?MortalityCase $mortalityCase): array
    {
        if (! $mortalityCase) {
            return [
                'caseNumber' => '—',
                'birthDate' => $animal->birth_date?->format('Y-m-d')
                    ?? $animal->registered_at?->format('Y-m-d')
                    ?? '—',
                'deathDate' => '—',
                'cause' => '—',
                'notes' => '—',
                'supervisor' => '—',
                'autopsy' => '—',
                'docDate' => '—',
            ];
        }

        $autopsy = $mortalityCase->autopsyReferral;

        return [
            'caseNumber' => $mortalityCase->case_number,
            'birthDate' => $animal->birth_date?->format('Y-m-d')
                ?? $animal->registered_at?->format('Y-m-d')
                ?? '—',
            'deathDate' => $mortalityCase->death_date?->format('Y-m-d') ?? '—',
            'cause' => $mortalityCase->displayCause(),
            'notes' => $mortalityCase->notes ?: '—',
            'supervisor' => $mortalityCase->supervisor?->name ?? '—',
            'autopsy' => $autopsy ? 'نعم' : 'لا',
            'docDate' => $autopsy?->documented_at?->format('Y-m-d') ?? '—',
        ];
    }

    /** @return array<string, mixed> */
    private function buildSlaughterPayload(?HospitalCase $slaughterCase): array
    {
        if (! $slaughterCase) {
            return [
                'caseNumber' => '—',
                'decisionDate' => '—',
                'admittedAt' => '—',
                'chiefComplaint' => '—',
                'closingOutcome' => '—',
                'vet' => '—',
                'headVet' => '—',
                'notes' => '—',
                'decisions' => [],
            ];
        }

        $headVet = $slaughterCase->procedures
            ->sortByDesc('recorded_at')
            ->first()
            ?->recorder
            ?->name ?? '—';

        $decisions = $slaughterCase->procedures
            ->sortByDesc('recorded_at')
            ->map(fn ($procedure) => [
                'date' => $procedure->recorded_at?->format('Y-m-d H:i') ?? '—',
                'diagnosis' => $procedure->diagnosis ?: '—',
                'treatment' => $procedure->treatment ?: '—',
                'vet' => $procedure->recorder?->name ?? '—',
                'note' => $procedure->note ?: '—',
                'result' => $procedure->case_result?->label() ?? '—',
            ])
            ->values()
            ->all();

        return [
            'caseNumber' => $slaughterCase->case_number,
            'decisionDate' => $slaughterCase->closed_at?->format('Y-m-d') ?? '—',
            'admittedAt' => $slaughterCase->admitted_at?->format('Y-m-d') ?? '—',
            'chiefComplaint' => $slaughterCase->chief_complaint ?: '—',
            'closingOutcome' => $slaughterCase->closing_outcome ?: '—',
            'vet' => $slaughterCase->admitter?->name ?? '—',
            'headVet' => $headVet,
            'notes' => $slaughterCase->closing_outcome ?: ($slaughterCase->chief_complaint ?: '—'),
            'decisions' => $decisions,
        ];
    }

  /**
     * @param  \Illuminate\Support\Collection<int, \App\Models\MedicalCaseProcedure>  $procedures
     * @param  array<int, array<string, string>>  $diagnoses
     * @param  array<int, array<string, string>>  $treatments
     * @param  array<int, array<string, string>>  $nutrition
     */
    private function appendCaseMedicalRows(
        array &$diagnoses,
        array &$treatments,
        array &$nutrition,
        $procedures,
        string $caseType,
        string $reference,
        ?string $defaultVet,
    ): void {
        foreach ($procedures as $procedure) {
            $date = $procedure->recorded_at?->format('Y-m-d') ?? '—';
            $vet = $procedure->recorder?->name ?? ($defaultVet ?? '—');

            if (filled($procedure->diagnosis)) {
                $diagnoses[] = [
                    'date' => $date,
                    'caseType' => $caseType,
                    'diagnosis' => $procedure->diagnosis,
                    'vet' => $vet,
                    'ref' => $reference,
                ];
            }

            if (filled($procedure->treatment)) {
                $treatments[] = [
                    'date' => $date,
                    'treatment' => $procedure->treatment,
                    'vet' => $vet,
                    'linkedDiagnosis' => $procedure->diagnosis ?: '—',
                    'ref' => $reference,
                ];
            }

            if ($procedure->nutritionRecommendation) {
                $nutrition[] = $this->mapNutritionRow(
                    $procedure->nutritionRecommendation,
                    $reference,
                    $date,
                );
            }
        }
    }

    /** @return array<string, string> */
    private function mapNutritionRow(
        MedicalNutritionRecommendation $recommendation,
        string $reference,
        string $fallbackDate,
    ): array {
        $status = $this->nutritionStatus($recommendation);

        return [
            'startDate' => $recommendation->start_date?->format('Y-m-d') ?? $fallbackDate,
            'recommendation' => $recommendation->recommendation_text,
            'duration' => $this->formatNutritionDuration($recommendation),
            'status' => $status['label'],
            'statusClass' => $status['class'],
            'ref' => $reference,
        ];
    }

    /** @return array{label: string, class: string} */
    private function nutritionStatus(MedicalNutritionRecommendation $recommendation): array
    {
        if ($recommendation->end_date?->isPast()) {
            return ['label' => 'منتهية', 'class' => 'badge-gray'];
        }

        return ['label' => 'جارية', 'class' => 'badge-blue'];
    }

    private function formatNutritionDuration(MedicalNutritionRecommendation $recommendation): string
    {
        if (! $recommendation->start_date || ! $recommendation->end_date) {
            return '—';
        }

        $days = $recommendation->start_date->diffInDays($recommendation->end_date) + 1;

        return $days === 1 ? 'يوم واحد' : "{$days} أيام";
    }

    private function recordState(Animal $animal, ?MortalityCase $mortalityCase, ?HospitalCase $slaughterCase): string
    {
        if ($animal->status === AnimalStatus::Exited->value) {
            return 'exited';
        }

        if ($mortalityCase?->victim_kind === MortalityVictimKind::NewbornUnderFollowUp) {
            return 'stillborn';
        }

        if ($animal->status === AnimalStatus::Dead->value || $mortalityCase) {
            return 'dead';
        }

        if ($slaughterCase) {
            return 'slaughter';
        }

        if ($animal->status === AnimalStatus::Quarantine->value) {
            return 'quarantine';
        }

        if ($animal->status === AnimalStatus::PendingReceipt->value) {
            return 'pending_receipt';
        }

        return 'active';
    }

    /** @return array<string, mixed> */
    private function updateValidationRules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'species' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string', Rule::in(['ذكر', 'أنثى'])],
            'distinguishing_marks' => ['nullable', 'string', 'max:500'],
            'age_method' => ['required', 'string', Rule::in(['birth', 'approx'])],
            'birth_date' => ['required_if:age_method,birth', 'nullable', 'date', 'before_or_equal:today'],
            'approx_age_value' => ['required_if:age_method,approx', 'nullable', 'integer', 'min:1'],
            'approx_age_unit' => ['required_if:age_method,approx', 'nullable', 'string', Rule::in(['أيام', 'أشهر', 'سنوات'])],
            'origin' => ['required', 'string', Rule::in(['مولود داخل الحديقة', 'وارد من خارج الحديقة'])],
            'animal_source' => ['required', 'string', 'max:255'],
            'prior_history' => ['nullable', 'string', 'max:5000'],
            'photo' => ['nullable', 'image', 'max:5120'],
            'prior_history_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'remove_photo' => ['nullable', 'boolean'],
        ];
    }

    /** @return array<string, mixed> */
    private function validationRules(array $groups): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'species' => ['required', 'string', 'max:255'],
            'group' => ['required', 'string', Rule::in($groups)],
            'gender' => ['required', 'string', Rule::in(['ذكر', 'أنثى'])],
            'distinguishing_marks' => ['nullable', 'string', 'max:500'],
            'age_method' => ['required', 'string', Rule::in(['birth', 'approx'])],
            'birth_date' => ['required_if:age_method,birth', 'nullable', 'date', 'before_or_equal:today'],
            'approx_age_value' => ['required_if:age_method,approx', 'nullable', 'integer', 'min:1'],
            'approx_age_unit' => ['required_if:age_method,approx', 'nullable', 'string', Rule::in(['أيام', 'أشهر', 'سنوات'])],
            'origin' => ['required', 'string', Rule::in(['مولود داخل الحديقة', 'وارد من خارج الحديقة'])],
            'animal_source' => ['required', 'string', 'max:255'],
            'prior_history' => ['nullable', 'string', 'max:5000'],
            'photo' => ['nullable', 'image', 'max:5120'],
            'prior_history_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ];
    }

    /** @return array<string, string> */
    private function validationMessages(): array
    {
        return [
            'species.required' => 'النوع مطلوب.',
            'group.required' => 'يجب اختيار المجموعة.',
            'gender.required' => 'يجب اختيار الجنس.',
            'origin.required' => 'يجب تحديد أصل الحيوان.',
            'animal_source.required' => 'يجب إدخال مصدر الحيوان.',
            'birth_date.required_if' => 'تاريخ الميلاد مطلوب عند اختيار تاريخ ميلاد معروف.',
            'approx_age_value.required_if' => 'العمر التقريبي مطلوب.',
            'approx_age_unit.required_if' => 'وحدة العمر مطلوبة.',
        ];
    }
}
