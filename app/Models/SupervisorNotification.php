<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupervisorNotification extends Model
{
    protected $fillable = [
        'user_id',
        'receiving_task_id',
        'medical_nutrition_recommendation_id',
        'title',
        'message',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function receivingTask(): BelongsTo
    {
        return $this->belongsTo(ReceivingTask::class);
    }

    public function nutritionRecommendation(): BelongsTo
    {
        return $this->belongsTo(MedicalNutritionRecommendation::class, 'medical_nutrition_recommendation_id');
    }

    public function isUnread(): bool
    {
        return $this->read_at === null;
    }

    public function markAsRead(): void
    {
        if ($this->read_at === null) {
            $this->update(['read_at' => now()]);
        }
    }
}
