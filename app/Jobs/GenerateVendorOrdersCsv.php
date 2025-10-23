<?php

namespace App\Jobs;

use App\Mail\VendorExportReady;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\VendorExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class GenerateVendorOrdersCsv implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $vendorExportId;

    public array $filters;

    public function __construct(int $vendorExportId, array $filters = [])
    {
        $this->vendorExportId = $vendorExportId;
        $this->filters = $filters;
    }

    public function handle()
    {
        $export = VendorExport::find($this->vendorExportId);
        if (! $export) {
            return;
        }
        $vendor = User::find($export->vendor_id);
        if (! $vendor) {
            return;
        }

        $q = OrderItem::with('order', 'product')
            ->whereHas('product', fn ($qq) => $qq->where('vendor_id', $export->vendor_id));

        if (! empty($this->filters['status'])) {
            $q->whereHas('order', fn ($qo) => $qo->where('status', $this->filters['status']));
        }
        if (! empty($this->filters['start_date'])) {
            $q->whereHas('order', fn ($qo) => $qo->whereDate('created_at', '>=', $this->filters['start_date']));
        }
        if (! empty($this->filters['end_date'])) {
            $q->whereHas('order', fn ($qo) => $qo->whereDate('created_at', '<=', $this->filters['end_date']));
        }

        $filename = 'vendor_orders_' . $export->vendor_id . '_' . date('Ymd_His') . '_' . Str::random(6) . '.csv';
        $temp = tempnam(sys_get_temp_dir(), 'vendor_exp_');
        $handle = fopen($temp, 'w');
        fputcsv($handle, ['order_id', 'order_date', 'product', 'quantity', 'total_price', 'status']);

        $q->chunk(200, function ($items) use ($handle) {
            foreach ($items as $it) {
                fputcsv($handle, [
                    $it->order_id,
                    $it->order?->created_at?->format('Y-m-d H:i'),
                    Str::limit($it->product?->name ?? '', 120),
                    $it->qty ?? $it->quantity ?? 1,
                    number_format((float) (($it->price ?? 0) * ($it->qty ?? $it->quantity ?? 1)), 2),
                    $it->order?->status ?? '',
                ]);
            }
        });

        fclose($handle);

        $storagePath = 'vendor_exports/' . $filename;
        Storage::disk('local')->putFileAs('vendor_exports', new \Illuminate\Http\File($temp), $filename);
        // update export record
        $export->update([
            'filename' => $filename,
            'path' => $storagePath,
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        @unlink($temp);

        // create temporary signed URL (2 hours)
        $signed = URL::temporarySignedRoute(
            'vendor.orders.export-file',
            now()->addHours(2),
            ['filename' => $filename]
        );

        // queue mail to vendor with link
        try {
            Mail::to($vendor->email)->queue(new VendorExportReady($signed, $filename));
        } catch (\Throwable $e) {
            // swallow mail errors silently in job - they will be logged by framework
        }
    }
}
