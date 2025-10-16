<?php

namespace App\Console\Commands;

use App\Models\OrderItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class NormalizeReturnImages extends Command
{
    protected $signature = 'returns:normalize-images';

    protected $description = 'Normalize absolute local image URLs stored in order_items.meta to storage-relative paths';

    public function handle()
    {
        $this->info('Scanning order items...');
        $count = 0;
        OrderItem::chunk(200, function ($items) use (&$count) {
            foreach ($items as $item) {
                $meta = $item->meta ?? [];
                $changed = false;
                foreach (['user_images', 'admin_images'] as $key) {
                    if (! empty($meta[$key]) && is_array($meta[$key])) {
                        foreach ($meta[$key] as $i => $img) {
                            if (is_string($img) && preg_match('#^https?://#i', $img)) {
                                // If URL contains /storage/ then try to convert
                                $u = $img;
                                $pos = stripos($u, '/storage/');
                                if ($pos !== false) {
                                    $rel = substr($u, $pos + strlen('/storage/'));
                                    // if file exists in public disk
                                    if (Storage::disk('public')->exists($rel)) {
                                        $meta[$key][$i] = $rel;
                                        $changed = true;
                                    }
                                }
                            }
                        }
                    }
                }
                if ($changed) {
                    $item->meta = $meta;
                    $item->save();
                    $count++;
                }
            }
        });

        $this->info("Normalized {$count} items.");

        return 0;
    }
}
