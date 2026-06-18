<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\BirthRegistrationResource;
use App\Models\BirthRegistration;
use App\Models\User;
use App\Services\BirthRegistrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupervisorBirthRegistrationController extends Controller
{
    public function mothers(Request $request, BirthRegistrationService $service): JsonResponse
    {
        $supervisor = $this->supervisorUser($request);

        $animals = $service->mothersForSupervisor($supervisor)->map(fn ($animal) => [
            'id' => $animal->code,
            'name' => $animal->name ?? '',
            'type' => $animal->species,
            'label' => $animal->name
                ? "{$animal->code} — {$animal->name} ({$animal->species})"
                : "{$animal->code} — {$animal->species}",
        ]);

        return response()->json(['data' => $animals->values()]);
    }

    public function newborns(Request $request, BirthRegistrationService $service): JsonResponse
    {
        $supervisor = $this->supervisorUser($request);

        $animals = $service->newbornsForSupervisor($supervisor)->map(fn ($animal) => [
            'id' => $animal->code,
            'name' => $animal->name ?? '',
            'type' => $animal->species,
            'label' => $animal->name
                ? "{$animal->code} — {$animal->name} ({$animal->species})"
                : "{$animal->code} — {$animal->species}",
        ]);

        return response()->json(['data' => $animals->values()]);
    }

    public function index(Request $request): JsonResponse
    {
        $supervisor = $this->supervisorUser($request);

        $registrations = BirthRegistration::query()
            ->with(['mother', 'newborns'])
            ->where('supervisor_id', $supervisor->id)
            ->when($request->query('date'), function ($query, string $date) {
                $query->whereDate('created_at', $date);
            })
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => BirthRegistrationResource::collection($registrations),
        ]);
    }

    public function store(Request $request, BirthRegistrationService $service): JsonResponse
    {
        $supervisor = $this->supervisorUser($request);

        $data = $request->validate([
            'mother_code' => ['required', 'string', 'max:50'],
            'birth_date' => ['required', 'date'],
            'birth_count' => ['required', 'integer', 'min:1', 'max:10'],
            'newborns' => ['required'],
            'newborn_photos' => ['nullable', 'array', 'max:10'],
            'newborn_photos.*' => ['file', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ]);

        $newborns = $this->parseNewbornsPayload($data['newborns']);

        $newborns = Validator::make(
            ['newborns' => $newborns],
            [
                'newborns' => ['required', 'array', 'min:1', 'max:10'],
                'newborns.*.gender' => ['required', 'string', 'max:20'],
                'newborns.*.distinguishing_mark' => ['nullable', 'string', 'max:255'],
                'newborns.*.note' => ['nullable', 'string', 'max:2000'],
            ],
        )->validate()['newborns'];

        $birthCount = (int) $data['birth_count'];

        if (count($newborns) !== $birthCount) {
            return response()->json([
                'message' => 'عدد المواليد لا يطابق البيانات المدخلة.',
            ], 422);
        }

        for ($index = 0; $index < $birthCount; $index++) {
            $photo = $request->file("newborn_photos.{$index}")
                ?? $request->file("newborn_photos[{$index}]");

            if (! $photo) {
                return response()->json([
                    'message' => 'صورة المولود رقم '.($index + 1).' مطلوبة.',
                    'errors' => [
                        "newborn_photos.{$index}" => ['صورة المولود مطلوبة.'],
                    ],
                ], 422);
            }

            $newborns[$index]['photo_path'] = $photo->store('animals', 'public');
        }

        $mother = $service->findMotherForSupervisor($supervisor, $data['mother_code']);

        if (! $mother) {
            return response()->json([
                'message' => 'الأم غير موجودة في مجموعتك أو غير مؤهلة.',
            ], 422);
        }

        $registration = $service->register(
            $supervisor,
            $mother,
            $data['birth_date'],
            $newborns,
        );

        return response()->json([
            'message' => 'تم تسجيل الولادة وإرسالها لقسم الرعاية.',
            'data' => new BirthRegistrationResource($registration->load(['mother', 'newborns'])),
        ], 201);
    }

    private function supervisorUser(Request $request): User
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->role !== UserRole::GroupSupervisor->value || ! $user->assigned_group) {
            abort(403, 'هذا المسار مخصص لمشرفي المجموعات.');
        }

        return $user;
    }

    /** @return list<array<string, mixed>> */
    private function parseNewbornsPayload(mixed $payload): array
    {
        if (is_string($payload)) {
            $decoded = json_decode($payload, true);
            if (is_array($decoded)) {
                return array_values($decoded);
            }
        }

        if (is_array($payload)) {
            return array_values($payload);
        }

        return [];
    }
}
