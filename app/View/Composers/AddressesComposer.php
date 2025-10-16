<?php

namespace App\View\Composers;

use App\View\Builders\AddressesViewBuilder;
use Illuminate\View\View;

class AddressesComposer
{
    public function compose(View $view): void
    {
        $data = $view->getData();
        if (isset($data['addresses'])) {
            [$default, $others] = AddressesViewBuilder::splitAddresses($data['addresses']);
            $view->with('addrDefault', $default)->with('addrOthers', $others);
        }
    }
}
