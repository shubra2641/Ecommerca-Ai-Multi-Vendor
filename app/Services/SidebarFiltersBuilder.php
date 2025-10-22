<?php

namespace App\Services;

use Illuminate\Http\Request;

class SidebarFiltersBuilder
{
    public function build(Request $request, $brandList): array
    {
        $selectedBrands = (array) $request->get('brand', []);

        return [
            'selectedBrands' => $selectedBrands,
            'brandList' => $brandList,
        ];
    }
}
