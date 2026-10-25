<?php

namespace App\Console\Commands;

use App\Models\Doctor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanPortfolioXssCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'portfolio:clean-xss 
                            {--dry-run : Simulate sanitization without updating database}
                            {--rollback : Restore portfolio_data from the latest backup}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sanitize stored XSS and raw HTML injection vectors inside doctor portfolio_data JSON';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = (bool) $this->option('dry-run');
        $isRollback = (bool) $this->option('rollback');
        $backupDir = storage_path('app/private/backups');

        if ($isRollback) {
            return $this->handleRollback($isDryRun, $backupDir);
        }

        $this->info('--- Scanning and Sanitizing Doctor portfolio_data (SEC-09) ---');

        if ($isDryRun) {
            $this->warn('[DRY-RUN MODE ACTIVATED]: No database records will be modified.');
        }

        $doctors = Doctor::whereNotNull('portfolio_data')->get();
        $this->line("Found {$doctors->count()} doctor(s) with saved portfolio data.");

        $totalCleanedDoctors = 0;
        $totalCleanedFields = 0;
        $backupPayload = [];

        foreach ($doctors as $doctor) {
            $originalData = $doctor->portfolio_data;
            if (! is_array($originalData)) {
                continue;
            }

            $hasModifications = false;
            $cleanedFieldsForDoc = 0;

            $cleanedData = $this->sanitizeRecursive($originalData, $hasModifications, $cleanedFieldsForDoc, "Doctor #{$doctor->id} ({$doctor->name})");

            if ($hasModifications) {
                $totalCleanedDoctors++;
                $totalCleanedFields += $cleanedFieldsForDoc;
                $backupPayload[$doctor->id] = $originalData;

                if (! $isDryRun) {
                    $doctor->update(['portfolio_data' => $cleanedData]);
                    $this->info(" Updated Doctor #{$doctor->id} ({$doctor->name}) in database.");
                }
            }
        }

        // Save backup if live modifications occurred
        if (! $isDryRun && ! empty($backupPayload)) {
            if (! File::exists($backupDir)) {
                File::makeDirectory($backupDir, 0750, true);
            }
            $backupFile = "{$backupDir}/portfolio_data_backup_".time().'.json';
            File::put($backupFile, json_encode($backupPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $this->info("Backup saved before modification at: {$backupFile}");
        }

        $this->newLine();
        $this->info('================ SANITIZATION SUMMARY ================');
        $this->line("Doctors scanned:         {$doctors->count()}");
        $this->line("Doctors with XSS/HTML:   {$totalCleanedDoctors}");
        $this->line("Fields sanitized:        {$totalCleanedFields}");
        $this->info($isDryRun ? 'Dry-run complete. Run without --dry-run to commit changes.' : 'Portfolio data sanitization completed successfully.');

        return Command::SUCCESS;
    }

    /**
     * Recursively sanitize all strings inside an array.
     */
    protected function sanitizeRecursive(mixed $data, bool &$hasModifications, int &$cleanedCount, string $context): mixed
    {
        if (is_array($data)) {
            $result = [];
            foreach ($data as $key => $value) {
                $result[$key] = $this->sanitizeRecursive($value, $hasModifications, $cleanedCount, "{$context} -> [{$key}]");
            }

            return $result;
        }

        if (is_string($data)) {
            $sanitized = $this->sanitizeString($data);
            if ($sanitized !== $data) {
                $hasModifications = true;
                $cleanedCount++;
                $this->warn(" [SANITIZE] {$context}");
                $this->line("   Original:  {$data}");
                $this->line("   Cleaned:   {$sanitized}");

                return $sanitized;
            }

            return $data;
        }

        return $data;
    }

    /**
     * Strip HTML tags, dangerous javascript: pseudo-protocols, and malicious script vectors.
     */
    protected function sanitizeString(string $input): string
    {
        // 1. Strip raw HTML tags
        $cleaned = strip_tags($input);

        // 2. Remove javascript: and data: text pseudo-protocols in links
        $cleaned = preg_replace('/javascript\s*:/i', '', $cleaned);
        $cleaned = preg_replace('/vbscript\s*:/i', '', $cleaned);
        $cleaned = preg_replace('/data\s*:\s*text\/html/i', '', $cleaned);

        // 3. Decode & strip stray encoded script constructs
        $decoded = html_entity_decode($cleaned, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if (preg_match('/<script|onload=|onerror=/i', $decoded)) {
            $cleaned = strip_tags($decoded);
        }

        return trim($cleaned);
    }

    /**
     * Revert the database changes using the latest backup file.
     */
    protected function handleRollback(bool $isDryRun, string $backupDir): int
    {
        $this->warn('--- Rolling Back Portfolio Data Sanitization ---');

        if (! File::exists($backupDir)) {
            $this->error("No backup directory found at {$backupDir}.");

            return Command::FAILURE;
        }

        $backupFiles = File::glob("{$backupDir}/portfolio_data_backup_*.json");
        if (empty($backupFiles)) {
            $this->error("No backup files found in {$backupDir}.");

            return Command::FAILURE;
        }

        // Sort to get the most recent backup
        rsort($backupFiles);
        $latestBackup = $backupFiles[0];
        $this->info("Using latest backup file: {$latestBackup}");

        $backupData = json_decode(File::get($latestBackup), true);
        if (! is_array($backupData)) {
            $this->error('Failed to parse backup JSON file.');

            return Command::FAILURE;
        }

        $restoredCount = 0;
        foreach ($backupData as $doctorId => $originalPortfolioData) {
            $doctor = Doctor::find($doctorId);
            if ($doctor) {
                if ($isDryRun) {
                    $this->info(" [DRY-RUN] Would restore Doctor #{$doctorId} ({$doctor->name})");
                } else {
                    $doctor->update(['portfolio_data' => $originalPortfolioData]);
                    $this->info(" Restored Doctor #{$doctorId} ({$doctor->name})");
                }
                $restoredCount++;
            }
        }

        $this->info("Rollback complete. Restored {$restoredCount} doctor(s).");

        return Command::SUCCESS;
    }
}
