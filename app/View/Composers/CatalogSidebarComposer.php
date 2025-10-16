<?php

namespace App\View\Composers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogSidebarComposer
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function compose(View $view): void
    {
        $selectedBrands = (array) $this->request->input('brand', []);
        $minPrice = $this->request->input('min_price');
        $maxPrice = $this->request->input('max_price');

        $view->with([
            'csSelectedBrands' => $selectedBrands,
            'csMinPrice' => is_numeric($minPrice) ? (float) $minPrice : null,
            'csMaxPrice' => is_numeric($maxPrice) ? (float) $maxPrice : null,
        ]);
    }
}
