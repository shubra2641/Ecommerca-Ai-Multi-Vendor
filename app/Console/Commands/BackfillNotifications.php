<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Notifications\DatabaseNotification;

class BackfillNotifications extends Command
{
    protected $signature = 'notifications:backfill {--dry-run}';

    protected $description = 'Backfill title/message fields for existing database notifications when missing';

    public function handle()
    {
        $dry = $this->option('dry-run');
        $q = DatabaseNotification::query()->whereRaw("JSON_EXTRACT(data, '$.title') IS NULL OR JSON_EXTRACT(data, '$.message') IS NULL");
        $count = $q->count();
        $this->info("Found $count notifications missing title or message");
        if ($count === 0) {
            return 0;
        }
        $bar = $this->output->createProgressBar($count);
        $bar->start();
        foreach ($q->cursor() as $n) {
            $updated = false;
            $data = $n->data ?? [];
            $type = $data['type'] ?? null;
            if (empty($data['title'])) {
                $title = $this->guessTitle($type, $data);
                if ($title) {
                    $data['title'] = $title;
                    $updated = true;
                }
            }
            if (empty($data['message'])) {
                $message = $this->guessMessage($type, $data);
                if ($message) {
                    $data['message'] = $message;
                    $updated = true;
                }
            }
            if ($updated) {
                if ($dry) {
                    $this->line(" would update notification {$n->id} -> title={$data['title']}, message={$data['message']}");
                } else {
                    $n->data = $data;
                    $n->save();
                }
            }
            $bar->advance();
        }
        $bar->finish();
        $this->line('');
        $this->info('Done');

        return 0;
    }

    protected function guessTitle($type, $data)
    {
        if (! $type) {
            return null;
        }
        switch ($type) {
            case 'order_created':
                return __('New order placed');
            case 'payment_status':
                return __('Payment');
            case 'user_registered':
                return __('New user registered');
            case 'vendor_registered':
                return __('New vendor registered');
            case 'stock_low':
                return __('Product stock low');
            case 'review_submitted':
                return __('New product review');
            case 'product_interest':
                return __('Product interest');
            case 'return_request':
                return __('Return request');
            default:
                return ucwords(str_replace(['_', '-'], ' ', $type));
        }
    }

    protected function guessMessage($type, $data)
    {
        $id = $data['order_id'] ?? $data['payment_id'] ?? $data['item_id'] ?? null;
        switch ($type) {
            case 'order_created':
                return __('Order #:id placed', ['id' => $id ?? '']);
            case 'payment_status':
                return __('Payment :id status :status', ['id' => $data['payment_id'] ?? '', 'status' => $data['status'] ?? '']);
            case 'user_registered':
                return $data['name'] ?? ($data['email'] ?? '');
            case 'vendor_registered':
                return $data['name'] ?? ($data['email'] ?? '');
            case 'stock_low':
                return $data['message'] ?? null;
            case 'review_submitted':
                return $data['message'] ?? null;
            case 'product_interest':
                return $data['message'] ?? null;
            case 'return_request':
                return $data['message'] ?? null;
            default:
                return $data['message'] ?? null;
        }
    }
}
