<?php

namespace App\Support;

use App\Models\VisitSetting;

final class VisitSettingPresenter
{
  /** @return array<string, mixed> */
  public static function toPublicArray(VisitSetting $settings): array
  {
    $openTime = self::formatTime($settings->open_time);
    $closeTime = self::formatTime($settings->close_time);

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
        'schedule_label' => VisitSetting::scheduleLabel(),
        'working_days_label' => VisitSetting::scheduleLabel(),
        'closed_days_label' => null,
        'open_daily' => true,
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
