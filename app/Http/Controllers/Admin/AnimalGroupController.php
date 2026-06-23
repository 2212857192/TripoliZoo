<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnimalGroup;
use App\Models\User;
use App\Services\AdminActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AnimalGroupController extends Controller
{
  public function index(): View
  {
    return view('admin.animal-groups.index', [
      'groups' => AnimalGroup::queryForAdminIndex()
        ->orderBy('sort_order')
        ->orderBy('id')
        ->get(),
    ]);
  }

  public function create(): View
  {
    return view('admin.animal-groups.create', [
      'nextSortOrder' => (int) AnimalGroup::query()->max('sort_order') + 1,
    ]);
  }

  public function store(Request $request): RedirectResponse
  {
    $data = $this->validated($request);
    $group = AnimalGroup::create($data);

    AdminActivityLogger::log('animal_group', $group->id, 'created', "إضافة مجموعة حيوانية: {$group->name}");

    return redirect()
      ->route('admin.animal-groups.index')
      ->with('success', 'تم حفظ المجموعة.');
  }

  public function edit(AnimalGroup $animalGroup): View
  {
    return view('admin.animal-groups.edit', [
      'group' => $animalGroup,
    ]);
  }

  public function update(Request $request, AnimalGroup $animalGroup): RedirectResponse
  {
    $data = $this->validated($request, $animalGroup);
    $animalGroup->update($data);

    AdminActivityLogger::log('animal_group', $animalGroup->id, 'updated', "تعديل مجموعة حيوانية: {$animalGroup->name}");

    return redirect()
      ->route('admin.animal-groups.index')
      ->with('success', 'تم تحديث المجموعة.');
  }

  public function toggle(AnimalGroup $animalGroup): RedirectResponse
  {
    if ($animalGroup->is_active) {
      $inUse = AnimalGroup::hasRegisteredAnimals($animalGroup->id, $animalGroup->name)
        || AnimalGroup::hasLinkedEmployees($animalGroup->id, $animalGroup->name);

      if ($inUse) {
        return back()->with('error', 'لا يمكن تعطيل مجموعة مرتبطة بحيوانات أو موظفين.');
      }
    }

    $animalGroup->update(['is_active' => ! $animalGroup->is_active]);

    $action = $animalGroup->is_active ? 'تفعيل' : 'تعطيل';
    AdminActivityLogger::log('animal_group', $animalGroup->id, 'status', "{$action} مجموعة: {$animalGroup->name}");

    return back()->with(
      'success',
      $animalGroup->is_active ? 'تم تفعيل المجموعة.' : 'تم تعطيل المجموعة.',
    );
  }

  /** @return array<string, mixed> */
  private function validated(Request $request, ?AnimalGroup $group = null): array
  {
    $data = $request->validate([
      'name' => [
        'required',
        'string',
        'max:255',
        Rule::unique('animal_groups', 'name')->ignore($group?->id),
      ],
      'code_prefix' => [
        'required',
        'string',
        'max:10',
        'alpha_num',
        Rule::unique('animal_groups', 'code_prefix')->ignore($group?->id),
      ],
      'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
      'is_active' => ['sometimes', 'boolean'],
    ], [
      'name.required' => 'اسم المجموعة مطلوب.',
      'name.unique' => 'اسم المجموعة مستخدم مسبقاً.',
      'code_prefix.required' => 'بادئة الرقم مطلوبة.',
      'code_prefix.unique' => 'بادئة الرقم مستخدمة مسبقاً.',
      'sort_order.required' => 'ترتيب العرض مطلوب.',
    ]);

    $data['code_prefix'] = strtoupper($data['code_prefix']);
    $data['is_active'] = $request->has('is_active')
      ? $request->boolean('is_active')
      : ($group?->is_active ?? true);

    return $data;
  }
}
