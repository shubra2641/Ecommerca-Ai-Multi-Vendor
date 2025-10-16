<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Models\BalanceHistory;
use App\Models\OrderItem;
use App\Services\CommissionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DistributeOrderProceedsListener
{
    public function handle(OrderPaid $event): void
    {
        $order = $event->order->loadMissing('items.product', 'items.product.vendor');

        // idempotency: skip if already processed
        if ($order->vendor_distribution_processed) {
            Log::info('DistributeOrderProceedsListener skipped: already processed for order ' . $order->id);

            return;
        }

        try {
            DB::transaction(function () use ($order) {
                // Aggregate per-vendor earnings
                $vendorAmounts = [];

                foreach ($order->items as $item) {
                    /** @var OrderItem $item */
                    $product = $item->product;
                    $vendorId = $product?->vendor_id;

                    // use stored vendor_earnings (computed at checkout)
                    $earnings = (float) $item->vendor_earnings;

                    if ($vendorId) {
                        if (! isset($vendorAmounts[$vendorId])) {
                            $vendorAmounts[$vendorId] = 0.0;
                        }
                        $vendorAmounts[$vendorId] += $earnings;
                    }
                }

                // Shipping and taxes are considered platform/admin revenue by default

                Log::info('DistributeOrderProceedsListener processing order ' . $order->id . ' computed vendorAmounts: ' . json_encode($vendorAmounts));

                // Credit each vendor or hold if within return window
                foreach ($vendorAmounts as $vendorId => $amount) {
                    if ($amount <= 0) {
                        continue;
                    }
                    $vendor = \App\Models\User::find($vendorId);
                    if (! $vendor) {
                        Log::warning('DistributeOrderProceedsListener: vendor not found id=' . $vendorId . ' for order ' . $order->id);

                        continue;
                    }

                    // Determine if any items for this vendor in this order are still within refund window
                    $vendorItems = $order->items->filter(fn ($it) => ($it->product?->vendor_id ?? null) == $vendorId);
                    $hasHeld = false;
                    foreach ($vendorItems as $vi) {
                        if ($vi->isWithinReturnWindow()) {
                            $hasHeld = true;
                            break;
                        }
                    }

                    if ($hasHeld) {
                        // Create a held credit record (do not increment available balance)
                        BalanceHistory::createTransaction(
                            $vendor,
                            BalanceHistory::TYPE_CREDIT,
                            $amount,
                            (float) $vendor->balance,
                            (float) $vendor->balance,
                            'Held credit for Order #' . $order->id . ' (refund window active)',
                            null,
                            $order
                        );
                        Log::info('DistributeOrderProceedsListener held vendor ' . $vendorId . ' amount ' . $amount . ' for order ' . $order->id);
                    } else {
                        $previous = (float) $vendor->balance;
                        $vendor->increment('balance', $amount);
                        $vendor->refresh();

                        BalanceHistory::createTransaction(
                            $vendor,
                            BalanceHistory::TYPE_CREDIT,
                            $amount,
                            $previous,
                            (float) $vendor->balance,
                            'Order #' . $order->id,
                            null,
                            $order
                        );

                        Log::info('DistributeOrderProceedsListener credited vendor ' . $vendorId . ' amount ' . $amount . ' for order ' . $order->id);
                    }
                }

                // Credit admin (platform) with shipping/taxes/commissions as configured
                $platformShare = 0.0;
                // Shipping
                $platformShare += (float) $order->shipping_price;
                // Commission amounts from items (sum of vendor_commission_amount)
                foreach ($order->items as $it) {
                    // Use stored commission if available, otherwise compute it now
                    $commission = (float) $it->vendor_commission_amount;
                    if ($commission <= 0 && $it->product) {
                        try {
                            $break = CommissionService::breakdown($it->product, (int) $it->qty, (float) $it->price);
                            $commission = (float) ($break['commission'] ?? 0.0);
                        } catch (\Throwable $e) {
                            Log::warning('DistributeOrderProceedsListener: failed computing commission fallback for order ' . $order->id . ' item ' . $it->id . ': ' . $e->getMessage());
                        }
                    }
                    $platformShare += $commission;
                }

                if ($platformShare > 0) {
                    $admin = \App\Models\User::where('role', 'admin')->first();
                    if ($admin) {
                        $prevAdmin = (float) $admin->balance;
                        $admin->increment('balance', $platformShare);
                        $admin->refresh();

                        BalanceHistory::createTransaction(
                            $admin,
                            BalanceHistory::TYPE_CREDIT,
                            $platformShare,
                            $prevAdmin,
                            (float) $admin->balance,
                            'Platform share for Order #' . $order->id,
                            null,
                            $order
                        );

                        Log::info('DistributeOrderProceedsListener credited admin id ' . $admin->id . ' amount ' . $platformShare . ' for order ' . $order->id);
                    } else {
                        Log::warning('DistributeOrderProceedsListener: no admin user found to credit platform share for order ' . $order->id);
                    }
                }

                // mark order as distributed
                $order->vendor_distribution_processed = true;
                $order->save();
            });
        } catch (\Throwable $e) {
            Log::error('DistributeOrderProceedsListener failed for order ' . ($order->id ?? 'n/a') . ': ' . $e->getMessage());
            throw $e;
        }
    }
}
