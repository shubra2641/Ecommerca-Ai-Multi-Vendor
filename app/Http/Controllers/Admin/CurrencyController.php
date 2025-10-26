<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CurrencyController extends Controller
{
    public function index()
    {
        $currencies = Currency::orderBy('is_default', 'desc')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.currencies.index', compact('currencies'));
    }

    public function create()
    {
        return view('admin.currencies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|size:3|unique:currencies,code',
            'symbol' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0.00000001',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);

        DB::transaction(function () use ($request): void {
            // If setting as default, remove default from others
            if ($request->is_default) {
                Currency::where('is_default', true)->update(['is_default' => false]);
            }

            $data = $request->all();
            Currency::create($data);
        });

        return redirect()->route('admin.currencies.index')
            ->with('success', __('Currency created successfully'));
    }

    public function show(Currency $currency)
    {
        return view('admin.currencies.show', compact('currency'));
    }

    public function edit(Currency $currency)
    {
        return view('admin.currencies.edit', compact('currency'));
    }

    public function update(Request $request, Currency $currency)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|size:3|unique:currencies,code,' . $currency->id,
            'symbol' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0.00000001',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);

        DB::transaction(function () use ($request, $currency): void {
            // If setting as default, remove default from others
            if ($request->is_default && ! $currency->is_default) {
                Currency::where('is_default', true)->update(['is_default' => false]);
            }

            $data = $request->all();
            $currency->update($data);
        });

        return redirect()->route('admin.currencies.index')
            ->with('success', __('Currency updated successfully'));
    }

    public function destroy(Currency $currency)
    {
        if ($currency->is_default) {
            return redirect()->route('admin.currencies.index')
                ->with('error', __('Cannot delete default currency'));
        }

        $currency->delete();

        return redirect()->route('admin.currencies.index')
            ->with('success', __('Currency deleted successfully'));
    }

    public function makeDefault(Currency $currency)
    {
        DB::transaction(function () use ($currency): void {
            Currency::where('is_default', true)->update(['is_default' => false]);
            $currency->update(['is_default' => true]);
        });

        return redirect()->route('admin.currencies.index')
            ->with('success', __('Default currency updated successfully'));
    }

    public function bulkActivate(Request $request)
    {
        $ids = $request->input('ids', []);
        Currency::whereIn('id', $ids)->update(['active' => true]);

        return redirect()->back()->with('success', __('Currencies activated successfully'));
    }

    public function bulkDeactivate(Request $request)
    {
        $ids = $request->input('ids', []);
        Currency::whereIn('id', $ids)->where('is_default', false)->update(['active' => false]);

        return redirect()->back()->with('success', __('Currencies deactivated successfully'));
    }

    public function toggleStatus(Currency $currency)
    {
        $currency->update([
            'is_active' => ! $currency->is_active,
        ]);

        $status = $currency->is_active ? __('activated') : __('deactivated');

        return redirect()->back()->with('success', __('Currency :name has been :status', [
            'name' => $currency->name,
            'status' => $status,
        ]));
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        Currency::whereIn('id', $ids)->where('is_default', false)->delete();

        return redirect()->back()->with('success', __('Currencies deleted successfully'));
    }
}
