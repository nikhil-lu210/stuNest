<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class DatabaseBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a daily database backup with 3-day retention (shared-hosting friendly)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $this->info('Starting daily database backup...');

            // Create backup directory if it doesn't exist
            $backupDir = storage_path('app/public/db_backup');
            if (! file_exists($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            // Generate filename with current date
            $date = Carbon::now()->format('dmY');
            $filename = "holboxito_db_backup_{$date}.sql";
            $filepath = $backupDir . DIRECTORY_SEPARATOR . $filename;

            // Get database configuration
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host     = config('database.connections.mysql.host');
            $port     = config('database.connections.mysql.port');

            // Try mysqldump first
            $mysqldumpPath = $this->getMysqldumpPath();

            $this->info('Looking for mysqldump...');
            $this->info('Found mysqldump at: ' . ($mysqldumpPath ?: 'NOT FOUND'));

            if ($mysqldumpPath) {
                $command = sprintf(
                    '%s --host=%s --port=%s --user=%s --password=%s --single-transaction --routines --triggers %s > %s',
                    escapeshellarg($mysqldumpPath),
                    escapeshellarg($host),
                    escapeshellarg($port),
                    escapeshellarg($username),
                    escapeshellarg($password),
                    escapeshellarg($database),
                    escapeshellarg($filepath)
                );

                $this->info('Creating database dump using mysqldump...');
                exec($command, $output, $returnCode);

                if ($returnCode !== 0) {
                    throw new \Exception('Database backup failed with return code: ' . $returnCode);
                }
            } else {
                // Fallback: Use Laravel DB connection for shared hosting
                $this->info('mysqldump not found, using Laravel database connection...');
                $this->createBackupUsingLaravel($filepath);
            }

            // Verify backup file was created and has content
            if (! file_exists($filepath) || filesize($filepath) === 0) {
                throw new \Exception('Backup file was not created or is empty');
            }

            $this->info("Database backup created successfully: {$filename}");
            $this->info('File size: ' . $this->formatBytes(filesize($filepath)));

            // Clean old backups (keep only last 3 days)
            $this->cleanOldBackups($backupDir);

            $this->info('Daily database backup completed successfully!');

        } catch (\Exception $e) {
            $this->error('Database backup failed: ' . $e->getMessage());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * Clean old backup files (keep only last 3 days)
     */
    private function cleanOldBackups(string $backupDir): void
    {
        $this->info('Cleaning old backup files...');

        $files      = glob($backupDir . DIRECTORY_SEPARATOR . 'holboxito_db_backup_*.sql') ?: [];
        $cutoffDate = Carbon::now()->subDays(3);

        $deletedCount = 0;
        foreach ($files as $file) {
            $filename = basename($file);

            // Extract date from filename (holboxito_db_backup_DDMMYYYY.sql)
            if (preg_match('/holboxito_db_backup_(\d{8})\.sql/', $filename, $matches)) {
                $fileDate = Carbon::createFromFormat('dmY', $matches[1]);

                if ($fileDate->lt($cutoffDate)) {
                    if (@unlink($file)) {
                        $deletedCount++;
                        $this->info("Deleted old backup: {$filename}");
                    }
                }
            }
        }

        if ($deletedCount > 0) {
            $this->info("Cleaned {$deletedCount} old backup file(s)");
        } else {
            $this->info('No old backup files to clean');
        }
    }

    /**
     * Get mysqldump executable path (shared-hosting friendly)
     */
    private function getMysqldumpPath(): ?string
    {
        $customPath = env('MYSQLDUMP_PATH');
        if ($customPath && file_exists($customPath)) {
            return $customPath;
        }

        $linuxPaths = [
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
            '/usr/bin/mariadb-dump',
            '/usr/local/bin/mariadb-dump',
        ];

        foreach ($linuxPaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        $output     = [];
        $returnCode = 0;

        if (PHP_OS_FAMILY === 'Windows') {
            exec('where mysqldump 2>nul', $output, $returnCode);
        } else {
            exec('which mysqldump 2>/dev/null', $output, $returnCode);
        }

        if ($returnCode === 0 && ! empty($output)) {
            $path = trim($output[0]);
            if (file_exists($path) && is_executable($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $size, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        for ($i = 0; $size >= 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }

        return round($size, $precision) . ' ' . $units[$i];
    }

    /**
     * Create backup using Laravel's database connection (fallback for shared hosting)
     */
    private function createBackupUsingLaravel(string $filepath): void
    {
        $this->info('Creating backup using Laravel database connection...');

        try {
            $database  = config('database.connections.mysql.database');
            $tables    = \DB::select('SHOW TABLES');
            $tableKey  = 'Tables_in_' . $database;

            $sql  = '-- Database Backup Created: ' . now() . "\n";
            $sql .= "-- Database: {$database}\n\n";
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            foreach ($tables as $table) {
                $tableName = $table->$tableKey;

                // Table structure
                $createTable = \DB::select("SHOW CREATE TABLE `{$tableName}`")[0];
                $sql        .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $sql        .= $createTable->{'Create Table'} . ";\n\n";

                // Table data
                $rows = \DB::table($tableName)->get();

                if ($rows->count() > 0) {
                    $sql .= "LOCK TABLES `{$tableName}` WRITE;\n";

                    foreach ($rows as $row) {
                        $values = array_map(function ($value) {
                            if ($value === null) {
                                return 'NULL';
                            } elseif (is_string($value)) {
                                return "'" . addslashes($value) . "'";
                            }
                            return $value;
                        }, (array) $row);

                        $sql .= 'INSERT INTO `' . $tableName . '` VALUES (' . implode(',', $values) . ");\n";
                    }

                    $sql .= "UNLOCK TABLES;\n\n";
                }
            }

            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

            file_put_contents($filepath, $sql);
            $this->info('Backup created successfully using Laravel database connection');

        } catch (\Exception $e) {
            throw new \Exception('Failed to create backup using Laravel: ' . $e->getMessage());
        }
    }
}
