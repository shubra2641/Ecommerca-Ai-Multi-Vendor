<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\Governorate;
use App\Models\ShippingGroup;
use App\Models\ShippingGroupLocation;
use App\Services\HtmlSanitizer;
use Illuminate\Http\Request;

class ShippingGroupController extends Controller
{
    public function index()
    {
        $groups = ShippingGroup::withCount('locations')->paginate(20);

        return view('admin.shipping.index', compact('groups'));
    }

    public function create()
    {
        return view('admin.shipping.create', ['countries' => Country::where('active', 1)->get()]);
    }

    public function store(Request $request, HtmlSanitizer $sanitizer)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'default_price' => 'nullable|numeric',
            'estimated_days' => 'nullable|integer',
            'active' => 'sometimes|boolean',
            'locations' => 'nullable|array',
        ]);

        if (isset($data['name']) && is_string($data['name'])) {
            $data['name'] = $sanitizer->clean($data['name']);
        }

        $group = ShippingGroup::create($data + ['active' => (bool) ($data['active'] ?? true)]);

        if (! empty($data['locations'])) {
            $seen = [];
            $overlaps = [];
            foreach ($data['locations'] as $loc) {
                $clean = $this->cleanLocationInput($loc);
                if ($clean) {
                    $key = ($clean['country_id'] ?: '0') . '-' .
                        ($clean['governorate_id'] ?: '0') . '-' .
                        ($clean['city_id'] ?: '0');
                    if (isset($seen[$key])) {
                        $overlaps[] = $key;
                    }
                    $seen[$key] = true;
                    $clean['shipping_group_id'] = $group->id;
                    ShippingGroupLocation::create($clean);
                }
            }
            if ($overlaps) {
                $message = __(
                    'Some location rows were duplicated and may override each other: :c',
                    ['c' => implode(', ', $overlaps)]
                );
                session()->flash('warning', $message);
            }
        }

        return redirect()->route('admin.shipping.index')->with(
            'success',
            __('Shipping group created')
        );
    }

    public function edit(ShippingGroup $shipping)
    {
        $countries = Country::where('active', 1)->get();
        $locations = $shipping->locations()->with(['country', 'governorate', 'city'])->get();

        return view('admin.shipping.edit', compact('shipping', 'countries', 'locations'));
    }

    public function update(Request $request, ShippingGroup $shipping, HtmlSanitizer $sanitizer)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'default_price' => 'nullable|numeric',
            'estimated_days' => 'nullable|integer',
            'active' => 'sometimes|boolean',
            'locations' => 'nullable|array',
        ]);

        if (isset($data['name']) && is_string($data['name'])) {
            $data['name'] = $sanitizer->clean($data['name']);
        }

        $shipping->update($data + ['active' => (bool) ($data['active'] ?? $shipping->active)]);

        // Simple replace of locations for now
        if (isset($data['locations'])) {
            $shipping->locations()->delete();
            $seen = [];
            $overlaps = [];
            foreach ($data['locations'] as $loc) {
                $clean = $this->cleanLocationInput($loc);
                if ($clean) {
                    $key = ($clean['country_id'] ?: '0') . '-' .
                        ($clean['governorate_id'] ?: '0') . '-' .
                        ($clean['city_id'] ?: '0');
                    if (isset($seen[$key])) {
                        $overlaps[] = $key;
                    }
                    $seen[$key] = true;
                    $clean['shipping_group_id'] = $shipping->id;
                    ShippingGroupLocation::create($clean);
                }
            }
            if ($overlaps) {
                $message = __(
                    'Some location rows were duplicated and may override each other: :c',
                    ['c' => implode(', ', $overlaps)]
                );
                session()->flash('warning', $message);
            }
        }

        return redirect()->route('admin.shipping.index')->with(
            'success',
            __('Shipping group updated')
        );
    }

    public function destroy(ShippingGroup $shipping)
    {
        $shipping->delete();

        return redirect()->route('admin.shipping.index')->with('success', __('Deleted'));
    }

    private function cleanLocationInput(array $loc): ?array
    {
        $countryId = $loc['country_id'] ?? null;
        $govId = $loc['governorate_id'] ?? null;
        $cityId = $loc['city_id'] ?? null;
        $price = $loc['price'] ?? null;
        $days = $loc['estimated_days'] ?? null;

        if (! $countryId) {
            return null; // country is required for any location rule
        }
        $country = Country::find($countryId);
        if (! $country) {
            return null;
        }

        if ($govId) {
            $gov = Governorate::where('id', $govId)->where('country_id', $countryId)->first();
            if (! $gov) {
                return null; // invalid governorate for country
            }
            if ($cityId) {
                $city = City::where('id', $cityId)->whereHas('governorate', function ($q) use ($govId, $countryId): void {
                    $q->where('id', $govId)->where('country_id', $countryId);
                })->first();
                if (! $city) {
                    return null; // invalid city
                }
            } else {
                $cityId = null; // entire governorate
            }
        } else {
            // no governorate -> ignore provided city (can't map reliably without its governorate)
            $govId = null;
            $cityId = null;
        }

        return [
            'country_id' => $countryId,
            'governorate_id' => $govId,
            'city_id' => $cityId,
            'price' => is_numeric($price) ? $price : null,
            'estimated_days' => is_numeric($days) ? (int) $days : null,
        ];
    }
}
