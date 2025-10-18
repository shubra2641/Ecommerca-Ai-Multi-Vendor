<?php

namespace App\Console\Commands;

use App\Models\VendorExport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupVendorExports extends Command
{
    protected $signature = 'vendor_exports:cleanup {--days=7 : Number of days to keep exports}';

    protected $description = 'Remove vendor export files and DB rows older than specified days';

    public function handle(): int
    {
        $days = $this->getValidDays();
        $cutoff = now()->subDays($days);
        
        $this->info("Cleaning up exports older than {$days} days...");
        
        $exports = VendorExport::where('created_at', '<', $cutoff)->get();
        
        if ($exports->isEmpty()) {
            $this->info('No exports found to clean up.');
            return self::SUCCESS;
        }

        $this->processExports($exports);
        $this->info("Cleaned up {$exports->count()} exports older than {$days} days");

        return self::SUCCESS;
    }

    private function getValidDays(): int
    {
        $days = (int) $this->option('days');
        
        if ($days < 1) {
            $this->warn('Days must be at least 1. Using default value of 7.');
            return 7;
        }
        
        return $days;
    }

    private function processExports($exports): void
    {
        $bar = $this->output->createProgressBar($exports->count());
        $bar->start();

        foreach ($exports as $export) {
            $this->deleteExportFile($export);
            $export->delete();
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    private function deleteExportFile($export): void
    {
        if (!$export->path) {
            return;
        }

        try {
            if (Storage::disk('local')->exists($export->path)) {
                Storage::disk('local')->delete($export->path);
            }
        } catch (\Exception $e) {
            $this->warn("Failed to delete file: {$export->path}");
        }
    }
}