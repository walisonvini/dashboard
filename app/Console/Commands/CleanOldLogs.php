<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Log;
use Carbon\Carbon;

class CleanOldLogs extends Command
{
    protected $signature = 'logs:cleanup';
    protected $description = 'Delete logs older than 30 days';

    public function handle()
    {
        $count = Log::where('created_at', '<', Carbon::now()->subDays(30))->delete();

        $this->info("Deleted {$count} old logs.");
    }
}
