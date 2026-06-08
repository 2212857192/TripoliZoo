<?php

use Illuminate\Support\Facades\Route;

require_once app_path('Helpers/director.php');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('login');
});

Route::prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    });
    Route::get('/employees', function () {
        return view('admin.employees.index');
    });

    // Animals
    Route::get('/animals', function () {
        return view('admin.animals.index');
    });
    Route::get('/animals/create', function () {
        return view('admin.animals.create');
    });
    Route::get('/animals/{id}', function ($id) {
        return view('admin.animals.show', ['id' => $id]);
    });
    Route::get('/animals/{id}/edit', function ($id) {
        return view('admin.animals.edit', ['id' => $id]);
    });

    // Tickets
    Route::get('/tickets', function () {
        return view('admin.tickets.index');
    });
    Route::get('/tickets/create', function () {
        return view('admin.tickets.create');
    });
    Route::get('/tickets/{id}/edit', function ($id) {
        return view('admin.tickets.edit', ['id' => $id]);
    });
    Route::get('/tickets/buy', function () {
        return view('admin.tickets.buy');
    });
    Route::get('/tickets/show/{id}', function ($id) {
        return view('admin.tickets.show', ['id' => $id]);
    });

    // Visit Info
    Route::get('/visit-info', function () {
        return view('admin.visit-info.show');
    });
    Route::get('/visit-info/edit', function () {
        return view('admin.visit-info.edit');
    });

    // Map Locations
    Route::get('/map-locations', function () {
        return view('admin.map-locations.index');
    });
    Route::get('/map-locations/create', function () {
        return view('admin.map-locations.create');
    });
    Route::get('/map-locations/{id}/edit', function ($id) {
        return view('admin.map-locations.edit', ['id' => $id]);
    });
});

Route::prefix('vet')->group(function () {
    Route::get('/dashboard', function () {
        return view('vet.dashboard');
    });
    Route::get('/employees', function () {
        return view('vet.employees.index');
    });
    Route::get('/quarantine', function () {
        return view('vet.quarantine');
    });
    Route::get('/quarantine/create', function () {
        return view('vet.quarantine.create');
    });
    Route::get('/quarantine/{id}', function ($id) {
        return view('vet.quarantine.show', ['id' => $id]);
    });
    Route::get('/cases/hospital', function () {
        return view('vet.cases.hospital');
    });
    Route::get('/cases/hospital/{id}', function ($id) {
        return view('vet.cases.hospital.show', ['id' => $id]);
    });
    Route::get('/cases/field', function () {
        return view('vet.cases.field');
    });
    Route::get('/cases/field/{id}', function ($id) {
        return view('vet.cases.field.show', ['id' => $id]);
    });
    Route::get('/referrals/treatment', function () {
        return view('vet.referrals.treatment');
    });
    Route::get('/referrals/treatment/{id}', function ($id) {
        return view('vet.referrals.treatment.show', ['id' => $id]);
    });
    Route::get('/referrals/autopsy', function () {
        return view('vet.referrals.autopsy');
    });
    Route::get('/referrals/autopsy/{id}', function ($id) {
        return view('vet.referrals.autopsy.show', ['id' => $id]);
    });
    Route::get('/decisions', function () {
        return view('vet.decisions.index');
    });
    Route::get('/decisions/{id}', function ($id) {
        return view('vet.decisions.show', ['id' => $id]);
    });
});

Route::prefix('care')->group(function () {
    Route::get('/dashboard', function () { return view('care.dashboard'); });
    Route::get('/groups', function () { return view('care.dashboard'); });
    Route::get('/health', function () { return view('care.health.index'); });
    Route::get('/births', function () { return view('care.births.index'); });
    Route::get('/mortality', function () { return view('care.mortality.index'); });
    Route::get('/notes', function () { return view('care.notes.index'); });
    Route::get('/referrals/treatment', function () { return view('care.referrals.treatment'); });
    Route::get('/referrals/autopsy', function () { return view('care.referrals.autopsy'); });
    Route::get('/decisions', function () { return view('care.decisions.index'); })->name('care.decisions.index');
    Route::get('/decisions/{id}', function ($id) { return view('care.decisions.show', compact('id')); })->name('care.decisions.show');
});

Route::prefix('records')->group(function () {
    Route::get('/dashboard', function () { return view('records.dashboard'); });
    Route::get('/animals', function () { return view('records.animals.index'); });
    Route::get('/animals/create', function () { return view('records.animals.create'); });
    Route::get('/animals/{id}/edit', function ($id) { return view('records.animals.edit', compact('id')); });
    Route::get('/animals/{id}', function ($id) { return view('records.animals.show', compact('id')); });
    Route::get('/logs/births', function () { return view('records.logs.births'); });
    Route::get('/logs/stillbirths', function () { return view('records.logs.stillbirths'); });
    Route::get('/logs/entries', function () { return view('records.logs.entries'); });
    Route::get('/logs/exits', function () { return view('records.logs.exits'); });
    Route::get('/logs/mortality', function () { return view('records.logs.mortality'); });
    Route::get('/logs/slaughter', function () { return view('records.logs.slaughter'); });
});

Route::prefix('director')->group(function () {
    Route::get('/dashboard', function () {
        return view('director.dashboard');
    });

    Route::redirect('/admin/dashboard', '/director/admin/tickets');
    Route::redirect('/vet/dashboard', '/director/vet/cases/field');
    Route::redirect('/care/dashboard', '/director/care/health');
    Route::redirect('/records/dashboard', '/director/records/animals');

    Route::prefix('admin')->group(function () {
        Route::get('/employees', fn () => directorPage('admin.employees.index'));
        Route::get('/animals', fn () => directorPage('admin.animals.index'));
        Route::get('/map-locations', fn () => directorPage('admin.map-locations.index'));
        Route::get('/tickets', fn () => directorPage('admin.tickets.index'));
        Route::get('/visit-info', fn () => directorPage('admin.visit-info.show'));
    });

    Route::prefix('vet')->group(function () {
        Route::get('/quarantine', fn () => directorPage('vet.quarantine'));
        Route::get('/cases/hospital', fn () => directorPage('vet.cases.hospital'));
        Route::get('/cases/field', fn () => directorPage('vet.cases.field'));
        Route::get('/referrals/treatment', fn () => directorPage('vet.referrals.treatment'));
        Route::get('/referrals/autopsy', fn () => directorPage('vet.referrals.autopsy'));
        Route::get('/decisions', fn () => directorPage('vet.decisions.index'));
    });

    Route::prefix('care')->group(function () {
        Route::get('/health', fn () => directorPage('care.health.index'));
        Route::get('/births', fn () => directorPage('care.births.index'));
        Route::get('/mortality', fn () => directorPage('care.mortality.index'));
        Route::get('/notes', fn () => directorPage('care.notes.index'));
        Route::get('/referrals/treatment', fn () => directorPage('care.referrals.treatment'));
        Route::get('/referrals/autopsy', fn () => directorPage('care.referrals.autopsy'));
        Route::get('/decisions', fn () => directorPage('care.decisions.index'));
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
