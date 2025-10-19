<?php

namespace App\Console\Commands;

use App\Models\VendorExport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupVendorExports extends Command
{
    protected $signature = 'vendor_exports:cleanup {--days=7}';
    protected $description = 'Remove vendor export files and DB rows older than X days';

    public function handle()
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);
        
        $exports = VendorExport::where('created_at', '<', $cutoff)->get();
        $deletedCount = $this->deleteExports($exports);
        
        $this->info("Cleaned up {$deletedCount} exports older than {$days} days");
        return 0;
    }

    private function deleteExports($exports)
    {
        $count = 0;
        
        foreach ($exports as $export) {
            $this->deleteExportFile($export);
            $export->delete();
            $count++;
        }
        
        return $count;
    }

    private function deleteExportFile($export)
    {
        if ($export->path && Storage::disk('local')->exists($export->path)) {
            Storage::disk('local')->delete($export->path);
        }
    }
}