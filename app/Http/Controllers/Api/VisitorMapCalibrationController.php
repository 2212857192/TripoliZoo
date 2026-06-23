<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MapGpsCalibrationService;
use Illuminate\Http\JsonResponse;

class VisitorMapCalibrationController extends Controller
{
    public function __construct(private readonly MapGpsCalibrationService $calibration) {}

    public function show(): JsonResponse
    {
        return response()->json($this->calibration->payload());
    }
}
