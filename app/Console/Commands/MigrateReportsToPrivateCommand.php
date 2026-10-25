<?php

namespace App\Console\Commands;

use App\Models\PatientReport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class MigrateReportsToPrivateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:migrate-to-private 
                            {--dry-run : Simulate migration without modifying files or database}
                            {--rollback : Reverse migration, moving files back to public storage and restoring DB paths}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate patient medical reports from public/uploads/reports to private storage (storage/app/private/reports)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = (bool) $this->option('dry-run');
        $isRollback = (bool) $this->option('rollback');

        $this->info($isRollback ? '--- Reversing Patient Reports Migration ---' : '--- Migrating Patient Reports to Private Storage (SEC-07) ---');

        if ($isDryRun) {
            $this->warn('[DRY-RUN MODE ACTIVATED]: No files will be moved and no database records will be altered.');
        }

        $publicReportsDir = public_path('uploads/reports');
        $privateDisk = Storage::disk('local'); // Root: storage/app/private
        $privateReportsPath = storage_path('app/private/reports');

        if ($isRollback) {
            return $this->handleRollback($isDryRun, $publicReportsDir, $privateReportsPath);
        }

        return $this->handleMigration($isDryRun, $publicReportsDir, $privateReportsPath);
    }

    /**
     * Handle the migration of reports from public to private storage.
     */
    protected function handleMigration(bool $isDryRun, string $publicReportsDir, string $privateReportsPath): int
    {
        if (! File::exists($privateReportsPath) && ! $isDryRun) {
            File::makeDirectory($privateReportsPath, 0750, true);
        }

        $movedFilesCount = 0;
        $updatedDbCount = 0;
        $orphanedFilesCount = 0;

        // 1. Scan and migrate records currently in the database with public file paths
        $publicReports = PatientReport::where('file_path', 'like', 'uploads/%')
            ->orWhere('file_path', 'like', 'public/uploads/%')
            ->get();

        $this->line("Found {$publicReports->count()} database record(s) pointing to public paths.");

        foreach ($publicReports as $report) {
            $currentPath = $report->file_path;
            $fileName = basename($currentPath);
            $fullSourcePath = public_path($currentPath);
            $targetPrivatePath = "reports/{$fileName}";
            $fullDestinationPath = storage_path("app/private/{$targetPrivatePath}");

            $this->line("Processing Report #{$report->id} ({$report->report_number}): {$fileName}");

            if (File::exists($fullSourcePath)) {
                if ($isDryRun) {
                    $this->info(" [DRY-RUN] Would move: {$fullSourcePath} -> {$fullDestinationPath}");
                } else {
                    if (! File::exists(dirname($fullDestinationPath))) {
                        File::makeDirectory(dirname($fullDestinationPath), 0750, true);
                    }
                    File::move($fullSourcePath, $fullDestinationPath);
                    $this->info(" Moved physical file: {$fullSourcePath} -> {$fullDestinationPath}");
                }
                $movedFilesCount++;
            } else {
                $this->warn(" Source file not found on disk at: {$fullSourcePath}");
            }

            if ($isDryRun) {
                $this->info(" [DRY-RUN] Would update DB path: '{$currentPath}' -> '{$targetPrivatePath}'");
            } else {
                $report->update(['file_path' => $targetPrivatePath]);
                $this->info(" Updated DB record #{$report->id} to '{$targetPrivatePath}'");
            }
            $updatedDbCount++;
        }

        // 2. Scan for any orphaned files in public/uploads/reports not tracked in DB
        if (File::exists($publicReportsDir)) {
            $allPublicFiles = File::files($publicReportsDir);
            foreach ($allPublicFiles as $file) {
                $fileName = $file->getFilename();
                $targetPrivatePath = "reports/{$fileName}";
                $fullDestinationPath = storage_path("app/private/{$targetPrivatePath}");

                if ($isDryRun) {
                    $this->info(" [DRY-RUN] Orphan file found in public/uploads/reports. Would move: {$file->getPathname()} -> {$fullDestinationPath}");
                } else {
                    if (! File::exists(dirname($fullDestinationPath))) {
                        File::makeDirectory(dirname($fullDestinationPath), 0750, true);
                    }
                    File::move($file->getPathname(), $fullDestinationPath);
                    $this->info(" Moved orphan file: {$file->getPathname()} -> {$fullDestinationPath}");
                }
                $orphanedFilesCount++;
            }
        }

        // 3. Verify no report files remain in public storage
        $remainingPublicFiles = File::exists($publicReportsDir) ? count(File::files($publicReportsDir)) : 0;

        $this->newLine();
        $this->info('================ MIGRATION SUMMARY ================');
        $this->line("Database records updated: {$updatedDbCount}");
        $this->line("Physical files moved:     {$movedFilesCount}");
        $this->line("Orphaned files moved:     {$orphanedFilesCount}");
        $this->line("Public files remaining:   {$remainingPublicFiles}");

        if ($remainingPublicFiles > 0 && ! $isDryRun) {
            $this->error("SECURITY ALERT: {$remainingPublicFiles} file(s) still remain publicly accessible in {$publicReportsDir}!");

            return Command::FAILURE;
        }

        $this->info('MIGRATION COMPLETE: All medical reports are secured in private storage.');

        return Command::SUCCESS;
    }

    /**
     * Handle reversing the migration for rollback / disaster recovery.
     */
    protected function handleRollback(bool $isDryRun, string $publicReportsDir, string $privateReportsPath): int
    {
        $this->warn('Executing rollback: moving files back to public/uploads/reports...');

        if (! File::exists($publicReportsDir) && ! $isDryRun) {
            File::makeDirectory($publicReportsDir, 0755, true);
        }

        $privateReports = PatientReport::where('file_path', 'like', 'reports/%')->get();
        $this->line("Found {$privateReports->count()} database record(s) pointing to private storage.");

        $restoredFiles = 0;
        $restoredDb = 0;

        foreach ($privateReports as $report) {
            $fileName = basename($report->file_path);
            $fullSourcePath = storage_path("app/private/reports/{$fileName}");
            $fullDestinationPath = "{$publicReportsDir}/{$fileName}";
            $targetDbPath = "uploads/reports/{$fileName}";

            if (File::exists($fullSourcePath)) {
                if ($isDryRun) {
                    $this->info(" [DRY-RUN] Would rollback file: {$fullSourcePath} -> {$fullDestinationPath}");
                } else {
                    File::move($fullSourcePath, $fullDestinationPath);
                    $this->info(" Restored file: {$fullSourcePath} -> {$fullDestinationPath}");
                }
                $restoredFiles++;
            }

            if ($isDryRun) {
                $this->info(" [DRY-RUN] Would revert DB path: '{$report->file_path}' -> '{$targetDbPath}'");
            } else {
                $report->update(['file_path' => $targetDbPath]);
                $this->info(" Reverted DB record #{$report->id} to '{$targetDbPath}'");
            }
            $restoredDb++;
        }

        $this->newLine();
        $this->info('================ ROLLBACK SUMMARY ================');
        $this->line("Database records reverted: {$restoredDb}");
        $this->line("Files restored to public:  {$restoredFiles}");

        return Command::SUCCESS;
    }
}
