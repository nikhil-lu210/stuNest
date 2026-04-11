<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear Laravel log files to keep disk usage low';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $logPath = storage_path('logs');

        // Get all *.log files in the logs directory
        $logFiles = glob($logPath . '/*.log') ?: [];

        if (empty($logFiles)) {
            $this->info('No log files found to clear.');
            return self::SUCCESS;
        }

        foreach ($logFiles as $file) {
            if (is_file($file)) {
                // Truncate the file instead of deleting it to keep permissions intact
                file_put_contents($file, '');
                $this->info("Cleared: {$file}");
            }
        }

        $this->info('All log files have been cleared.');

        return self::SUCCESS;
    }
}
