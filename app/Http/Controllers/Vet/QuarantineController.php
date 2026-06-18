<?php

namespace App\Http\Controllers\Vet;

use App\Enums\AnimalStatus;
use App\Enums\QuarantineStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\Quarantine;
use App\Models\QuarantineNote;
use App\Models\QuarantineNotification;
use App\Models\QuarantineVaccine;
use App\Models\User;
use App\Services\AnimalCodeGenerator;
use App\Services\QuarantineCaseNumberGenerator;
use App\Services\QuarantineNotificationService;
use App\Services\ReceivingTaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class QuarantineController extends Controller
{
    public function index(bool $readOnly = false, ?string $layout = null): View
    {
        $user = auth()->user();
        $animalCodes = app(AnimalCodeGenerator::class);

        $query = Quarantine::query()
            ->with(['animal', 'responsibleVet', 'creator', 'notes.author', 'vaccines.author'])
            ->orderByDesc('entry_date')
            ->orderByDesc('id');

        if (! $readOnly && $user?->isVeterinarian()) {
            $query->whereHas('animal', fn ($q) => $q->where('group', $user->assigned_group));
        }

        $quarantines = $query->get();
        $grouped = $quarantines->groupBy(fn (Quarantine $q) => $q->status->value);
        $doctorsByGroup = $this->doctorsByGroup();

        $openCase = request()->query('open');
        $notifier = app(QuarantineNotificationService::class);

        if ($openCase && $user && ! $readOnly) {
            $openQuarantine = $quarantines->firstWhere('case_number', $openCase);
            if ($openQuarantine) {
                $notifier->markQuarantineAsReadForUser($openQuarantine, $user);
            }
        }

        return view('vet.quarantine', [
            '__layout' => $layout,
            'readOnly' => $readOnly,
            'openCase' => $openCase,
            'followup' => $grouped->get(QuarantineStatus::UnderFollowUp->value, collect()),
            'cleared' => $grouped->get(QuarantineStatus::HealthReleased->value, collect()),
            'failed' => $grouped->get(QuarantineStatus::Failed->value, collect()),
            'quarantineRecords' => $this->buildRecordsMap($quarantines, $doctorsByGroup),
            'animalGroups' => animal_groups(),
            'nextQuarantineCode' => $animalCodes->nextForQuarantine(),
            'doctorsByGroup' => $doctorsByGroup,
            'canAddClinicalRecords' => ! $readOnly && $user?->isVeterinarian(),
        ]);
    }

    public function store(
        Request $request,
        QuarantineCaseNumberGenerator $caseNumbers,
        QuarantineNotificationService $notifier,
        AnimalCodeGenerator $animalCodes
    ): RedirectResponse {
        $groups = animal_groups();

        $data = $request->validate([
            'species' => ['required', 'string', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'group' => ['required', 'string', Rule::in($groups)],
            'gender' => ['required', 'string', Rule::in(['ذكر', 'أنثى'])],
            'distinguishing_marks' => ['nullable', 'string', 'max:500'],
            'origin' => ['required', 'string', 'max:255'],
            'entry_date' => ['required', 'date'],
            'initial_health_status' => ['required', 'string', 'max:500'],
            'initial_notes' => ['nullable', 'string', 'max:2000'],
            'age_method' => ['required', 'string', Rule::in(['birth', 'approx'])],
            'birth_date' => ['required_if:age_method,birth', 'nullable', 'date'],
            'approx_age_value' => ['required_if:age_method,approx', 'nullable', 'integer', 'min:1'],
            'approx_age_unit' => ['required_if:age_method,approx', 'nullable', 'string', Rule::in(['أيام', 'أشهر', 'سنوات'])],
            'photo' => ['nullable', 'image', 'max:5120'],
        ]);

        $user = $request->user();
        $photoPath = $request->hasFile('photo')
            ? $request->file('photo')->store('animals', 'public')
            : null;

        $quarantine = null;

        DB::transaction(function () use ($data, $user, $photoPath, $caseNumbers, $animalCodes, &$quarantine) {
            $animal = Animal::create([
                'code' => $animalCodes->nextForQuarantine(),
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
                'source' => 'quarantine',
                'status' => AnimalStatus::Quarantine->value,
                'registered_at' => $data['entry_date'],
            ]);

            $quarantine = Quarantine::create([
                'case_number' => $caseNumbers->next(),
                'animal_id' => $animal->id,
                'reason' => '',
                'initial_health_status' => $data['initial_health_status'],
                'status' => QuarantineStatus::UnderFollowUp,
                'entry_date' => $data['entry_date'],
                'initial_notes' => $data['initial_notes'] ?? null,
                'responsible_vet_id' => $this->resolveResponsibleVetId($user, $data['group']),
                'created_by' => $user->id,
            ]);
        });

        $notifier->notifyGroupVets($quarantine);

        return redirect()
            ->route('quarantine.index')
            ->with('success', 'تم إدخال الحيوان للحجر الصحي وإشعار أطباء المجموعة «'.$data['group'].'».');
    }

    public function update(Request $request, Quarantine $quarantine): RedirectResponse
    {
        $this->authorizeQuarantineAccess($quarantine);

        $data = $request->validate([
            'species' => ['required', 'string', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'gender' => ['required', 'string', Rule::in(['ذكر', 'أنثى'])],
            'distinguishing_marks' => ['nullable', 'string', 'max:500'],
            'origin' => ['required', 'string', 'max:255'],
            'entry_date' => ['required', 'date'],
            'initial_notes' => ['nullable', 'string', 'max:2000'],
            'age_method' => ['required', 'string', Rule::in(['birth', 'approx'])],
            'birth_date' => ['required_if:age_method,birth', 'nullable', 'date'],
            'approx_age_value' => ['required_if:age_method,approx', 'nullable', 'integer', 'min:1'],
            'approx_age_unit' => ['required_if:age_method,approx', 'nullable', 'string', Rule::in(['أيام', 'أشهر', 'سنوات'])],
            'photo' => ['nullable', 'image', 'max:5120'],
        ]);

        $quarantine->loadMissing('animal');
        $animal = $quarantine->animal;

        DB::transaction(function () use ($request, $data, $quarantine, $animal) {
            $photoPath = $animal->photo_path;

            if ($request->hasFile('photo')) {
                if ($photoPath) {
                    Storage::disk('public')->delete($photoPath);
                }
                $photoPath = $request->file('photo')->store('animals', 'public');
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
                'registered_at' => $data['entry_date'],
            ]);

            $quarantine->update([
                'entry_date' => $data['entry_date'],
                'initial_notes' => $data['initial_notes'] ?? null,
            ]);
        });

        return redirect()
            ->route('quarantine.index', ['open' => $quarantine->case_number])
            ->with('success', 'تم حفظ تعديلات بيانات الحجر الصحي بنجاح.');
    }

    public function markNotificationRead(Quarantine $quarantine): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            abort(401);
        }

        QuarantineNotification::query()
            ->where('user_id', $user->id)
            ->where('quarantine_id', $quarantine->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['ok' => true]);
    }

    public function markNotificationReadByCase(Request $request): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            abort(401);
        }

        $data = $request->validate([
            'case_number' => ['required', 'string', 'max:50'],
        ]);

        $quarantine = Quarantine::query()
            ->where('case_number', $data['case_number'])
            ->first();

        if (! $quarantine) {
            return response()->json(['ok' => false, 'message' => 'Quarantine not found'], 404);
        }

        QuarantineNotification::query()
            ->where('user_id', $user->id)
            ->where('quarantine_id', $quarantine->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['ok' => true]);
    }

    public function markAllNotificationsRead(): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            abort(401);
        }

        QuarantineNotification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['ok' => true]);
    }

    public function release(
        Request $request,
        Quarantine $quarantine,
        ReceivingTaskService $receivingTasks
    ): RedirectResponse {
        $this->authorizeQuarantineAccess($quarantine);

        if ($quarantine->status !== QuarantineStatus::UnderFollowUp) {
            return back()->with('error', 'لا يمكن إصدار إفراج صحي إلا للحالات قيد المتابعة.');
        }

        DB::transaction(function () use ($quarantine) {
            $quarantine->update([
                'status' => QuarantineStatus::HealthReleased,
                'released_at' => now()->toDateString(),
            ]);
        });

        $task = $receivingTasks->createFromQuarantineRelease($quarantine->fresh('animal'), $request->user());
        $animal = $quarantine->fresh('animal')->animal;

        $message = $task
            ? "تم إصدار قرار الإفراج الصحي وإرسال مهمة استلام لمشرف مجموعة «{$animal->group}»."
            : "تم إصدار قرار الإفراج الصحي. لم يُعثر على مشرف مجموعة لـ «{$animal->group}» — راجع حسابات الموظفين.";

        return redirect()
            ->route('quarantine.index')
            ->with('success', $message);
    }

    public function close(Request $request, Quarantine $quarantine): RedirectResponse
    {
        $this->authorizeQuarantineAccess($quarantine);

        if ($quarantine->status !== QuarantineStatus::UnderFollowUp) {
            return back()->with('error', 'لا يمكن إنهاء هذه الحالة حالياً.');
        }

        $data = $request->validate([
            'close_reason' => ['required', 'string', 'max:255'],
            'close_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $quarantine->update([
            'status' => QuarantineStatus::Failed,
            'closed_at' => now()->toDateString(),
            'close_reason' => $data['close_reason'],
            'close_notes' => isset($data['close_notes']) ? trim($data['close_notes']) : null,
        ]);

        return redirect()
            ->route('quarantine.index')
            ->with('success', 'تم إنهاء حالة الحجر الصحي وتسجيلها ضمن الحالات التي لم تجتز الحجر.');
    }

    public function storeNote(
        Request $request,
        Quarantine $quarantine,
        QuarantineNotificationService $notifier
    ): RedirectResponse {
        $this->authorizeQuarantineAccess($quarantine);

        if (! $quarantine->isUnderFollowUp()) {
            return back()->with('error', 'لا يمكن إضافة ملاحظات إلا للحالات قيد المتابعة.');
        }

        if (! $request->user()->isVeterinarian()) {
            return back()->with('error', 'الملاحظات الصحية يضيفها الطبيب البيطري فقط.');
        }

        $data = $request->validate([
            'note' => ['required', 'string', 'max:2000'],
        ]);

        QuarantineNote::create([
            'quarantine_id' => $quarantine->id,
            'user_id' => $request->user()->id,
            'note' => $data['note'],
            'noted_at' => now(),
        ]);

        if (! $quarantine->responsibleVet?->isVeterinarian()) {
            $quarantine->update(['responsible_vet_id' => $request->user()->id]);
        }

        $quarantine->loadMissing('animal');
        $animal = $quarantine->animal;
        $vet = $request->user();
        $label = $animal->name ?: $animal->species;

        $notifier->notifyVetHead(
            $quarantine,
            'ملاحظة صحية جديدة',
            "سجّل د. {$vet->name} ملاحظة صحية على {$label} ({$quarantine->case_number}).",
            'quarantine_note_added'
        );

        return redirect()
            ->route('quarantine.index', ['open' => $quarantine->case_number])
            ->with('success', 'تم تسجيل الملاحظة الصحية في سجل المتابعة.');
    }

    public function storeVaccine(
        Request $request,
        Quarantine $quarantine,
        QuarantineNotificationService $notifier
    ): RedirectResponse {
        $this->authorizeQuarantineAccess($quarantine);

        if (! $quarantine->isUnderFollowUp()) {
            return back()->with('error', 'لا يمكن إضافة جرعات إلا للحالات قيد المتابعة.');
        }

        if (! $request->user()->isVeterinarian()) {
            return back()->with('error', 'الجرعات الوقائية يسجلها الطبيب البيطري فقط.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'administered_at' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        QuarantineVaccine::create([
            'quarantine_id' => $quarantine->id,
            'user_id' => $request->user()->id,
            'name' => $data['name'],
            'administered_at' => $data['administered_at'],
            'note' => $data['note'] ?? null,
        ]);

        if (! $quarantine->responsibleVet?->isVeterinarian()) {
            $quarantine->update(['responsible_vet_id' => $request->user()->id]);
        }

        $quarantine->loadMissing('animal');
        $animal = $quarantine->animal;
        $vet = $request->user();
        $label = $animal->name ?: $animal->species;

        $notifier->notifyVetHead(
            $quarantine,
            'جرعة وقائية جديدة',
            "سجّل د. {$vet->name} جرعة «{$data['name']}» للحيوان {$label} ({$quarantine->case_number}).",
            'quarantine_vaccine_added'
        );

        return redirect()
            ->route('quarantine.index', ['open' => $quarantine->case_number])
            ->with('success', 'تم تسجيل الجرعة الوقائية في سجل المتابعة.');
    }

    private function authorizeQuarantineAccess(Quarantine $quarantine): void
    {
        $quarantine->loadMissing('animal');
        $user = auth()->user();

        if (! $user || ! $user->managesAnimalGroup($quarantine->animal->group)) {
            abort(403, 'ليس لديك صلاحية إدارة حيوانات هذه المجموعة.');
        }
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Quarantine>  $quarantines
     * @return array<string, array<string, mixed>>
     */
    private function buildRecordsMap($quarantines, $doctorsByGroup): array
    {
        $records = [];

        foreach ($quarantines as $quarantine) {
            $animal = $quarantine->animal;
            $doctor = $quarantine->assignedDoctor($doctorsByGroup);

            $closeReason = $quarantine->close_reason;
            $closeNotes = $quarantine->close_notes;

            if (! $closeNotes && $closeReason && str_contains($closeReason, ' — ')) {
                [$closeReason, $closeNotes] = explode(' — ', $closeReason, 2);
            }

            $records[$quarantine->case_number] = [
                'caseNumber' => $quarantine->case_number,
                'type' => $animal->species,
                'code' => $animal->code,
                'animalName' => $animal->name ?? '',
                'mark' => $animal->distinguishing_marks ?? '',
                'gender' => $animal->gender,
                'group' => $animal->group,
                'source' => $animal->origin,
                'reason' => $quarantine->reason,
                'initialHealth' => $quarantine->initial_health_status,
                'vet' => $doctor?->name ?? '—',
                'vetRole' => $doctor?->role ?? null,
                'entryDate' => $quarantine->entry_date->format('Y-m-d'),
                'releasedAt' => $quarantine->released_at?->format('Y-m-d'),
                'closedAt' => $quarantine->closed_at?->format('Y-m-d'),
                'closeReason' => $closeReason,
                'closeNotes' => $closeNotes,
                'initialNotes' => $quarantine->initial_notes,
                'notes' => $quarantine->notes
                    ->map(fn ($note) => $note->noted_at->format('Y-m-d').' : '.$note->note)
                    ->values()
                    ->all(),
                'vaccines' => $quarantine->vaccines
                    ->map(function ($vaccine) {
                        $line = $vaccine->administered_at->format('Y-m-d').' — '.$vaccine->name;
                        if ($vaccine->author) {
                            $line .= ' ('.$vaccine->author->name.')';
                        }
                        if ($vaccine->note) {
                            $line .= ' — '.$vaccine->note;
                        }

                        return $line;
                    })
                    ->values()
                    ->all(),
                'age' => $this->formatAge($animal),
                'ageMethod' => $animal->age_method,
                'birthDate' => $animal->birth_date?->format('Y-m-d'),
                'approxAgeValue' => $animal->approx_age_value,
                'approxAgeUnit' => $animal->approx_age_unit,
                'status' => $quarantine->status->value,
                'statusLabel' => $quarantine->status->label(),
                'photoUrl' => $animal->photo_path ? Storage::url($animal->photo_path) : null,
            ];
        }

        return $records;
    }

    private function formatAge(Animal $animal): string
    {
        return $animal->formattedAge();
    }

    private function doctorsByGroup()
    {
        return User::query()
            ->where('status', 'active')
            ->where('role', UserRole::Veterinarian->value)
            ->whereNotNull('assigned_group')
            ->get()
            ->keyBy('assigned_group');
    }

    private function resolveResponsibleVetId(User $actor, string $group): ?int
    {
        if ($actor->isVeterinarian()) {
            return $actor->id;
        }

        return $this->doctorsByGroup()->get($group)?->id;
    }
}

