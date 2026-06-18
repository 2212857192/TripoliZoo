<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalNutritionRecommendation extends Model
{
    protected $fillable = [
        'medical_case_procedure_id',
        'recommendation_text',
        'start_date',
        'end_date',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function procedure(): BelongsTo
    {
        return $this->belongsTo(MedicalCaseProcedure::class, 'medical_case_procedure_id');
    }
}
