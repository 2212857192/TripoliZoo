<?php

namespace App\Models;

use App\Enums\MedicalCaseResult;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MedicalCaseProcedure extends Model
{
    protected $fillable = [
        'caseable_type',
        'caseable_id',
        'recorded_by',
        'diagnosis',
        'treatment',
        'note',
        'case_result',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'case_result' => MedicalCaseResult::class,
            'recorded_at' => 'datetime',
        ];
    }

    public function caseable(): MorphTo
    {
        return $this->morphTo();
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function nutritionRecommendation(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(MedicalNutritionRecommendation::class);
    }
}
