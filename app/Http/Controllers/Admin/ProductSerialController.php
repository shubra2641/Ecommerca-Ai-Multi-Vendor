<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Services\HtmlSanitizer;
use Illuminate\Http\Request;

class ProductSerialController extends Controller
{
    public function index(Product $product)
    {
        $serials = $product->serials()->orderBy('id', 'desc')->paginate(50);

        return view('admin.products.serials.index', compact('product', 'serials'));
    }

    public function import(Request $r, Product $product, HtmlSanitizer $sanitizer)
    {
        $r->validate(['file' => 'nullable|file', 'serials' => 'nullable|string']);
        $list = [];
        if ($r->hasFile('file')) {
            $content = file_get_contents($r->file('file')->getRealPath());
            $lines = preg_split('/\r?\n/', $content);
            foreach ($lines as $l) {
                $val = trim($l);
                if ($val !== '') {
                    $list[] = $sanitizer->clean($val);
                }
            }
        } else {
            $raw = $r->input('serials', '');
            $lines = preg_split('/\r?\n/', $raw);
            foreach ($lines as $l) {
                $val = trim($l);
                if ($val !== '') {
                    $list[] = $sanitizer->clean($val);
                }
            }
        }
        $created = 0;
        foreach ($list as $s) {
            if (! ProductSerial::where('product_id', $product->id)->where('serial', $s)->exists()) {
                ProductSerial::create(['product_id' => $product->id, 'serial' => $s]);
                $created++;
            }
        }

        return back()->with('success', __('Imported {$created} serials'));
    }

    public function export(Product $product)
    {
        $filename = 'product_'.$product->id.'_serials_'.date('Ymd_His').'.csv';
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename={$filename}"];
        $callback = function () use ($product) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['id', 'serial', 'sold_at']);
            $product->serials()->chunk(200, function ($rows) use ($out) {
                foreach ($rows as $r) {
                    fputcsv($out, [$r->id, $r->serial, $r->sold_at]);
                }
            });
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function markSold(Product $product, ProductSerial $serial)
    {
        if ($serial->product_id !== $product->id) {
            abort(404);
        }
        if ($serial->sold_at) {
            return back()->with('warning', __('Already sold'));
        }
        $serial->sold_at = now();
        $serial->save();

        return back()->with('success', __('Marked as sold'));
    }
}
