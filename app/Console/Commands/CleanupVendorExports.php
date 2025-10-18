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
        foreach ($exports as $e) {
            if ($e->path && Storage::disk('local')->exists($e->path)) {
                Storage::disk('local')->delete($e->path);
            }
            $e->delete();
        }
        $this->info('Cleaned up ' . count($exports) . ' exports older than ' . $days . ' days');

        return 0;
    }
}
