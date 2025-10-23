<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VendorExport;
use Illuminate\Http\Request;

class VendorExportController extends Controller
{
    public function index(Request $r)
    {
        $q = VendorExport::with('vendor')->latest();
        if ($r->filled('vendor')) {
            $q->where('vendor_id', $r->input('vendor'));
        }
        $items = $q->paginate(30)->withQueryString();

        return view('admin.vendor_exports.index', compact('items'));
    }

    public function download(VendorExport $export)
    {
        if (! $export->path || ! file_exists(storage_path('app/'.$export->path))) {
            abort(404);
        }

        return response()->download(storage_path('app/'.$export->path), $export->filename);
    }
}
