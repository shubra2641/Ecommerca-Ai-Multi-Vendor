<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

final class VendorChartService
{
    /**
     * Generate sales chart data for the last 12 months
     */
    public function generateSalesChartData($vendorId): array
    {
        return collect(range(11, 0, -1))->mapWithKeys(function ($i) use ($vendorId) {
            $date = now()->subMonths($i);
            $monthKey = $date->format('M Y');

            $monthlySales = OrderItem::whereHas('product', fn($q) => $q->where('vendor_id', $vendorId))
                ->whereHas('order', fn($qo) => $qo->whereIn('status', ['completed', 'delivered', 'shipped']))
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum(DB::raw('(price * COALESCE(qty, 1))'));

            return [$monthKey => (float) $monthlySales];
        })->toArray();
    }

    /**
     * Generate orders chart data for the last 12 months
     */
    public function generateOrdersChartData($vendorId): array
    {
        return collect(range(11, 0, -1))->mapWithKeys(function ($i) use ($vendorId) {
            $date = now()->subMonths($i);
            $monthKey = $date->format('M Y');

            $monthlyOrders = OrderItem::whereHas('product', fn($q) => $q->where('vendor_id', $vendorId))
                ->whereHas('order', fn($qo) => $qo->whereIn('status', ['completed', 'delivered', 'shipped']))
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->distinct('order_id')
                ->count('order_id');

            return [$monthKey => (int) $monthlyOrders];
        })->toArray();
    }

    /**
     * Calculate sales growth percentage compared to previous month
     */
    public function calculateSalesGrowth($vendorId): float
    {
        $currentMonth = OrderItem::whereHas('product', fn($q) => $q->where('vendor_id', $vendorId))
            ->whereHas('order', fn($qo) => $qo->whereIn('status', ['completed', 'delivered', 'shipped']))
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum(DB::raw('(price * COALESCE(qty, 1))'));

        $previousMonth = OrderItem::whereHas('product', fn($q) => $q->where('vendor_id', $vendorId))
            ->whereHas('order', fn($qo) => $qo->whereIn('status', ['completed', 'delivered', 'shipped']))
            ->whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->sum(DB::raw('(price * COALESCE(qty, 1))'));

        if ($previousMonth === 0) {
            return $currentMonth > 0 ? 100.0 : 0.0;
        }

        return ($currentMonth - $previousMonth) / $previousMonth * 100;
    }

    /**
     * Calculate orders growth percentage compared to previous month
     */
    public function calculateOrdersGrowth($vendorId): float
    {
        $currentMonthOrders = OrderItem::whereHas('product', fn($q) => $q->where('vendor_id', $vendorId))
            ->whereHas('order', fn($qo) => $qo->whereIn('status', ['completed', 'delivered', 'shipped']))
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->distinct('order_id')
            ->count('order_id');

        $previousMonthOrders = OrderItem::whereHas('product', fn($q) => $q->where('vendor_id', $vendorId))
            ->whereHas('order', fn($qo) => $qo->whereIn('status', ['completed', 'delivered', 'shipped']))
            ->whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->distinct('order_id')
            ->count('order_id');

        if ($previousMonthOrders === 0) {
            return $currentMonthOrders > 0 ? 100.0 : 0.0;
        }

        return ($currentMonthOrders - $previousMonthOrders) / $previousMonthOrders * 100;
    }
}
