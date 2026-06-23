<?php

namespace App\Services;

use App\Models\AnimalGroup;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class AnimalGroupCatalog
{
  private const CACHE_KEY = 'animal_groups.catalog.v1';

  /** @var list<array{name: string, code_prefix: string}> */
  private const FALLBACK_GROUPS = [
    ['name' => 'القططية', 'code_prefix' => 'C'],
    ['name' => 'الطيور', 'code_prefix' => 'B'],
    ['name' => 'الزواحف', 'code_prefix' => 'R'],
    ['name' => 'القرود', 'code_prefix' => 'M'],
    ['name' => 'الغزلان', 'code_prefix' => 'G'],
    ['name' => 'الثدييات الكبيرة', 'code_prefix' => 'L'],
    ['name' => 'الثدييات الصغيرة', 'code_prefix' => 'S'],
    ['name' => 'الدب واللامة', 'code_prefix' => 'D'],
  ];

  public function clearCache(): void
  {
    Cache::forget(self::CACHE_KEY);
  }

  /** @return Collection<int, AnimalGroup> */
  public function activeRecords(): Collection
  {
    if (! Schema::hasTable('animal_groups')) {
      return $this->fallbackRecords();
    }

    return Cache::remember(
      self::CACHE_KEY,
      3600,
      fn () => AnimalGroup::query()
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->orderBy('id')
        ->get(),
    );
  }

  /** @return Collection<int, AnimalGroup> */
  public function allRecords(): Collection
  {
    if (! Schema::hasTable('animal_groups')) {
      return $this->fallbackRecords();
    }

    return AnimalGroup::query()
      ->orderBy('sort_order')
      ->orderBy('id')
      ->get();
  }

  /** @return list<string> */
  public function names(): array
  {
    return $this->activeRecords()->pluck('name')->all();
  }

  /** @return array<string, string> */
  public function prefixes(): array
  {
    return $this->activeRecords()->pluck('code_prefix', 'name')->all();
  }

  public function find(int $id): ?AnimalGroup
  {
    if (! Schema::hasTable('animal_groups')) {
      return $this->fallbackRecords()->firstWhere('id', $id);
    }

    return AnimalGroup::query()->find($id);
  }

  public function findByName(string $name): ?AnimalGroup
  {
    if (! Schema::hasTable('animal_groups')) {
      return $this->fallbackRecords()->firstWhere('name', $name);
    }

    return AnimalGroup::query()->where('name', $name)->first();
  }

  /** @return Collection<int, AnimalGroup> */
  private function fallbackRecords(): Collection
  {
    return collect(self::FALLBACK_GROUPS)->values()->map(function (array $group, int $index) {
      $record = new AnimalGroup([
        'name' => $group['name'],
        'code_prefix' => $group['code_prefix'],
        'sort_order' => $index + 1,
        'is_active' => true,
      ]);
      $record->id = $index + 1;

      return $record;
    });
  }
}
