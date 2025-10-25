<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Governorate;
use Illuminate\Http\Request;

class GovernorateController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:access-admin');
    }

    public function index(Request $request)
    {
        $countryId = $request->query('country');
        $query = Governorate::query();
        if ($countryId) {
            $query->where('country_id', $countryId);
        }
        $governorates = $query->orderBy('name')->paginate(50);
        $countries = Country::orderBy('name')->get();

        return view('admin.locations.governorates.index', compact('governorates', 'countries', 'countryId'));
    }

    public function create()
    {
        $countries = Country::orderBy('name')->get();

        return view('admin.locations.governorates.create', compact('countries'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'name' => 'required|string|max:255',
        ]);
        $data['active'] = $request->has('active') ? 1 : 0;
        Governorate::create($data);

        return redirect()->route('admin.governorates.index')->with('success', __('Governorate created'));
    }

    public function edit(Governorate $governorate)
    {
        $countries = Country::orderBy('name')->get();

        return view('admin.locations.governorates.edit', compact('governorate', 'countries'));
    }

    public function update(Request $request, Governorate $governorate)
    {
        $data = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'name' => 'required|string|max:255',
        ]);
        $data['active'] = $request->has('active') ? 1 : 0;
        $governorate->update($data);

        return redirect()->route('admin.governorates.index')->with('success', __('Governorate updated'));
    }

    public function destroy(Governorate $governorate)
    {
        $governorate->delete();

        return redirect()->route('admin.governorates.index')->with('success', __('Governorate deleted'));
    }
}
