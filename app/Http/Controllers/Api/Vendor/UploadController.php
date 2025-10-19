<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function image(Request $r)
    {
        $r->validate([
            'file' => ['required', 'image', 'max:2048'], // 2MB
        ]);
        $file = $r->file('file');
        $path = $file->storeAs(
            'uploads/vendor/' . date('Y/m'),
            Str::random(20) . '.' . $file->getClientOriginalExtension(),
            'public'
        );
        $url = Storage::disk('public')->url($path);

        return response()->json(['url' => $url, 'path' => $path]);
    }
}
