<?php

namespace App\Models;

use App\Enums\AnimalStatus;
use App\Services\AnimalGroupCatalog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnimalGroup extends Model
{
  protected $fillable = [
    'name',
    'code_prefix',
    'sort_order',
    'is_active',
  ];

  protected function casts(): array
  {
    return [
      'sort_order' => 'integer',
      'is_active' => 'boolean',
    ];
  }

  protected static function booted(): void
  {
    static::saved(function (AnimalGroup $group): void {
      app(AnimalGroupCatalog::class)->clearCache();

      if ($group->wasChanged('name')) {
        Animal::query()
          ->withQuarantine()
          ->where('animal_group_id', $group->id)
          ->update(['group' => $group->name]);

        User::query()
          ->where('animal_group_id', $group->id)
          ->update(['assigned_group' => $group->name]);
      }
    });

    static::deleted(function (): void {
      app(AnimalGroupCatalog::class)->clearCache();
    });
  }

  public function animals(): HasMany
  {
    return $this->hasMany(Animal::class);
  }

  public function users(): HasMany
  {
    return $this->hasMany(User::class);
  }

  /** @return Builder<AnimalGroup> */
  public static function queryForAdminIndex(): Builder
  {
    static::syncMissingAnimalLinks();

    $excludedStatuses = [
      AnimalStatus::Exited->value,
      AnimalStatus::Dead->value,
    ];

    return static::query()
      ->select('animal_groups.*')
      ->selectSub(
        Animal::query()
          ->withQuarantine()
          ->selectRaw('count(distinct animals.id)')
          ->whereNotIn('animals.status', $excludedStatuses)
          ->where(function (Builder $query): void {
            $query
              ->whereColumn('animals.animal_group_id', 'animal_groups.id')
              ->orWhereColumn('animals.group', 'animal_groups.name');
          }),
        'registered_animals_count',
      )
      ->selectSub(
        User::query()
          ->employees()
          ->selectRaw('count(distinct users.id)')
          ->where(function (Builder $query): void {
            $query
              ->whereColumn('users.animal_group_id', 'animal_groups.id')
              ->orWhereColumn('users.assigned_group', 'animal_groups.name');
          }),
        'linked_employees_count',
      );
  }

  public static function syncMissingAnimalLinks(): void
  {
    $groupsByName = static::query()->pluck('id', 'name');

    foreach ($groupsByName as $name => $id) {
      Animal::query()
        ->withQuarantine()
        ->where(function (Builder $query) use ($id, $name): void {
          $query
            ->whereNull('animal_group_id')
            ->orWhere('animal_group_id', '!=', $id);
        })
        ->where('group', $name)
        ->update(['animal_group_id' => $id]);

      User::query()
        ->employees()
        ->where(function (Builder $query) use ($id, $name): void {
          $query
            ->whereNull('animal_group_id')
            ->orWhere('animal_group_id', '!=', $id);
        })
        ->where('assigned_group', $name)
        ->update(['animal_group_id' => $id]);
    }
  }

  public static function hasLinkedEmployees(int $groupId, string $groupName): bool
  {
    return User::query()
      ->employees()
      ->where(function (Builder $query) use ($groupId, $groupName): void {
        $query
          ->where('animal_group_id', $groupId)
          ->orWhere('assigned_group', $groupName);
      })
      ->exists();
  }

  public static function hasRegisteredAnimals(int $groupId, string $groupName): bool
  {
    return Animal::query()
      ->withQuarantine()
      ->whereNotIn('status', [
        AnimalStatus::Exited->value,
        AnimalStatus::Dead->value,
      ])
      ->where(function (Builder $query) use ($groupId, $groupName): void {
        $query
          ->where('animal_group_id', $groupId)
          ->orWhere('group', $groupName);
      })
      ->exists();
  }
}
