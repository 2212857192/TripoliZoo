<?php

namespace App\Http\Controllers\Records;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Services\RecordsAnimalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecordsAnimalController extends Controller
{
    public function index(Request $request, RecordsAnimalService $service): View
    {
        return view('records.animals.index', $service->indexViewData($request, '/records'));
    }

    public function directorIndex(Request $request, RecordsAnimalService $service): View
    {
        return directorPage('records.animals.index', $service->indexViewData($request, '/director/records', readOnly: true));
    }

    public function create(RecordsAnimalService $service): View
    {
        return view('records.animals.create', $service->createViewData('/records'));
    }

    public function store(Request $request, RecordsAnimalService $service): RedirectResponse
    {
        $animal = $service->register($request);

        return redirect()
            ->route('records.animals.show', $animal)
            ->with('success', "تم تسجيل الحيوان {$animal->code} بنجاح.");
    }

    public function show(Animal $animal, RecordsAnimalService $service): View
    {
        $animal = $service->findForRecords($animal->code);

        return view('records.animals.show', $service->showViewData($animal, '/records'));
    }

    public function directorShow(Animal $animal, RecordsAnimalService $service): View
    {
        $animal = $service->findForRecords($animal->code);

        return directorPage('records.animals.show', $service->showViewData($animal, '/director/records', readOnly: true));
    }

    public function edit(Animal $animal, RecordsAnimalService $service): View
    {
        $animal = $service->findForRecords($animal->code);

        return view('records.animals.edit', $service->editViewData($animal, '/records'));
    }

    public function update(Request $request, Animal $animal, RecordsAnimalService $service): RedirectResponse
    {
        $animal = $service->findForRecords($animal->code);
        $service->update($request, $animal);

        return redirect()
            ->route('records.animals.show', $animal)
            ->with('success', 'تم حفظ تعديلات الحيوان بنجاح.');
    }

    public function export(Animal $animal, RecordsAnimalService $service): View
    {
        $animal = $service->findForRecords($animal->code);

        return view('records.animals.export', $service->exportViewData($animal));
    }

    public function exit(Request $request, Animal $animal, RecordsAnimalService $service): RedirectResponse
    {
        $animal = $service->findForRecords($animal->code);
        $service->documentExit($request, $animal);

        return redirect()
            ->route('records.animals.show', $animal)
            ->with('success', 'تم توثيق خروج الحيوان بنجاح.');
    }
}
