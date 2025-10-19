<?php

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
        $cacheKey = "interest_top_products:$limit";
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
        // Validate inputs and load previous filters from session if not provided
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
        $from = $submitted ? $request->get('from') : ($saved['from'] ?? null);
        $to = $submitted ? $request->get('to') : ($saved['to'] ?? null);
        $query = PriceChange::where('product_id', $product->id);
        if ($from) {
            // use whereDate to avoid string concatenation and ensure correct date handling
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }
        $changes = $query->orderBy('created_at')->get();
        // Basic metrics
        $count = $changes->count();
        $first = $changes->first();
        $last = $changes->last();
        $netChange = $first && $last ? $last->new_price - $first->old_price : 0;
        $netPercent = ($first && $last && $first->old_price > 0) ?
            (($last->new_price - $first->old_price) / $first->old_price) * 100 : 0;
        $biggestDrop = $changes->sortBy('percent')->first(); // most negative percent
        $maxIncrease = $changes->sortByDesc('percent')->first();
        // Interest overlay: active interest counts evolution (approx by grouping creation times)
        $interestSeries = \App\Models\ProductInterest::selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->where('product_id', $product->id)
            ->whereNull('unsubscribed_at')
            ->groupBy('d')->orderBy('d')->get();
        // Moving averages (compute both SMA & EMA) with window
        $window = (int) ($submitted ? $request->get('ma', 5) : ($saved['ma'] ?? 5));
        if ($window < 2) {
            $window = 5;
        }
        if ($window > 60) {
            $window = 60;
        }
        $showSma = $submitted ? $request->boolean('show_sma') : ($saved['show_sma'] ?? true);
        $showEma = $submitted ? $request->boolean('show_ema') : ($saved['show_ema'] ?? false);
        $sma = $ema = [];
        if ($changes->count()) {
            $prices = $changes->pluck('new_price')->values();
            // SMA
            for ($i = 0; $i < $prices->count(); $i++) {
                $start = max(0, $i - $window + 1);
                $slice = $prices->slice($start, $i - $start + 1);
                $sma[$i] = round($slice->avg(), 2);
            }
            // EMA
            $k = 2 / ($window + 1);
            for ($i = 0; $i < $prices->count(); $i++) {
                if ($i === 0) {
                    $ema[$i] = round($prices[$i], 2);

                    continue;
                }
                $ema[$i] = round($prices[$i] * $k + $ema[$i - 1] * (1 - $k), 2);
            }
        }
        // Largest absolute drop in money value
        $largestAbsDrop = null;
        $largestAbsDropDiff = 0.0;
        foreach ($changes as $chg) {
            $diff = $chg->old_price - $chg->new_price; // only positive indicates drop
            if ($diff > $largestAbsDropDiff) {
                $largestAbsDropDiff = $diff;
                $largestAbsDrop = $chg;
            }
        }
        // Threshold selection
        $threshold = (float) ($submitted ?
            $request->get('thr', config('interest.price_drop_min_percent', 5)) :
            ($saved['thr'] ?? config('interest.price_drop_min_percent', 5))
        );
        if ($threshold < 1) {
            $threshold = 1;
        } if ($threshold > 90) {
            $threshold = 90;
        }
        // Persist filters
        session(['price_chart.filters' => [
            'from' => $from,
            'to' => $to,
            'ma' => $window,
            'show_sma' => $showSma,
            'show_ema' => $showEma,
            'thr' => $threshold,
        ]]);

        return view('admin.notify.price-chart', compact(
            'product',
            'changes',
            'count',
            'netChange',
            'netPercent',
            'biggestDrop',
            'maxIncrease',
            'interestSeries',
            'from',
            'to',
            'window',
            'sma',
            'ema',
            'showSma',
            'showEma',
            'largestAbsDrop',
            'largestAbsDropDiff',
            'threshold'
        ));
    }
}
