<?php

namespace App\Models\Scopes;

use App\Enums\AnimalStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * يستبعد حيوانات الحجر الصحي وحالات بانتظار الاستلام من قوائم النظام العامة.
 * المواليد قيد المتابعة يُعاملون كحيوانات عادية في واجهات المشرف والطبيب.
 */
class ExcludeQuarantineAnimals implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $builder->whereNotIn($model->qualifyColumn('status'), [
            AnimalStatus::Quarantine->value,
            AnimalStatus::PendingReceipt->value,
        ]);
    }
}
