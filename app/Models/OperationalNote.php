<?php

namespace App\Models;

use App\Enums\OperationalNoteKind;
use App\Enums\OperationalNoteStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OperationalNote extends Model
{
    protected $fillable = [
        'note_number',
        'supervisor_id',
        'group',
        'note_kind',
        'summary',
        'details',
        'has_attachment',
        'attachment_path',
        'status',
        'reviewed_by',
        'reviewed_at',
        'noted_at',
    ];

    protected function casts(): array
    {
        return [
            'note_kind' => OperationalNoteKind::class,
            'status' => OperationalNoteStatus::class,
            'has_attachment' => 'boolean',
            'reviewed_at' => 'datetime',
            'noted_at' => 'datetime',
        ];
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(OperationalNoteNotification::class);
    }

    public function getRouteKeyName(): string
    {
        return 'note_number';
    }

    public function canBeReviewed(): bool
    {
        return $this->status === OperationalNoteStatus::New;
    }
}
