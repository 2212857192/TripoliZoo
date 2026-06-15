<?php

use App\Enums\Portal;
use App\Http\Controllers\Admin\AnimalProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\MapLocationController;
use App\Http\Controllers\Admin\TicketSaleController;
use App\Http\Controllers\Admin\TicketTypeController;
use App\Http\Controllers\Admin\VisitInfoController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\VisitorAppController;
use Illuminate\Support\Facades\Route;

require_once app_path('Helpers/director.php');
require_once app_path('Helpers/animal_groups.php');

Route::get('/', fn () => view('welcome'));

Route::get('/app', [VisitorAppController::class, 'index'])->name('visitor.app');
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

        Route::get('/tickets', [TicketTypeController::class, 'index'])->name('tickets.index');
        Route::get('/tickets/create', [TicketTypeController::class, 'create'])->name('tickets.create');
        Route::get('/tickets/buy', [TicketSaleController::class, 'create'])->name('tickets.buy');
        Route::post('/tickets/buy', [TicketSaleController::class, 'store'])->name('tickets.buy.store');
        Route::get('/tickets/show/{sale}', [TicketSaleController::class, 'show'])->name('tickets.show');
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
    });

Route::prefix('vet')
    ->middleware(['auth', "portal:{$vetPortal}"])
    ->group(function () {
        Route::get('/dashboard', fn () => view('vet.dashboard'));
        Route::get('/employees', fn () => view('vet.employees.index'));
        Route::get('/quarantine', fn () => view('vet.quarantine'));
        Route::get('/quarantine/create', fn () => view('vet.quarantine.create'));
        Route::get('/quarantine/{id}', fn ($id) => view('vet.quarantine.show', compact('id')));
        Route::get('/cases/hospital', fn () => view('vet.cases.hospital'));
        Route::get('/cases/hospital/{id}', fn ($id) => view('vet.cases.hospital.show', compact('id')));
        Route::get('/cases/field', fn () => view('vet.cases.field'));
        Route::get('/cases/field/{id}', fn ($id) => view('vet.cases.field.show', compact('id')));
        Route::get('/referrals/treatment', fn () => view('vet.referrals.treatment'));
        Route::get('/referrals/treatment/{id}', fn ($id) => view('vet.referrals.treatment.show', compact('id')));
        Route::get('/referrals/autopsy', fn () => view('vet.referrals.autopsy'));
        Route::get('/referrals/autopsy/{id}', fn ($id) => view('vet.referrals.autopsy.show', compact('id')));
        Route::get('/decisions', fn () => view('vet.decisions.index'));
        Route::get('/decisions/{id}', fn ($id) => view('vet.decisions.show', compact('id')));
    });

Route::prefix('care')
    ->middleware(['auth', "portal:{$carePortal}"])
    ->group(function () {
        Route::get('/dashboard', fn () => view('care.dashboard'));
        Route::get('/groups', fn () => view('care.dashboard'));
        Route::get('/health', fn () => view('care.health.index'));
        Route::get('/births', fn () => view('care.births.index'));
        Route::get('/mortality', fn () => view('care.mortality.index'));
        Route::get('/notes', fn () => view('care.notes.index'));
        Route::get('/referrals/treatment', fn () => view('care.referrals.treatment'));
        Route::get('/referrals/autopsy', fn () => view('care.referrals.autopsy'));
        Route::get('/decisions', fn () => view('care.decisions.index'))->name('care.decisions.index');
        Route::get('/decisions/{id}', fn ($id) => view('care.decisions.show', compact('id')))->name('care.decisions.show');
    });

Route::prefix('records')
    ->middleware(['auth', "portal:{$recordsPortal}"])
    ->group(function () {
        Route::get('/dashboard', fn () => view('records.dashboard'));
        Route::get('/animals', fn () => view('records.animals.index'));
        Route::get('/animals/create', fn () => view('records.animals.create'));
        Route::get('/animals/{id}/edit', fn ($id) => view('records.animals.edit', compact('id')));
        Route::get('/animals/{id}', fn ($id) => view('records.animals.show', compact('id')));
        Route::get('/logs/births', fn () => view('records.logs.births'));
        Route::get('/logs/stillbirths', fn () => view('records.logs.stillbirths'));
        Route::get('/logs/entries', fn () => view('records.logs.entries'));
        Route::get('/logs/exits', fn () => view('records.logs.exits'));
        Route::get('/logs/mortality', fn () => view('records.logs.mortality'));
        Route::get('/logs/slaughter', fn () => view('records.logs.slaughter'));
    });

Route::prefix('director')
    ->middleware(['auth', "portal:{$directorPortal}"])
    ->group(function () {
        Route::get('/dashboard', fn () => view('director.dashboard'));

        Route::redirect('/admin/dashboard', '/director/admin/tickets');
        Route::redirect('/vet/dashboard', '/director/vet/cases/field');
        Route::redirect('/care/dashboard', '/director/care/health');
        Route::redirect('/records/dashboard', '/director/records/animals');

        Route::prefix('admin')->group(function () {
            Route::get('/employees', fn () => directorPage('admin.employees.index', app(EmployeeController::class)->index(request())->getData()));
            Route::get('/animals', fn () => directorPage('admin.animals.index', app(AnimalProfileController::class)->index(request())->getData()));
            Route::get('/map-locations', fn () => directorPage('admin.map-locations.index', app(MapLocationController::class)->index(request())->getData()));
            Route::get('/tickets', fn () => directorPage('admin.tickets.index', app(TicketTypeController::class)->index(request())->getData()));
            Route::get('/visit-info', fn () => directorPage('admin.visit-info.show', app(VisitInfoController::class)->show(request())->getData()));
        });

        Route::prefix('vet')->group(function () {
            Route::get('/quarantine', fn () => directorPage('vet.quarantine'));
            Route::get('/quarantine/{id}', fn ($id) => directorPage('vet.quarantine.show', compact('id')));
            Route::get('/cases/hospital', fn () => directorPage('vet.cases.hospital'));
            Route::get('/cases/hospital/{id}', fn ($id) => directorPage('vet.cases.hospital.show', compact('id')));
            Route::get('/cases/field', fn () => directorPage('vet.cases.field'));
            Route::get('/cases/field/{id}', fn ($id) => directorPage('vet.cases.field.show', compact('id')));
            Route::get('/referrals/treatment', fn () => directorPage('vet.referrals.treatment'));
            Route::get('/referrals/treatment/{id}', fn ($id) => directorPage('vet.referrals.treatment.show', compact('id')));
            Route::get('/referrals/autopsy', fn () => directorPage('vet.referrals.autopsy'));
            Route::get('/referrals/autopsy/{id}', fn ($id) => directorPage('vet.referrals.autopsy.show', compact('id')));
            Route::get('/decisions', fn () => directorPage('vet.decisions.index'));
            Route::get('/decisions/{id}', fn ($id) => directorPage('vet.decisions.show', compact('id')));
        });

        Route::prefix('care')->group(function () {
            Route::get('/health', fn () => directorPage('care.health.index'));
            Route::get('/births', fn () => directorPage('care.births.index'));
            Route::get('/mortality', fn () => directorPage('care.mortality.index'));
            Route::get('/notes', fn () => directorPage('care.notes.index'));
            Route::get('/referrals/treatment', fn () => directorPage('care.referrals.treatment'));
            Route::get('/referrals/autopsy', fn () => directorPage('care.referrals.autopsy'));
            Route::get('/decisions', fn () => directorPage('care.decisions.index'));
            Route::get('/decisions/{id}', fn ($id) => directorPage('care.decisions.show', compact('id')));
        });

        Route::prefix('records')->group(function () {
            Route::get('/animals', fn () => directorPage('records.animals.index'));
            Route::get('/logs/births', fn () => directorPage('records.logs.births'));
            Route::get('/logs/stillbirths', fn () => directorPage('records.logs.stillbirths'));
            Route::get('/logs/entries', fn () => directorPage('records.logs.entries'));
            Route::get('/logs/exits', fn () => directorPage('records.logs.exits'));
            Route::get('/logs/mortality', fn () => directorPage('records.logs.mortality'));
            Route::get('/logs/slaughter', fn () => directorPage('records.logs.slaughter'));
        });
    });
