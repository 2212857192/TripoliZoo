<?php

use App\Services\AnimalGroupCatalog;

if (! function_exists('animal_groups')) {
  /**
   * المجموعات الرسمية بالترتيب المعتمد.
   *
   * @return list<string>
   */
  function animal_groups(): array
  {
    return app(AnimalGroupCatalog::class)->names();
  }
}

if (! function_exists('animal_group_prefixes')) {
  /**
   * بادئات أرقام الحيوانات لكل مجموعة.
   *
   * @return array<string, string>
   */
  function animal_group_prefixes(): array
  {
    return app(AnimalGroupCatalog::class)->prefixes();
  }
}

if (! function_exists('animal_group_records')) {
  /**
   * سجلات المجموعات النشطة مع المعرّفات.
   *
   * @return \Illuminate\Support\Collection<int, \App\Models\AnimalGroup>
   */
  function animal_group_records(): \Illuminate\Support\Collection
  {
    return app(AnimalGroupCatalog::class)->activeRecords();
  }
}

if (! function_exists('quarantine_code_prefix')) {
  function quarantine_code_prefix(): string
  {
    return 'Q';
  }
}
