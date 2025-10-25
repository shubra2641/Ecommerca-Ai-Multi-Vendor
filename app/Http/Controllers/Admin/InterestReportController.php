<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PriceChange;
use App\Models\Product;
use App\Models\ProductInterest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InterestReportController extends Controller
{
    public function topProducts(Request $request)
    {
        $request->validate([
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $limit = (int) ($request->get('limit', 20));
        $cacheKey = "interest_top_products:{$limit}";
        $rows = cache()->remember($cacheKey, 300, function () use ($limit) {
            return ProductInterest::select('product_id', DB::raw('COUNT(*) as total'))
                ->whereNull('unsubscribed_at')
                ->groupBy('product_id')
                ->orderByDesc('total')
                ->take($limit)
                ->get();
        });
        $products = Product::whereIn('id', $rows->pluck('product_id'))->get()->keyBy('id');

        return view('admin.notify.top-products', compact('rows', 'products', 'limit'));
    }

    public function priceChart(Product $product, Request $request)
    {
        $filters = $this->validateAndGetFilters($request);

        $changes = $this->getPriceChanges($product, $filters);
        $metrics = $this->calculateBasicMetrics($changes);
        $interestSeries = $this->getInterestSeries($product);
        $movingAverages = $this->calculateMovingAverages($changes, $filters);
        $largestDrop = $this->findLargestAbsoluteDrop($changes);
        $threshold = $this->getThreshold($filters);

        $this->persistFilters($filters);

        return view('admin.notify.price-chart', array_merge([
            'product' => $product,
            'changes' => $changes,
            'interestSeries' => $interestSeries,
            'largestAbsDrop' => $largestDrop['change'],
            'largestAbsDropDiff' => $largestDrop['diff'],
            'threshold' => $threshold,
        ], $metrics, $movingAverages, $filters));
    }

    private function validateAndGetFilters(Request $request): array
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'ma' => ['nullable', 'integer', 'min:2', 'max:60'],
            'ma_type' => ['nullable', 'string', 'max:20'],
            'thr' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'show_sma' => ['nullable', 'boolean'],
            'show_ema' => ['nullable', 'boolean'],
        ]);

        $saved = session('price_chart.filters', []);
        $submitted = $request->hasAny(['from', 'to', 'ma', 'ma_type', 'thr', 'show_sma', 'show_ema']);

        return [
            'from' => $submitted ? $request->get('from') : ($saved['from'] ?? null),
            'to' => $submitted ? $request->get('to') : ($saved['to'] ?? null),
            'ma' => $submitted ? $request->get('ma', 5) : ($saved['ma'] ?? 5),
            'show_sma' => $submitted ? $request->boolean('show_sma') : ($saved['show_sma'] ?? true),
            'show_ema' => $submitted ? $request->boolean('show_ema') : ($saved['show_ema'] ?? false),
            'thr' => $submitted ? $request->get('thr') : ($saved['thr'] ?? config('interest.price_drop_min_percent', 5)),
        ];
    }

    private function getPriceChanges(Product $product, array $filters)
    {
        $query = PriceChange::where('product_id', $product->id);

        if ($filters['from']) {
            $query->whereDate('created_at', '>=', $filters['from']);
        }

        if ($filters['to']) {
            $query->whereDate('created_at', '<=', $filters['to']);
        }

        return $query->orderBy('created_at')->get();
    }

    private function calculateBasicMetrics($changes): array
    {
        $count = $changes->count();
        $first = $changes->first();
        $last = $changes->last();

        $netChange = $first && $last ? $last->new_price - $first->old_price : 0;
        $netPercent = $first && $last && $first->old_price > 0
            ? ($last->new_price - $first->old_price) / $first->old_price * 100
            : 0;

        $biggestDrop = $changes->sortBy('percent')->first();
        $maxIncrease = $changes->sortByDesc('percent')->first();

        return [
            'count' => $count,
            'netChange' => $netChange,
            'netPercent' => $netPercent,
            'biggestDrop' => $biggestDrop,
            'maxIncrease' => $maxIncrease,
        ];
    }

    private function getInterestSeries(Product $product)
    {
        return ProductInterest::selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->where('product_id', $product->id)
            ->whereNull('unsubscribed_at')
            ->groupBy('d')
            ->orderBy('d')
            ->get();
    }

    private function calculateMovingAverages($changes, array $filters): array
    {
        $window = (int) $filters['ma'];
        $window = max(2, min(60, $window));

        $sma = $ema = [];

        if ($changes->isEmpty()) {
            return [
                'window' => $window,
                'sma' => $sma,
                'ema' => $ema,
                'showSma' => $filters['show_sma'],
                'showEma' => $filters['show_ema'],
            ];
        }

        $prices = $changes->pluck('new_price')->values();
        $pricesCount = $prices->count();

        // SMA
        for ($i = 0; $i < $pricesCount; $i++) {
            $start = max(0, $i - $window + 1);
            $slice = $prices->slice($start, $i - $start + 1);
            $sma[$i] = round($slice->avg(), 2);
        }

        // EMA
        $smoothingFactor = 2 / ($window + 1);
        for ($i = 0; $i < $pricesCount; $i++) {
            if ($i === 0) {
                $ema[$i] = round($prices[$i], 2);
                continue;
            }
            $ema[$i] = round($prices[$i] * $smoothingFactor + $ema[$i - 1] * (1 - $smoothingFactor), 2);
        }

        return [
            'window' => $window,
            'sma' => $sma,
            'ema' => $ema,
            'showSma' => $filters['show_sma'],
            'showEma' => $filters['show_ema'],
        ];
    }

    private function findLargestAbsoluteDrop($changes): array
    {
        $largestAbsDrop = null;
        $largestAbsDropDiff = 0.0;

        foreach ($changes as $chg) {
            $diff = $chg->old_price - $chg->new_price;
            if ($diff > $largestAbsDropDiff) {
                $largestAbsDropDiff = $diff;
                $largestAbsDrop = $chg;
            }
        }

        return [
            'change' => $largestAbsDrop,
            'diff' => $largestAbsDropDiff,
        ];
    }

    private function getThreshold(array $filters): float
    {
        $threshold = (float) $filters['thr'];
        return max(1, min(90, $threshold));
    }

    private function persistFilters(array $filters): void
    {
        session([
            'price_chart.filters' => [
                'from' => $filters['from'],
                'to' => $filters['to'],
                'ma' => $filters['ma'],
                'show_sma' => $filters['show_sma'],
                'show_ema' => $filters['show_ema'],
                'thr' => $filters['thr'],
            ],
        ]);
    }
}
