<?php

use App\Models\VisitSetting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        VisitSetting::query()->each(function (VisitSetting $settings): void {
            $settings->update([
                'working_days' => VisitSetting::defaultWorkingDays(),
            ]);
        });
    }

    public function down(): void
    {
        // Irreversible: previous per-day schedules are not restored.
    }
};
