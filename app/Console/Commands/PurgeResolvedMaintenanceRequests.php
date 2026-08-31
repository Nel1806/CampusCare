<?php

namespace App\Console\Commands;

use App\Models\MaintenanceRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PurgeResolvedMaintenanceRequests extends Command
{
    protected $signature = 'maintenance:purge-resolved';

    protected $description = 'Permanently delete resolved maintenance requests older than 30 days';

    public function handle(): int
    {
        $deleted = 0;

        MaintenanceRequest::query()
            ->where('status', 'resolved')
            ->where('updated_at', '<=', now()->subDays(30))
            ->eachById(function (MaintenanceRequest $maintenanceRequest) use (&$deleted) {
                if ($maintenanceRequest->photo_path) {
                    Storage::disk('public')->delete($maintenanceRequest->photo_path);
                }

                $maintenanceRequest->delete();
                $deleted++;
            });

        $this->info("Deleted {$deleted} resolved maintenance request(s) older than 30 days.");

        return self::SUCCESS;
    }
}
