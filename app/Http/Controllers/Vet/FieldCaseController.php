<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Models\FieldCase;
use App\Services\FieldCaseService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FieldCaseController extends Controller
{
    public function index(Request $request, FieldCaseService $service): View
    {
        return view('vet.cases.field', $service->indexViewData($request, '/vet'));
    }

    public function directorIndex(Request $request, FieldCaseService $service): View
    {
        return directorPage('vet.cases.field', $service->indexViewData($request, '/director/vet', readOnly: true));
    }

    public function show(FieldCase $fieldCase, FieldCaseService $service): View
    {
        return view('vet.cases.field.show', $service->showViewData($fieldCase));
    }

    public function directorShow(FieldCase $fieldCase, FieldCaseService $service): View
    {
        return directorPage('vet.cases.field.show', $service->showViewData($fieldCase));
    }
}
