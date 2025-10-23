<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\Governorate;
use App\Services\HtmlSanitizer;
use Illuminate\Http\Request;

class CityController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:access-admin');
    }
    public function index(Request $request)
    {
        $govId = $request->query('governorate');
        $query = City::query();
        if ($govId) {
            $query->where('governorate_id', $govId);
        }
        $cities = $query->orderBy('name')->paginate(50);
        $countries = Country::orderBy('name')->get();
        $governorates = Governorate::orderBy('name')->get();

        return view('admin.locations.cities.index', compact('cities', 'countries', 'governorates', 'govId'));
    }

    public function create()
    {
        $countries = Country::orderBy('name')->get();
        $governorates = Governorate::orderBy('name')->get();

        return view('admin.locations.cities.create', compact('countries', 'governorates'));
    }

    public function store(Request $request, HtmlSanitizer $sanitizer)
    {
        $data = $request->validate([
            'governorate_id' => 'required|exists:governorates,id',
            'name' => 'required|string|max:255',
        ]);
        $data['active'] = $request->has('active') ? 1 : 0;
        // sanitize string inputs
        foreach ($data as $k => $v) {
            if (is_string($v)) {
                $data[$k] = $sanitizer->clean($v);
            }
        }
        City::create($data);

        return redirect()->route('admin.cities.index')->with('success', __('City created'));
    }

    public function edit(City $city)
    {
        $countries = Country::orderBy('name')->get();
        $governorates = Governorate::orderBy('name')->get();

        return view('admin.locations.cities.edit', compact('city', 'countries', 'governorates'));
    }

    public function update(Request $request, City $city, HtmlSanitizer $sanitizer)
    {
        $data = $request->validate([
            'governorate_id' => 'required|exists:governorates,id',
            'name' => 'required|string|max:255',
        ]);
        $data['active'] = $request->has('active') ? 1 : 0;
        // sanitize string inputs
        foreach ($data as $k => $v) {
            if (is_string($v)) {
                $data[$k] = $sanitizer->clean($v);
            }
        }
        $city->update($data);

        return redirect()->route('admin.cities.index')->with('success', __('City updated'));
    }

    public function destroy(City $city)
    {
        $city->delete();

        return redirect()->route('admin.cities.index')->with('success', __('City deleted'));
    }
}
