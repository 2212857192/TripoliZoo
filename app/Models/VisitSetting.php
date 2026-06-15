<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitSetting extends Model
{
    protected $fillable = [
        'status_text',
        'status_visible',
        'urgent_alert',
        'ambulance_phone',
        'security_phone',
        'address',
        'latitude',
        'longitude',
        'entry_instructions',
        'working_days',
        'open_time',
        'close_time',
        'last_ticket_time_note',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'status_visible' => 'boolean',
            'working_days' => 'array',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'status_text' => 'مفتوحة — أهلاً بزوارنا',
            'status_visible' => true,
            'ambulance_phone' => '193',
            'security_phone' => '091-555-0123',
            'address' => 'حديقة حيوانات طرابلس — طريق المطار، طرابلس، ليبيا',
            'latitude' => 32.848500,
            'longitude' => 13.178500,
            'entry_instructions' => 'يرجى الحضور من البوابة الرئيسية مع التذكرة.',
            'working_days' => [
                'sat' => true, 'sun' => true, 'mon' => true,
                'tue' => true, 'wed' => true, 'thu' => true, 'fri' => false,
            ],
            'open_time' => '09:00',
            'close_time' => '18:00',
            'last_ticket_time_note' => 'قبل ساعة واحدة من موعد الإغلاق',
        ]);
    }
}
