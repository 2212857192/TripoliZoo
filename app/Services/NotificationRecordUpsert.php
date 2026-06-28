<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class NotificationRecordUpsert
{
    /**
     * يحفظ إشعاراً دون إعادة تعيين read_at عند التحديث، إلا إذا تغيّر المحتوى وطُلِب إعادة الفتح.
     *
     * @param  class-string<Model>  $modelClass
     * @param  array<string, mixed>  $keys
     * @param  array<string, mixed>  $attributes
     */
    public static function save(
        string $modelClass,
        array $keys,
        array $attributes,
        bool $reopenOnContentChange = false,
    ): Model {
        /** @var Model $notification */
        $notification = $modelClass::query()->firstOrNew($keys);

        $contentChanged = false;
        if ($notification->exists) {
            foreach (['title', 'message'] as $field) {
                if (
                    array_key_exists($field, $attributes)
                    && $notification->getAttribute($field) !== $attributes[$field]
                ) {
                    $contentChanged = true;
                    break;
                }
            }
        }

        $notification->fill($attributes);

        if (! $notification->exists) {
            $notification->setAttribute('read_at', null);
        } elseif ($reopenOnContentChange && $contentChanged) {
            $notification->setAttribute('read_at', null);
        }

        $notification->save();

        return $notification;
    }
}
