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
            'working_days' => self::defaultWorkingDays(),
            'open_time' => '09:00',
            'close_time' => '18:00',
            'last_ticket_time_note' => 'قبل ساعة واحدة من موعد الإغلاق',
        ]);
    }

    /** @return array<string, bool> */
    public static function defaultWorkingDays(): array
    {
        return [
            'sat' => true,
            'sun' => true,
            'mon' => true,
            'tue' => true,
            'wed' => true,
            'thu' => true,
            'fri' => true,
        ];
    }

    public static function scheduleLabel(): string
    {
        return 'مفتوحة يومياً';
    }

    public function lastUpdatedLabel(): string
    {
        if (! $this->updated_at) {
            return '—';
        }

        return $this->updated_at->format('d/m/Y — h:i A');
    }

    /** @return list<string> */
    public function workingDayLabels(): array
    {
        $labels = [
            'sat' => 'السبت',
            'sun' => 'الأحد',
            'mon' => 'الإثنين',
            'tue' => 'الثلاثاء',
            'wed' => 'الأربعاء',
            'thu' => 'الخميس',
            'fri' => 'الجمعة',
        ];

        $days = $this->working_days ?? [];

        return collect($labels)
            ->filter(fn (string $label, string $key) => ! empty($days[$key]))
            ->values()
            ->all();
    }

    public function closedDayLabels(): string
    {
        $labels = [
            'sat' => 'السبت',
            'sun' => 'الأحد',
            'mon' => 'الإثنين',
            'tue' => 'الثلاثاء',
            'wed' => 'الأربعاء',
            'thu' => 'الخميس',
            'fri' => 'الجمعة',
        ];

        $days = $this->working_days ?? [];
        $closed = collect($labels)
            ->filter(fn (string $label, string $key) => empty($days[$key]))
            ->values();

        return $closed->isEmpty() ? 'لا يوجد' : $closed->implode(' — ');
    }

    public function formattedOpenTime(): string
    {
        return $this->formatTimeValue($this->open_time) ?? '—';
    }

    public function formattedCloseTime(): string
    {
        return $this->formatTimeValue($this->close_time) ?? '—';
    }

    private function formatTimeValue(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        try {
            return \Illuminate\Support\Carbon::createFromFormat('H:i', substr($value, 0, 5))
                ->locale('ar')
                ->translatedFormat('h:i A');
        } catch (\Throwable) {
            return $value;
        }
    }
}
