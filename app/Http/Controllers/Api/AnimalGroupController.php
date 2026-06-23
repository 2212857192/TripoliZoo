<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AnimalGroupCatalog;
use Illuminate\Http\JsonResponse;

class AnimalGroupController extends Controller
{
  public function index(AnimalGroupCatalog $catalog): JsonResponse
  {
    $groups = $catalog->activeRecords()->map(fn ($group) => [
      'id' => $group->id,
      'name' => $group->name,
      'code_prefix' => $group->code_prefix,
      'sort_order' => $group->sort_order,
    ])->values();

    return response()->json(['data' => $groups]);
  }
}
