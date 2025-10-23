<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\Governorate;
use App\Models\ShippingZone;
use App\Services\HtmlSanitizer;
use Illuminate\Http\Request;

class ShippingZoneController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(ShippingZone::class, 'shipping_zone');
    }

    public function index()
    {
        $zones = ShippingZone::withCount('rules')->paginate(20);

        return view('admin.shipping_zones.index', compact('zones'));
    }

    public function create(Request $request)
    {
        $countries = Country::where('active', 1)->with(['governorates' => function ($query): void {
            $query->where('active', 1)->with(['cities' => function ($q): void {
                $q->where('active', 1);
            },
            ]);
        },
        ])->get();

        return view('admin.shipping_zones.create', [
            'countries' => $countries,
        ]);
    }

    public function store(Request $request, HtmlSanitizer $sanitizer)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'code' => 'nullable|string|max:50',
            'active' => 'sometimes|boolean',
            'rules' => 'nullable|array',
            'rules.*.country_id' => 'required_with:rules|exists:countries,id',
            'rules.*.governorate_id' => 'nullable|exists:governorates,id',
            'rules.*.city_id' => 'nullable|exists:cities,id',
            'rules.*.price' => 'required_with:rules|numeric|min:0',
            'rules.*.estimated_days' => 'required_with:rules|integer|min:1',
            'rules.*.active' => 'sometimes|boolean',
        ]);
        if (isset($data['name']) && is_string($data['name'])) {
            $data['name'] = $sanitizer->clean($data['name']);
        }
        if (isset($data['code']) && is_string($data['code'])) {
            $data['code'] = $sanitizer->clean($data['code']);
        }

        $zone = ShippingZone::create([
            'name' => $data['name'],
            'code' => $data['code'] ?? null,
            'active' => (bool) ($data['active'] ?? true),
        ]);
        $this->persistRules($zone, $data['rules'] ?? []);

        return redirect()->route('admin.shipping-zones.index')->with('success', __('Zone created'));
    }

    public function edit(ShippingZone $shipping_zone, Request $request)
    {
        $countries = Country::where('active', 1)->with(['governorates' => function ($query): void {
            $query->where('active', 1)->with(['cities' => function ($q): void {
                $q->where('active', 1);
            },
            ]);
        },
        ])->get();
        $rules = $shipping_zone->rules()->get();

        // Handle rule removal
        if ($request->has('remove_rule')) {
            $ruleIndex = $request->get('remove_rule');
            $rulesArray = $rules->toArray();
            if (isset($rulesArray[$ruleIndex])) {
                $ruleToDelete = $rulesArray[$ruleIndex];
                $shipping_zone->rules()->where('id', $ruleToDelete['id'])->delete();

                return redirect()->route('admin.shipping-zones.edit', $shipping_zone)
                    ->with('success', __('Rule removed successfully'));
            }
        }

        return view('admin.shipping_zones.edit', [
            'zone' => $shipping_zone,
            'countries' => $countries,
            'rules' => $rules,
        ]);
    }

    public function update(Request $request, ShippingZone $shipping_zone, HtmlSanitizer $sanitizer)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'code' => 'nullable|string|max:50',
            'active' => 'sometimes|boolean',
            'rules' => 'nullable|array',
            'rules.*.country_id' => 'required_with:rules|exists:countries,id',
            'rules.*.governorate_id' => 'nullable|exists:governorates,id',
            'rules.*.city_id' => 'nullable|exists:cities,id',
            'rules.*.price' => 'required_with:rules|numeric|min:0',
            'rules.*.estimated_days' => 'required_with:rules|integer|min:1',
            'rules.*.active' => 'sometimes|boolean',
        ]);
        if (isset($data['name']) && is_string($data['name'])) {
            $data['name'] = $sanitizer->clean($data['name']);
        }
        if (isset($data['code']) && is_string($data['code'])) {
            $data['code'] = $sanitizer->clean($data['code']);
        }

        $shipping_zone->update([
            'name' => $data['name'],
            'code' => $data['code'] ?? null,
            'active' => (bool) ($data['active'] ?? $shipping_zone->active),
        ]);
        $shipping_zone->rules()->delete();
        $this->persistRules($shipping_zone, $data['rules'] ?? []);

        return redirect()->route('admin.shipping-zones.index')->with('success', __('Zone updated'));
    }

    public function destroy(ShippingZone $shipping_zone)
    {
        $shipping_zone->delete();

        return redirect()->route('admin.shipping-zones.index')->with('success', __('Deleted'));
    }

    private function persistRules(ShippingZone $zone, array $rules): void
    {
        foreach ($rules as $r) {
            $clean = $this->cleanRule($r);
            if ($clean) {
                $zone->rules()->create($clean);
            }
        }
    }

    private function cleanRule(array $r): ?array
    {
        $country = $r['country_id'] ?? null;
        $gov = $r['governorate_id'] ?? null;
        $city = $r['city_id'] ?? null;
        $price = $r['price'] ?? null;
        $days = $r['estimated_days'] ?? null;
        if (! $country) {
            return null; // require at least country
        }
        if ($gov && ! Governorate::where('id', $gov)->where('country_id', $country)->exists()) {
            return null;
        }
        if (
            $city && ! City::where('id', $city)
                ->whereHas('governorate', function ($q) use ($gov, $country): void {
                    $q->where('id', $gov)
                        ->where('country_id', $country);
                })->exists()
        ) {
            return null;
        }
        if (! $gov) {
            $city = null; // ignore standalone city if governorate missing
        }

        return [
            'country_id' => $country,
            'governorate_id' => $gov,
            'city_id' => $city,
            'price' => is_numeric($price) ? $price : null,
            'estimated_days' => is_numeric($days) ? (int) $days : null,
            'active' => isset($r['active']) ? (bool) $r['active'] : true,
        ];
    }
}
