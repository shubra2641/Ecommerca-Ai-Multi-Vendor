<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductSerial;
use Illuminate\Http\Request;

class ProductSerialController extends Controller
{
    public function allSerials()
    {
        $serials = ProductSerial::with('product')->orderBy('id', 'desc')->paginate(50);

        return view('admin.products.serials.all', compact('serials'));
    }

    public function index(Product $product)
    {
        $serials = $product->serials()->orderBy('id', 'desc')->paginate(50);

        return view('admin.products.serials.index', compact('product', 'serials'));
    }

    public function import(Request $request, Product $product)
    {
        $request->validate(['file' => 'nullable|file', 'serials' => 'nullable|string']);
        $list = [];
        if ($request->hasFile('file')) {
            $content = file_get_contents($request->file('file')->getRealPath());
            $lines = preg_split('/\r?\n/', $content);
            foreach ($lines as $line) {
                $val = trim($line);
                if ($val !== '') {
                    $list[] = $val;
                }
            }
        } else {
            $raw = $request->input('serials', '');
            $lines = preg_split('/\r?\n/', $raw);
            foreach ($lines as $line) {
                $val = trim($line);
                if ($val !== '') {
                    $list[] = $val;
                }
            }
        }
        foreach ($list as $serialNumber) {
            if (! ProductSerial::where('product_id', $product->id)->where('serial', $serialNumber)->exists()) {
                ProductSerial::create(['product_id' => $product->id, 'serial' => $serialNumber]);
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
