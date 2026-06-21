<?php

use App\Enums\Portal;
use App\Http\Controllers\Records\RecordsAnimalController;
use App\Http\Controllers\Records\RecordsDashboardController;
use App\Http\Controllers\Records\RecordsBirthLogController;
use App\Http\Controllers\Records\RecordsEntryLogController;
use App\Http\Controllers\Records\RecordsExitLogController;
use App\Http\Controllers\Records\RecordsMortalityLogController;
use App\Http\Controllers\Records\RecordsSlaughterLogController;
use App\Http\Controllers\Records\RecordsStillbirthLogController;
use App\Http\Controllers\Director\DirectorDashboardController;
use App\Http\Controllers\Admin\AnimalProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\MapLocationController;
use App\Http\Controllers\Admin\TicketTypeController;
use App\Http\Controllers\Admin\VisitInfoController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Care\CareDashboardController;
use App\Http\Controllers\Care\CareNotificationController;
use App\Http\Controllers\Care\HealthCaseController;
use App\Http\Controllers\Care\AutopsyReferralController as CareAutopsyReferralController;
use App\Http\Controllers\Care\BirthRegistrationController;
use App\Http\Controllers\Care\MortalityCaseController;
use App\Http\Controllers\Care\OperationalNoteController;
use App\Http\Controllers\Care\MedicalDecisionController;
use App\Http\Controllers\Vet\QuarantineController;
use App\Http\Controllers\Vet\VetDashboardController;
use App\Http\Controllers\Care\TreatmentReferralController as CareTreatmentReferralController;
use App\Http\Controllers\Vet\AutopsyReferralController as VetAutopsyReferralController;
use App\Http\Controllers\Vet\FieldCaseController;
use App\Http\Controllers\Vet\HospitalCaseController;
use App\Http\Controllers\Vet\TreatmentReferralController as VetTreatmentReferralController;
use App\Http\Controllers\Vet\VetNotificationController;
use App\Http\Controllers\VisitorAppController;
use App\Models\HospitalCase;
use App\Models\Quarantine;
use App\Models\ReceivingTask;
use App\Services\AdminAnimalProfileService;
use App\Services\AnimalCodeGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

require_once app_path('Helpers/director.php');
require_once app_path('Helpers/animal_groups.php');

Route::get('/', fn () => view('welcome'));

Route::get('/app', [VisitorAppController::class, 'index'])->name('visitor.app');
Route::get('/app/map', [VisitorAppController::class, 'map'])->name('visitor.map');
Route::get('/app/visit-info', [VisitorAppController::class, 'visitInfo'])->name('visitor.visit-info');
Route::get('/app/animals/{profile}', [VisitorAppController::class, 'profile'])->name('visitor.animal');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);

    Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');
    Route::get('/verify-reset-code', [ForgotPasswordController::class, 'verifyForm'])->name('password.verify');
    Route::post('/verify-reset-code', [ForgotPasswordController::class, 'verify'])->name('password.verify.submit');
    Route::post('/resend-reset-code', [ForgotPasswordController::class, 'resend'])->name('password.resend');
    Route::get('/reset-password', [ForgotPasswordController::class, 'resetForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::post('/account/password', [PasswordController::class, 'update'])
    ->middleware('auth')
    ->name('account.password.update');

$adminPortal = Portal::Admin->value;
$directorPortal = Portal::Director->value;
$carePortal = Portal::Care->value;
$vetPortal = Portal::Vet->value;
$recordsPortal = Portal::Records->value;

Route::prefix('admin')
    ->middleware(['auth', "portal:{$adminPortal}"])
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
        Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
        Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::patch('/employees/{employee}/toggle', [EmployeeController::class, 'toggleStatus'])->name('employees.toggle');

        Route::get('/animals', [AnimalProfileController::class, 'index'])->name('animals.index');
        Route::get('/animals/create', [AnimalProfileController::class, 'create'])->name('animals.create');
        Route::post('/animals', [AnimalProfileController::class, 'store'])->name('animals.store');
        Route::get('/animals/{profile}', [AnimalProfileController::class, 'show'])->name('animals.show');
        Route::get('/animals/{profile}/edit', [AnimalProfileController::class, 'edit'])->name('animals.edit');
        Route::put('/animals/{profile}', [AnimalProfileController::class, 'update'])->name('animals.update');
        Route::patch('/animals/{profile}/visibility', [AnimalProfileController::class, 'toggleVisibility'])->name('animals.visibility');
        Route::get('/animals/{profile}/qr', [AnimalProfileController::class, 'qrImage'])->name('animals.qr');

        Route::get('/tickets', [TicketTypeController::class, 'index'])->name('tickets.index');
        Route::get('/tickets/create', [TicketTypeController::class, 'create'])->name('tickets.create');
        Route::post('/tickets', [TicketTypeController::class, 'store'])->name('tickets.store');
        Route::get('/tickets/{ticket}/edit', [TicketTypeController::class, 'edit'])->name('tickets.edit');
        Route::put('/tickets/{ticket}', [TicketTypeController::class, 'update'])->name('tickets.update');
        Route::patch('/tickets/{ticket}/toggle', [TicketTypeController::class, 'toggle'])->name('tickets.toggle');

        Route::get('/visit-info', [VisitInfoController::class, 'show'])->name('visit-info.show');
        Route::get('/visit-info/edit', [VisitInfoController::class, 'edit'])->name('visit-info.edit');
        Route::put('/visit-info', [VisitInfoController::class, 'update'])->name('visit-info.update');

        Route::get('/map-locations', [MapLocationController::class, 'index'])->name('map-locations.index');
        Route::get('/map-locations/create', [MapLocationController::class, 'create'])->name('map-locations.create');
        Route::post('/map-locations', [MapLocationController::class, 'store'])->name('map-locations.store');
        Route::get('/map-locations/{mapLocation}/edit', [MapLocationController::class, 'edit'])->name('map-locations.edit');
        Route::put('/map-locations/{mapLocation}', [MapLocationController::class, 'update'])->name('map-locations.update');
        Route::patch('/map-locations/{mapLocation}/toggle', [MapLocationController::class, 'toggle'])->name('map-locations.toggle');
        Route::delete('/map-locations/{mapLocation}', [MapLocationController::class, 'destroy'])->name('map-locations.destroy');
    });

Route::prefix('vet')
    ->middleware(['auth', "portal:{$vetPortal}"])
    ->group(function () {
        Route::get('/dashboard', [VetDashboardController::class, 'index']);
        Route::get('/employees', fn () => view('vet.employees.index'));
        Route::get('/quarantine', [QuarantineController::class, 'index'])->name('quarantine.index');
        Route::post('/quarantine', [QuarantineController::class, 'store'])->name('quarantine.store');
        Route::put('/quarantine/{quarantine}', [QuarantineController::class, 'update'])->name('quarantine.update');
        Route::post('/quarantine/notifications/read-all', [QuarantineController::class, 'markAllNotificationsRead'])->name('quarantine.notifications.read-all');
        Route::post('/quarantine/notifications/read', [QuarantineController::class, 'markNotificationReadByCase'])->name('quarantine.notification.read-case');
        Route::post('/quarantine/{quarantine}/release', [QuarantineController::class, 'release'])->name('quarantine.release');
        Route::post('/quarantine/{quarantine}/close', [QuarantineController::class, 'close'])->name('quarantine.close');
        Route::post('/quarantine/{quarantine}/notes', [QuarantineController::class, 'storeNote'])->name('quarantine.notes.store');
        Route::post('/quarantine/{quarantine}/vaccines', [QuarantineController::class, 'storeVaccine'])->name('quarantine.vaccines.store');
        Route::post('/quarantine/{quarantine}/read-notification', [QuarantineController::class, 'markNotificationRead'])->name('quarantine.notification.read');
        Route::get('/quarantine/create', fn () => view('vet.quarantine.create'));
        Route::get('/cases/hospital', [HospitalCaseController::class, 'index'])->name('vet.cases.hospital.index');
        Route::get('/cases/hospital/{hospitalCase}', [HospitalCaseController::class, 'show'])->name('vet.cases.hospital.show');
        Route::post('/cases/hospital/{hospitalCase}/approve-discharge', [HospitalCaseController::class, 'approveDischarge'])->name('vet.cases.hospital.approve-discharge');
        Route::post('/cases/hospital/{hospitalCase}/approve-slaughter', [HospitalCaseController::class, 'approveSlaughter'])->name('vet.cases.hospital.approve-slaughter');
        Route::post('/cases/hospital/{hospitalCase}/issue-decision', [HospitalCaseController::class, 'issueDecision'])->name('vet.cases.hospital.issue-decision');
        Route::get('/cases/field', [FieldCaseController::class, 'index'])->name('vet.cases.field.index');
        Route::get('/cases/field/{fieldCase}', [FieldCaseController::class, 'show'])->name('vet.cases.field.show');
        Route::get('/referrals/treatment', [VetTreatmentReferralController::class, 'index'])->name('vet.referrals.treatment.index');
        Route::post('/referrals/treatment/{treatmentReferral}/approve', [VetTreatmentReferralController::class, 'approve'])->name('vet.referrals.treatment.approve');
        Route::post('/referrals/treatment/{treatmentReferral}/reject', [VetTreatmentReferralController::class, 'reject'])->name('vet.referrals.treatment.reject');
        Route::post('/notifications/treatment-referral/read', [VetNotificationController::class, 'markReadByReferral'])->name('vet.referrals.treatment.notification.read');
        Route::get('/referrals/treatment/{id}', fn ($id) => view('vet.referrals.treatment.show', compact('id')));
        Route::get('/referrals/autopsy', [VetAutopsyReferralController::class, 'index'])->name('vet.referrals.autopsy.index');
        Route::post('/referrals/autopsy/{autopsyReferral}/document', [VetAutopsyReferralController::class, 'document'])->name('vet.referrals.autopsy.document');
        Route::get('/referrals/autopsy/{autopsyReferral}/report', [VetAutopsyReferralController::class, 'report'])->name('vet.referrals.autopsy.report');
        Route::get('/referrals/autopsy/{autopsyReferral}', [VetAutopsyReferralController::class, 'show'])->name('vet.referrals.autopsy.show');
        Route::post('/notifications/autopsy-referral/read', [VetNotificationController::class, 'markReadByAutopsyReferral'])->name('vet.referrals.autopsy.notification.read');
        Route::post('/notifications/hospital/{hospitalCase}/read', [VetNotificationController::class, 'markHospitalCaseRead'])->name('vet.hospital.notification.read');
        Route::get('/decisions', fn () => app(MedicalDecisionController::class)->index(portal: 'vet'))->name('vet.decisions.index');
        Route::get('/decisions/{receivingTask}', fn (ReceivingTask $receivingTask) => app(MedicalDecisionController::class)->show($receivingTask, portal: 'vet'))->name('vet.decisions.show');
        Route::post('/notifications/read', [VetNotificationController::class, 'markReadByTask'])->name('vet.notification.read');
        Route::get('/notifications/feed', [VetNotificationController::class, 'feed'])->name('vet.notifications.feed');
        Route::post('/notifications/read-all', [VetNotificationController::class, 'markAllRead'])->name('vet.notifications.read-all');
    });

Route::prefix('care')
    ->middleware(['auth', "portal:{$carePortal}"])
    ->group(function () {
        Route::get('/dashboard', [CareDashboardController::class, 'index']);
        Route::get('/groups', [CareDashboardController::class, 'index']);
        Route::get('/health', [HealthCaseController::class, 'index'])->name('care.health.index');
        Route::post('/health/{healthCase}/review', [HealthCaseController::class, 'review'])->name('care.health.review');
        Route::post('/health/{healthCase}/refer', [HealthCaseController::class, 'refer'])->name('care.health.refer');
        Route::get('/health/{healthCase}/attachment', [HealthCaseController::class, 'attachment'])->name('care.health.attachment');
        Route::post('/notifications/health/{healthCase}/read', [HealthCaseController::class, 'markNotificationRead'])->name('care.health.notification.read');
        Route::get('/births', [BirthRegistrationController::class, 'index'])->name('care.births.index');
        Route::get('/births/{animal}/photo', [BirthRegistrationController::class, 'photo'])->name('care.births.photo');
        Route::post('/notifications/birth/{birthRegistration}/read', [BirthRegistrationController::class, 'markNotificationRead'])->name('care.births.notification.read');
        Route::get('/mortality', [MortalityCaseController::class, 'index'])->name('care.mortality.index');
        Route::post('/mortality/{mortalityCase}/approve', [MortalityCaseController::class, 'approve'])->name('care.mortality.approve');
        Route::post('/mortality/{mortalityCase}/refer-autopsy', [MortalityCaseController::class, 'referForAutopsy'])->name('care.mortality.refer-autopsy');
        Route::get('/mortality/{mortalityCase}/attachment', [MortalityCaseController::class, 'attachment'])->name('care.mortality.attachment');
        Route::post('/notifications/mortality/{mortalityCase}/read', [MortalityCaseController::class, 'markNotificationRead'])->name('care.mortality.notification.read');
        Route::get('/notes', [OperationalNoteController::class, 'index'])->name('care.notes.index');
        Route::post('/notes/{operationalNote}/review', [OperationalNoteController::class, 'review'])->name('care.notes.review');
        Route::get('/notes/{operationalNote}/attachment', [OperationalNoteController::class, 'attachment'])->name('care.notes.attachment');
        Route::post('/notifications/operational-note/{operationalNote}/read', [OperationalNoteController::class, 'markNotificationRead'])->name('care.notes.notification.read');
        Route::get('/referrals/treatment', [CareTreatmentReferralController::class, 'index'])->name('care.referrals.treatment.index');
        Route::get('/referrals/autopsy', [CareAutopsyReferralController::class, 'index'])->name('care.referrals.autopsy.index');
        Route::get('/decisions', [MedicalDecisionController::class, 'index'])->name('care.decisions.index');
        Route::get('/decisions/slaughter/{hospitalCase}', [MedicalDecisionController::class, 'showSlaughter'])->name('care.decisions.slaughter.show');
        Route::get('/decisions/{receivingTask}', [MedicalDecisionController::class, 'show'])->name('care.decisions.show');
        Route::post('/notifications/read', [CareNotificationController::class, 'markReadByTask'])->name('care.notification.read');
        Route::get('/notifications/feed', [CareNotificationController::class, 'feed'])->name('care.notifications.feed');
        Route::post('/notifications/read-all', [CareNotificationController::class, 'markAllRead'])->name('care.notifications.read-all');
    });

Route::prefix('records')
    ->middleware(['auth', "portal:{$recordsPortal}"])
    ->group(function () {
        Route::get('/dashboard', [RecordsDashboardController::class, 'index'])->name('records.dashboard');
        Route::get('/animals', [RecordsAnimalController::class, 'index'])->name('records.animals.index');
        Route::get('/animals/create', [RecordsAnimalController::class, 'create'])->name('records.animals.create');
        Route::post('/animals', [RecordsAnimalController::class, 'store'])->name('records.animals.store');
        Route::get('/animals/{animal}/edit', [RecordsAnimalController::class, 'edit'])->name('records.animals.edit');
        Route::put('/animals/{animal}', [RecordsAnimalController::class, 'update'])->name('records.animals.update');
        Route::get('/animals/{animal}/export', [RecordsAnimalController::class, 'export'])->name('records.animals.export');
        Route::post('/animals/{animal}/exit', [RecordsAnimalController::class, 'exit'])->name('records.animals.exit');
        Route::get('/animals/{animal}', [RecordsAnimalController::class, 'show'])->name('records.animals.show');
        Route::get('/logs/births', [RecordsBirthLogController::class, 'index'])->name('records.logs.births');
        Route::get('/logs/stillbirths', [RecordsStillbirthLogController::class, 'index'])->name('records.logs.stillbirths');
        Route::get('/logs/entries', [RecordsEntryLogController::class, 'index'])->name('records.logs.entries');
        Route::get('/logs/exits', [RecordsExitLogController::class, 'index'])->name('records.logs.exits');
        Route::get('/logs/mortality', [RecordsMortalityLogController::class, 'index'])->name('records.logs.mortality');
        Route::get('/logs/slaughter', [RecordsSlaughterLogController::class, 'index'])->name('records.logs.slaughter');
    });

Route::prefix('director')
    ->middleware(['auth', "portal:{$directorPortal}"])
    ->group(function () {
        Route::get('/dashboard', [DirectorDashboardController::class, 'index']);

        Route::redirect('/admin/dashboard', '/director/admin/tickets');
        Route::redirect('/vet/dashboard', '/director/vet/cases/field');
        Route::redirect('/care/dashboard', '/director/care/health');
        Route::redirect('/records/dashboard', '/director/records/animals');

        Route::prefix('admin')->group(function () {
            Route::get('/employees', fn () => directorPage('admin.employees.index', app(EmployeeController::class)->index(request())->getData()));
            Route::get('/animals', fn (Request $request) => directorPage(
                'admin.animals.index',
                app(AdminAnimalProfileService::class)->indexViewData($request, readOnly: true),
            ));
            Route::get('/map-locations', fn () => directorPage('admin.map-locations.index', app(MapLocationController::class)->index(request())->getData()));
            Route::get('/tickets', fn () => directorPage('admin.tickets.index', app(TicketTypeController::class)->index(request())->getData()));
            Route::get('/visit-info', fn () => directorPage('admin.visit-info.show', app(VisitInfoController::class)->show(request())->getData()));
        });

        Route::prefix('vet')->group(function () {
            Route::get('/quarantine', fn () => app(QuarantineController::class)->index(readOnly: true, layout: 'director.layout'));
            Route::get('/quarantine/{quarantine}', fn (Quarantine $quarantine) => redirect('/director/vet/quarantine?open='.$quarantine->case_number));
            Route::get('/cases/hospital', [HospitalCaseController::class, 'directorIndex']);
            Route::get('/cases/hospital/{hospitalCase}', [HospitalCaseController::class, 'directorShow']);
            Route::get('/cases/field', [FieldCaseController::class, 'directorIndex']);
            Route::get('/cases/field/{fieldCase}', [FieldCaseController::class, 'directorShow']);
            Route::get('/referrals/treatment', [VetTreatmentReferralController::class, 'directorIndex']);
            Route::get('/referrals/treatment/{id}', fn ($id) => directorPage('vet.referrals.treatment.show', compact('id')));
            Route::get('/referrals/autopsy', [VetAutopsyReferralController::class, 'directorIndex']);
            Route::get('/referrals/autopsy/{autopsyReferral}', [VetAutopsyReferralController::class, 'directorShow']);
            Route::get('/decisions', fn () => app(MedicalDecisionController::class)->index(readOnly: true, layout: 'director.layout', portal: 'vet'));
            Route::get('/decisions/{receivingTask}', fn (ReceivingTask $receivingTask) => app(MedicalDecisionController::class)->show($receivingTask, readOnly: true, layout: 'director.layout', portal: 'vet'));
        });

        Route::prefix('care')->group(function () {
            Route::get('/health', [HealthCaseController::class, 'directorIndex']);
            Route::get('/births', [BirthRegistrationController::class, 'directorIndex']);
            Route::get('/births/{animal}/photo', [BirthRegistrationController::class, 'directorPhoto']);
            Route::get('/mortality', [MortalityCaseController::class, 'directorIndex']);
            Route::get('/notes', [OperationalNoteController::class, 'directorIndex']);
            Route::get('/referrals/treatment', [CareTreatmentReferralController::class, 'directorIndex']);
            Route::get('/referrals/autopsy', [CareAutopsyReferralController::class, 'directorIndex']);
            Route::get('/decisions', fn () => app(MedicalDecisionController::class)->index(readOnly: true, layout: 'director.layout'));
            Route::get('/decisions/slaughter/{hospitalCase}', fn (HospitalCase $hospitalCase) => app(MedicalDecisionController::class)->showSlaughter($hospitalCase, readOnly: true, layout: 'director.layout'));
            Route::get('/decisions/{receivingTask}', fn (ReceivingTask $receivingTask) => app(MedicalDecisionController::class)->show($receivingTask, readOnly: true, layout: 'director.layout'));
        });

        Route::prefix('records')->group(function () {
            Route::get('/animals', [RecordsAnimalController::class, 'directorIndex']);
            Route::get('/animals/{animal}', [RecordsAnimalController::class, 'directorShow']);
            Route::get('/logs/births', [RecordsBirthLogController::class, 'directorIndex']);
            Route::get('/logs/stillbirths', [RecordsStillbirthLogController::class, 'directorIndex']);
            Route::get('/logs/entries', [RecordsEntryLogController::class, 'directorIndex']);
            Route::get('/logs/exits', [RecordsExitLogController::class, 'directorIndex']);
            Route::get('/logs/mortality', [RecordsMortalityLogController::class, 'directorIndex']);
            Route::get('/logs/slaughter', [RecordsSlaughterLogController::class, 'directorIndex']);
        });
    });
