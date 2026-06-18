<?php

namespace App\Models;

use App\Enums\AnimalExitType;
use App\Models\Scopes\ExcludeQuarantineAnimals;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class AnimalExit extends Model
{
    protected $fillable = [
        'animal_id',
        'recorded_by',
        'exit_date',
        'exit_type',
        'recipient',
        'reason',
        'notes',
        'attachment_path',
    ];

    protected function casts(): array
    {
        return [
            'exit_type' => AnimalExitType::class,
            'exit_date' => 'date',
        ];
    }

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class)->withoutGlobalScope(ExcludeQuarantineAnimals::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function attachmentUrl(): ?string
    {
        return $this->attachment_path ? Storage::url($this->attachment_path) : null;
    }
}
