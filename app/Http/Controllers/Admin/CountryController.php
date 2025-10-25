<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:access-admin');
    }

    public function index()
    {
        $countries = Country::orderBy('name')->paginate(50);

        return view('admin.locations.countries.index', compact('countries'));
    }

    public function create()
    {
        return view('admin.locations.countries.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'iso_code' => 'nullable|string|max:5',
        ]);

        $data['active'] = $request->has('active') ? 1 : 0;
        Country::create($data);

        return redirect()->route('admin.countries.index')->with('success', __('Country created'));
    }

    public function edit(Country $country)
    {
        return view('admin.locations.countries.edit', compact('country'));
    }

    public function update(Request $request, Country $country)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'iso_code' => 'nullable|string|max:5',
        ]);

        $data['active'] = $request->has('active') ? 1 : 0;
        $country->update($data);

        return redirect()->route('admin.countries.index')->with('success', __('Country updated'));
    }

    public function destroy(Country $country)
    {
        $country->delete();

        return redirect()->route('admin.countries.index')->with('success', __('Country deleted'));
    }
}
