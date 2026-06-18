<?php



namespace App\Http\Controllers\Api;



use App\Enums\UserRole;

use App\Http\Controllers\Controller;

use App\Models\QuarantineNotification;

use App\Models\User;

use App\Models\VetNotification;

use App\Services\QuarantineNotificationService;

use App\Services\VetNotificationService;

use Illuminate\Http\JsonResponse;

use Illuminate\Http\Request;



class DoctorNotificationController extends Controller

{

    public function index(Request $request): JsonResponse

    {

        $vet = $this->veterinarianUser($request);



        $quarantineItems = QuarantineNotification::query()

            ->where('user_id', $vet->id)

            ->with(['quarantine:id,case_number'])

            ->latest()

            ->limit(50)

            ->get()

            ->map(fn (QuarantineNotification $notification) => [

                'id' => $notification->id,

                'type' => 'quarantine',

                'title' => $notification->title,

                'message' => $notification->message,

                'case_number' => $notification->quarantine?->case_number,

                'task_number' => null,

                'is_read' => $notification->read_at !== null,

                'read_at' => $notification->read_at?->toIso8601String(),

                'created_at' => $notification->created_at?->toIso8601String(),

            ]);



        $receivingItems = VetNotification::query()

            ->where('user_id', $vet->id)

            ->with(['receivingTask.quarantine:id,case_number'])

            ->latest()

            ->limit(50)

            ->get()

            ->map(fn (VetNotification $notification) => [

                'id' => $notification->id,

                'type' => 'receiving_delay',

                'title' => $notification->title,

                'message' => $notification->message,

                'case_number' => $notification->receivingTask?->quarantine?->case_number,

                'task_number' => $notification->receivingTask?->task_number,

                'is_read' => $notification->read_at !== null,

                'read_at' => $notification->read_at?->toIso8601String(),

                'created_at' => $notification->created_at?->toIso8601String(),

            ]);



        $notifications = $quarantineItems

            ->concat($receivingItems)

            ->sortByDesc('created_at')

            ->values()

            ->take(50);



        $unreadCount = QuarantineNotification::query()

            ->where('user_id', $vet->id)

            ->whereNull('read_at')

            ->count()

            + VetNotification::query()

                ->where('user_id', $vet->id)

                ->whereNull('read_at')

                ->count();



        return response()->json([

            'data' => $notifications,

            'unread_count' => $unreadCount,

        ]);

    }



    public function markRead(Request $request, QuarantineNotification $notification): JsonResponse

    {

        $vet = $this->veterinarianUser($request);



        if ($notification->user_id !== $vet->id) {

            abort(403, 'لا يمكنك تعديل هذا الإشعار.');

        }



        if ($notification->read_at === null) {

            $notification->update(['read_at' => now()]);

        }



        return response()->json(['ok' => true]);

    }



    public function markReceivingRead(Request $request, VetNotification $notification): JsonResponse

    {

        $vet = $this->veterinarianUser($request);

        app(VetNotificationService::class)->markAsReadForUser($notification, $vet);



        return response()->json(['ok' => true]);

    }



    public function markAllRead(Request $request): JsonResponse

    {

        $vet = $this->veterinarianUser($request);



        QuarantineNotification::query()

            ->where('user_id', $vet->id)

            ->whereNull('read_at')

            ->update(['read_at' => now()]);



        VetNotification::query()

            ->where('user_id', $vet->id)

            ->whereNull('read_at')

            ->update(['read_at' => now()]);



        return response()->json(['ok' => true]);

    }



    public function markReadByCase(Request $request, QuarantineNotificationService $notifier): JsonResponse

    {

        $vet = $this->veterinarianUser($request);



        $data = $request->validate([

            'case_number' => ['required', 'string', 'max:50'],

        ]);



        $quarantine = \App\Models\Quarantine::query()

            ->where('case_number', $data['case_number'])

            ->first();



        if ($quarantine) {

            $notifier->markQuarantineAsReadForUser($quarantine, $vet);

        }



        return response()->json(['ok' => true]);

    }



    private function veterinarianUser(Request $request): User

    {

        /** @var User $user */

        $user = $request->user();



        if ($user->role !== UserRole::Veterinarian->value || ! $user->assigned_group) {

            abort(403, 'هذا المسار مخصص للأطباء البيطريين المسندين لمجموعة.');

        }



        return $user;

    }

}

