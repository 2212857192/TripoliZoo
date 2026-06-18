<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AuthPasswordController;
use App\Http\Controllers\Api\DeviceTokenController;
use App\Http\Controllers\Api\DoctorDashboardController;
use App\Http\Controllers\Api\DoctorHealthReportController;
use App\Http\Controllers\Api\DoctorMedicalCaseController;
use App\Http\Controllers\Api\DoctorNotificationController;
use App\Http\Controllers\Api\DoctorQuarantineController;
use App\Http\Controllers\Api\HealthCaseAttachmentController;
use App\Http\Controllers\Api\HealthReportAttachmentController;
use App\Http\Controllers\Api\MortalityCaseAttachmentController;
use App\Http\Controllers\Api\OperationalNoteAttachmentController;
use App\Http\Controllers\Api\SupervisorBirthRegistrationController;
use App\Http\Controllers\Api\SupervisorMortalityCaseController;
use App\Http\Controllers\Api\SupervisorOperationalNoteController;
use App\Http\Controllers\Api\SupervisorHealthCaseController;
use App\Http\Controllers\Api\ReceivingTaskController;
use App\Http\Controllers\Api\SupervisorDashboardController;
use App\Http\Controllers\Api\SupervisorHealthReportController;
use App\Http\Controllers\Api\SupervisorNotificationController;
use App\Http\Controllers\Api\VisitorAnimalController;
use App\Http\Controllers\Api\VisitorMapController;
use App\Http\Controllers\Api\VisitorTicketController;
use App\Http\Controllers\Api\VisitorVisitInfoController;
use Illuminate\Support\Facades\Route;

Route::get('/animals', [VisitorAnimalController::class, 'index']);
Route::get('/animals/{identifier}', [VisitorAnimalController::class, 'show']);
Route::get('/map', [VisitorMapController::class, 'show']);
Route::get('/visit-info', [VisitorVisitInfoController::class, 'show']);
Route::get('/ticket-types', [VisitorTicketController::class, 'types']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tickets', [VisitorTicketController::class, 'mine']);
    Route::post('/tickets/purchase/cash', [VisitorTicketController::class, 'purchaseCash']);
    Route::post('/tickets/purchase/electronic/verify', [VisitorTicketController::class, 'verifyElectronicPayment']);
    Route::post('/tickets/purchase/electronic/confirm', [VisitorTicketController::class, 'confirmElectronicPayment']);
    Route::post('/tickets/purchase', [VisitorTicketController::class, 'purchase']);
    Route::get('/tickets/{ticketNumber}', [VisitorTicketController::class, 'show']);
});

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthPasswordController::class, 'sendCode']);
    Route::post('/verify-otp', [AuthPasswordController::class, 'verifyCode']);
    Route::post('/reset-password', [AuthPasswordController::class, 'resetPassword']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::post('/device-tokens', [DeviceTokenController::class, 'store']);
        Route::delete('/device-tokens', [DeviceTokenController::class, 'destroy']);

        Route::get('/supervisor/dashboard', [SupervisorDashboardController::class, 'show']);
        Route::get('/supervisor/notifications', [SupervisorNotificationController::class, 'index']);
        Route::post('/supervisor/notifications/read-all', [SupervisorNotificationController::class, 'markAllRead']);
        Route::post('/supervisor/notifications/{notification}/read', [SupervisorNotificationController::class, 'markRead']);
        Route::post('/supervisor/notifications/health/{notification}/read', [SupervisorNotificationController::class, 'markHealthReportRead']);
        Route::get('/supervisor/animals', [SupervisorHealthReportController::class, 'animals']);
        Route::get('/supervisor/health-reports', [SupervisorHealthReportController::class, 'index']);
        Route::post('/supervisor/health-reports', [SupervisorHealthReportController::class, 'store']);
        Route::get('/supervisor/health-reports/{healthReport}', [SupervisorHealthReportController::class, 'show']);
        Route::get('/supervisor/health-cases', [SupervisorHealthCaseController::class, 'index']);
        Route::post('/supervisor/health-cases', [SupervisorHealthCaseController::class, 'store']);
        Route::get('/supervisor/health-cases/{healthCase}', [SupervisorHealthCaseController::class, 'show']);
        Route::get('/supervisor/mortality-cases', [SupervisorMortalityCaseController::class, 'index']);
        Route::post('/supervisor/mortality-cases', [SupervisorMortalityCaseController::class, 'store']);
        Route::get('/supervisor/operational-notes', [SupervisorOperationalNoteController::class, 'index']);
        Route::post('/supervisor/operational-notes', [SupervisorOperationalNoteController::class, 'store']);
        Route::get('/supervisor/animals/mothers', [SupervisorBirthRegistrationController::class, 'mothers']);
        Route::get('/supervisor/animals/newborns', [SupervisorBirthRegistrationController::class, 'newborns']);
        Route::get('/supervisor/birth-registrations', [SupervisorBirthRegistrationController::class, 'index']);
        Route::post('/supervisor/birth-registrations', [SupervisorBirthRegistrationController::class, 'store']);
        Route::get('/health-cases/{healthCase}/attachment', [HealthCaseAttachmentController::class, 'show']);
        Route::get('/mortality-cases/{mortalityCase}/attachment', [MortalityCaseAttachmentController::class, 'show']);
        Route::get('/operational-notes/{operationalNote}/attachment', [OperationalNoteAttachmentController::class, 'show']);
        Route::get('/doctor/dashboard', [DoctorDashboardController::class, 'show']);
        Route::get('/doctor/notifications', [DoctorNotificationController::class, 'index']);
        Route::post('/doctor/notifications/read-all', [DoctorNotificationController::class, 'markAllRead']);
        Route::post('/doctor/notifications/read-by-case', [DoctorNotificationController::class, 'markReadByCase']);
        Route::post('/doctor/notifications/{notification}/read', [DoctorNotificationController::class, 'markRead']);
        Route::post('/doctor/notifications/receiving/{notification}/read', [DoctorNotificationController::class, 'markReceivingRead']);
        Route::get('/doctor/quarantines', [DoctorQuarantineController::class, 'index']);
        Route::get('/doctor/quarantines/{quarantine}', [DoctorQuarantineController::class, 'show']);
        Route::post('/doctor/quarantines/{quarantine}/notes', [DoctorQuarantineController::class, 'storeNote']);
        Route::post('/doctor/quarantines/{quarantine}/vaccines', [DoctorQuarantineController::class, 'storeVaccine']);
        Route::post('/doctor/quarantines/{quarantine}/release', [DoctorQuarantineController::class, 'release']);
        Route::post('/doctor/quarantines/{quarantine}/close', [DoctorQuarantineController::class, 'close']);
        Route::get('/doctor/animals', [DoctorMedicalCaseController::class, 'animals']);
        Route::get('/doctor/cases', [DoctorMedicalCaseController::class, 'index']);
        Route::get('/doctor/cases/{caseKey}', [DoctorMedicalCaseController::class, 'show'])
            ->where('caseKey', '.+');
        Route::post('/doctor/cases/{caseKey}/procedures', [DoctorMedicalCaseController::class, 'storeProcedure'])
            ->where('caseKey', '.+');
        Route::post('/doctor/cases/{caseKey}/close', [DoctorMedicalCaseController::class, 'closeFieldCase'])
            ->where('caseKey', '.+');
        Route::post('/doctor/field-cases', [DoctorMedicalCaseController::class, 'storeFieldCase']);
        Route::get('/doctor/health-reports', [DoctorHealthReportController::class, 'index']);
        Route::get('/doctor/health-reports/{healthReport}', [DoctorHealthReportController::class, 'show']);
        Route::post('/doctor/health-reports/{healthReport}/close', [DoctorHealthReportController::class, 'close']);
        Route::get('/health-reports/{healthReport}/attachment', [HealthReportAttachmentController::class, 'show']);
        Route::get('/receiving-tasks', [ReceivingTaskController::class, 'index']);
        Route::get('/receiving-tasks/{receivingTask}', [ReceivingTaskController::class, 'show']);
        Route::post('/receiving-tasks/{receivingTask}/confirm', [ReceivingTaskController::class, 'confirm']);
        Route::post('/receiving-tasks/{receivingTask}/delay', [ReceivingTaskController::class, 'delay']);
    });
});

Route::get('/storage/{path}', function ($path) {
    $filePath = 'public/' . $path;
    if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($filePath) && !\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
        abort(404);
    }

    $disk = \Illuminate\Support\Facades\Storage::disk('public')->exists($path) ? 'public' : 'local';
    $fullPath = $disk === 'public' ? $path : $filePath;

    $file = \Illuminate\Support\Facades\Storage::disk($disk)->get($fullPath);
    $type = \Illuminate\Support\Facades\Storage::disk($disk)->mimeType($fullPath);

    return response($file, 200)
        ->header('Content-Type', $type)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, HEAD, OPTIONS');
})->where('path', '.*');
