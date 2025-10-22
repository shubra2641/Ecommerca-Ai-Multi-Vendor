<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressesController extends Controller
{
    public function index()
    {
        $addresses = Address::where('user_id', Auth::id())->with('country', 'governorate', 'city')->orderByDesc('is_default')->orderBy('id')->get();
        $countries = \App\Models\Country::where('active', 1)->orderBy('name')->get();

        $addrDefault = $addresses->firstWhere('is_default', true);
        $addrOthers = $addresses->where('is_default', false);
        $editingAddress = null; // Default to null for add mode

        // load governorates & cities lazily via AJAX; include user's selected ones for edit convenience
        return view('front.account.addresses', compact('addrDefault', 'addrOthers', 'countries', 'editingAddress'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:120',
            'name' => 'nullable|string|max:190',
            'phone' => 'nullable|string|max:50',
            'country_id' => 'nullable|integer',
            'governorate_id' => 'nullable|integer',
            'city_id' => 'nullable|integer',
            'line1' => 'required|string|max:255',
            'line2' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:30',
            'is_default' => 'nullable|boolean',
        ]);
        $data['user_id'] = Auth::id();
        $data['is_default'] = ! empty($data['is_default']);
        if ($data['is_default']) {
            Address::where('user_id', Auth::id())->update(['is_default' => false]);
        }
        Address::create($data);

        return back()->with('success', __('Address added'));
    }

    public function update(Request $request, Address $address)
    {
        abort_unless($address->user_id === Auth::id(), 403);
        // If this request only toggles default, don't run full validation (allow quick 'Make default').
        // Laravel forms include _token and _method, so ignore those when counting payload keys.
        $payload = $request->except(['_token', '_method']);
        if ($request->has('is_default') && count($payload) === 1) {
            $is = ! empty($payload['is_default']) ? 1 : 0;
            if ($is) {
                Address::where('user_id', Auth::id())->where('id', '!=', $address->id)->update(['is_default' => false]);
            }
            $address->is_default = $is;
            $address->save();

            return back()->with('success', __('Address updated'));
        }

        $data = $request->validate([
            'title' => 'nullable|string|max:120',
            'name' => 'nullable|string|max:190',
            'phone' => 'nullable|string|max:50',
            'country_id' => 'nullable|integer',
            'governorate_id' => 'nullable|integer',
            'city_id' => 'nullable|integer',
            'line1' => 'required|string|max:255',
            'line2' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:30',
            'is_default' => 'nullable|boolean',
        ]);
        $data['is_default'] = ! empty($data['is_default']);
        if ($data['is_default']) {
            Address::where('user_id', Auth::id())->where('id', '!=', $address->id)->update(['is_default' => false]);
        }
        $address->update($data);

        return back()->with('success', __('Address updated'));
    }

    public function edit(Address $address)
    {
        abort_unless($address->user_id === Auth::id(), 403);
        $address->load('country.governorates', 'governorate.cities');
        $countries = \App\Models\Country::where('active', 1)->orderBy('name')->get();
        $governorates = $address->country ? $address->country->governorates : collect();
        $cities = $address->governorate ? $address->governorate->cities : collect();

        return view('front.account.address_edit', compact('address', 'countries', 'governorates', 'cities'));
    }
}
