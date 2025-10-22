<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CurrencyController extends Controller
{
    public function switch(Request $r)
    {
        $data = $r->validate(['code' => 'required|string|max:5']);
        if (Schema::hasTable('currencies')) {
            $cur = \App\Models\Currency::where('code', $data['code'])->first();
            if ($cur) {
                session(['currency_id' => $cur->id]);

                return response()->json([
                    'status' => 'ok',
                    'currency' => [
                        'id' => $cur->id,
                        'code' => $cur->code,
                        'symbol' => $cur->symbol,
                        'exchange_rate' => (float) $cur->exchange_rate,
                    ],
                ]);
            }
        }

        return response()->json(['status' => 'error'], 404);
    }
}
