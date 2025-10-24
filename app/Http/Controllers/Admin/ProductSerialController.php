<?php

declare(strict_types=1);

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
        foreach ($list as $s) {
            if (! ProductSerial::where('product_id', $product->id)->where('serial', $s)->exists()) {
                ProductSerial::create(['product_id' => $product->id, 'serial' => $s]);
            }
        }

        return back()->with('success', __('Imported successfully'));
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
