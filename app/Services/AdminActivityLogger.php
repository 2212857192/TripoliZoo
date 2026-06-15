<?php

namespace App\Services;

use App\Models\AdminActivityLog;
use Illuminate\Support\Facades\Auth;

class AdminActivityLogger
{
    public static function log(string $entityType, ?int $entityId, string $action, string $summary): void
    {
        AdminActivityLog::create([
            'user_id' => Auth::id(),
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'action' => $action,
            'summary' => $summary,
        ]);
    }
}
