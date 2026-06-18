<?php

namespace App\Console\Commands;

use App\Services\MapPathGraphService;
use Illuminate\Console\Command;

class SyncMapPathGraph extends Command
{
    protected $signature = 'map:sync-graph';

    protected $description = 'Rebuild the hidden walkway graph used for in-zoo navigation';

    public function handle(MapPathGraphService $service): int
    {
        $service->syncFromLocations();
        $this->info('Map navigation graph synced successfully.');

        return self::SUCCESS;
    }
}
