<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\VendorWithdrawal;
use App\Models\BalanceHistory;
use Illuminate\Support\Facades\DB;

final class VendorDashboardService
{
    public function getDashboardData(int $vendorId): array
    {
        $totalSales = $this->getTotalSales($vendorId);
        $totalWithdrawals = $this->getTotalWithdrawals($vendorId);

        return [
            'total_sales' => (float) $totalSales,
            'total_orders' => $this->getOrdersCount($vendorId),
            'pending_withdrawals' => $this->getPendingWithdrawals($vendorId),
            'total_products' => $this->getProductsCount($vendorId),
            'active_products' => $this->getActiveProductsCount($vendorId),
            'pending_products' => $this->getPendingProductsCount($vendorId),
            'actual_balance' => $this->getActualBalance($totalSales, $totalWithdrawals),
            'recent_orders' => $this->getRecentOrders($vendorId),
            'total_withdrawn' => $this->getTotalWithdrawn($vendorId),
            'monthly_approved' => $this->getMonthlyApproved($vendorId),
        ];
    }

    private function getTotalSales(int $vendorId): float
    {
        return (float) OrderItem::whereHas('product', fn($q) => $q->where('vendor_id', $vendorId))
            ->whereHas('order', fn($qo) => $qo->whereIn('status', ['completed', 'delivered', 'shipped']))
            ->sum(DB::raw('(price * COALESCE(qty, 1))'));
    }

    private function getOrdersCount(int $vendorId): int
    {
        return OrderItem::whereHas('product', fn($q) => $q->where('vendor_id', $vendorId))
            ->distinct('order_id')
            ->count('order_id');
    }

    private function getPendingWithdrawals(int $vendorId): float
    {
        return (float) VendorWithdrawal::where('user_id', $vendorId)
            ->where('status', 'pending')
            ->sum('amount');
    }

    private function getProductsCount(int $vendorId): int
    {
        return Product::where('vendor_id', $vendorId)->count();
    }

    private function getActiveProductsCount(int $vendorId): int
    {
        return Product::where('vendor_id', $vendorId)
            ->where('active', true)
            ->count();
    }

    private function getPendingProductsCount(int $vendorId): int
    {
        return Product::where('vendor_id', $vendorId)
            ->where('active', false)
            ->whereNull('rejection_reason')
            ->count();
    }

    private function getTotalWithdrawals(int $vendorId): float
    {
        return (float) VendorWithdrawal::where('user_id', $vendorId)
            ->where('status', 'completed')
            ->sum('amount');
    }

    private function getActualBalance(float $totalSales, float $totalWithdrawals): float
    {
        return $totalSales - $totalWithdrawals;
    }

    private function getRecentOrders(int $vendorId): \Illuminate\Database\Eloquent\Collection
    {
        return Order::whereHas('items.product', fn($q) => $q->where('vendor_id', $vendorId))
            ->latest('created_at')
            ->limit(5)
            ->get();
    }

    private function getBalanceHistory(int $vendorId): array
    {
        return BalanceHistory::where('user_id', $vendorId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->toArray();
    }

    private function getTotalWithdrawn(int $vendorId): int
    {
        return (int) BalanceHistory::where('user_id', $vendorId)
            ->whereIn('type', ['withdrawal_approved', 'withdrawal_executed'])
            ->count();
    }

    private function getMonthlyApproved(int $vendorId): float
    {
        return (float) BalanceHistory::where('user_id', $vendorId)
            ->where('type', 'withdrawal_approved')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('amount');
    }
}
