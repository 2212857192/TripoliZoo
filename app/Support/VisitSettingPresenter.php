<?php

namespace App\Support;

use App\Models\VisitSetting;

final class VisitSettingPresenter
{
  private const DAY_LABELS = [
    'sat' => 'السبت',
    'sun' => 'الأحد',
    'mon' => 'الإثنين',
    'tue' => 'الثلاثاء',
    'wed' => 'الأربعاء',
    'thu' => 'الخميس',
    'fri' => 'الجمعة',
  ];

  /** @return array<string, mixed> */
  public static function toPublicArray(VisitSetting $settings): array
  {
    $openTime = self::formatTime($settings->open_time);
    $closeTime = self::formatTime($settings->close_time);
    $openDays = self::openDayKeys($settings);
    $closedDays = self::closedDayKeys($settings);

    return [
      'status' => [
        'text' => $settings->status_text,
        'visible' => (bool) $settings->status_visible,
      ],
      'urgent_alert' => $settings->urgent_alert,
      'ambulance_phone' => $settings->ambulance_phone,
      'security_phone' => $settings->security_phone,
      'location' => [
        'address' => $settings->address,
        'latitude' => $settings->latitude !== null ? (float) $settings->latitude : null,
        'longitude' => $settings->longitude !== null ? (float) $settings->longitude : null,
        'google_maps_url' => self::googleMapsUrl($settings),
      ],
      'hours' => [
        'open_time' => $openTime,
        'close_time' => $closeTime,
        'working_hours_label' => ($openTime && $closeTime) ? "{$openTime} - {$closeTime}" : null,
        'working_days_label' => self::workingDaysLabel($openDays),
        'closed_days_label' => self::closedDaysLabel($closedDays),
        'last_ticket_time_note' => $settings->last_ticket_time_note,
      ],
      'entry_instructions' => $settings->entry_instructions,
      'guidelines' => self::guidelines($settings->entry_instructions),
    ];
  }

  public static function formatTime(mixed $time): ?string
  {
    if ($time === null || $time === '') {
      return null;
    }

    return substr((string) $time, 0, 5);
  }

  /** @return list<string> */
  public static function openDayKeys(VisitSetting $settings): array
  {
    return collect(self::DAY_LABELS)
      ->keys()
      ->filter(fn (string $day) => (bool) data_get($settings->working_days, $day, false))
      ->values()
      ->all();
  }

  /** @return list<string> */
  public static function closedDayKeys(VisitSetting $settings): array
  {
    return collect(self::DAY_LABELS)
      ->keys()
      ->reject(fn (string $day) => (bool) data_get($settings->working_days, $day, false))
      ->values()
      ->all();
  }

  /** @param  list<string>  $openDays */
  public static function workingDaysLabel(array $openDays): ?string
  {
    if ($openDays === []) {
      return null;
    }

    if ($openDays === ['sat', 'sun', 'mon', 'tue', 'wed', 'thu']) {
      return 'السبت — الخميس';
    }

    if (count($openDays) === 7) {
      return 'طيلة الأسبوع';
    }

    return collect($openDays)
      ->map(fn (string $day) => self::DAY_LABELS[$day] ?? $day)
      ->implode('، ');
  }

  /** @param  list<string>  $closedDays */
  public static function closedDaysLabel(array $closedDays): ?string
  {
    if ($closedDays === []) {
      return null;
    }

    return collect($closedDays)
      ->map(fn (string $day) => self::DAY_LABELS[$day] ?? $day)
      ->implode('، ');
  }

  /** @return list<string> */
  public static function guidelines(?string $text): array
  {
    if ($text === null || trim($text) === '') {
      return [];
    }

    return collect(preg_split('/\R/u', $text) ?: [])
      ->map(static function (string $line): string {
        $line = trim($line);

        return (string) preg_replace('/^[•\-\*]\s*/u', '', $line);
      })
      ->filter()
      ->values()
      ->all();
  }

  public static function googleMapsUrl(VisitSetting $settings): ?string
  {
    if ($settings->latitude === null || $settings->longitude === null) {
      return null;
    }

    $lat = (float) $settings->latitude;
    $lng = (float) $settings->longitude;

    return "https://www.google.com/maps/search/?api=1&query={$lat},{$lng}";
  }
}
