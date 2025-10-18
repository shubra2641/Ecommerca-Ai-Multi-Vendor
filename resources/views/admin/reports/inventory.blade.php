@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="page-header mb-3">
        <div class="page-header-content">
            <h1 class="page-title">{{ __('Inventory Report') }}</h1>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card stats-card stats-card-primary h-100">
                <div class="stats-card-body">
                    <div class="stats-card-content">
                        <div class="stats-number" data-countup data-target="{{ (int)($totals['total_products'] ?? 0) }}">{{ $totals['total_products'] }}</div>
                        <div class="stats-label">{{ __('Total Products') }}</div>
                        <div class="stats-trend">
                            <i class="fas fa-boxes text-primary"></i>
                            <span class="text-primary">{{ __('All Products') }}</span>
                        </div>
                    </div>
                    <div class="stats-icon"><i class="fas fa-boxes"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card stats-card stats-card-success h-100">
                <div class="stats-card-body">
                    <div class="stats-card-content">
                        <div class="stats-number" data-countup data-target="{{ (int)($totals['manage_stock_count'] ?? 0) }}">{{ $totals['manage_stock_count'] }}</div>
                        <div class="stats-label">{{ __('Manage Stock') }}</div>
                        <div class="stats-trend">
                            <i class="fas fa-arrow-up text-success"></i>
                            <span class="text-success">{{ number_format((($totals['manage_stock_count'] / max($totals['total_products'], 1)) * 100), 1) }}% {{ __('tracked') }}</span>
                        </div>
                    </div>
                    <div class="stats-icon"><i class="fas fa-warehouse"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card stats-card stats-card-danger h-100">
                <div class="stats-card-body">
                    <div class="stats-card-content">
                        <div class="stats-number" data-countup data-target="{{ (int)($totals['out_of_stock'] ?? 0) }}">{{ $totals['out_of_stock'] }}</div>
                        <div class="stats-label">{{ __('Out of Stock') }}</div>
                        <div class="stats-trend">
                            <i class="fas fa-exclamation-triangle text-danger"></i>
                            <span class="text-danger">{{ __('Need restocking') }}</span>
                        </div>
                    </div>
                    <div class="stats-icon"><i class="fas fa-exclamation-triangle"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card modern-card stats-card stats-card-warning h-100">
                <div class="stats-card-body">
                    <div class="stats-card-content">
                        <div class="stats-number" data-countup data-target="{{ (int)($totals['serials_low'] ?? 0) }}">{{ $totals['serials_low'] }}</div>
                        <div class="stats-label">{{ __('Serials low (<=5)') }}</div>
                        <div class="stats-trend">
                            <i class="fas fa-clock text-warning"></i>
                            <span class="text-warning">{{ __('Low inventory') }}</span>
                        </div>
                    </div>
                    <div class="stats-icon"><i class="fas fa-clock"></i></div>
                </div>
            </div>
        </div>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('SKU') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Manage Stock') }}</th>
                <th>{{ __('Available Stock') }}</th>
                <th>{{ __('Has Serials') }}</th>
                <th>{{ __('Unsold Serials') }}</th>
                <th>{{ __('Variations') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $p)
            <tr>
                <td>{{ $p['id'] }}</td>
                <td>{{ $p['sku'] }}</td>
                <td>{{ $p['name'] }}</td>
                <td>{{ $p['manage_stock'] ? __('Yes') : __('No') }}</td>
                <td>{{ $p['available_stock'] === null ? __('Unlimited') : $p['available_stock'] }}</td>
                <td>{{ $p['has_serials'] ? __('Yes') : __('No') }}</td>
                <td>{{ $p['unsold_serials'] }}</td>
                <td>
                    @if(!empty($p['variations']) && $p['variations']->count() > 0)
                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#vars-{{ $p['id'] }}">{{ $p['variations']->count() }} {{ __('Variations') }}</button>
                        <div class="collapse mt-2" id="vars-{{ $p['id'] }}">
                            <table class="table table-sm table-bordered mb-0">
                                <thead><tr><th>{{ __('SKU') }}</th><th>{{ __('Name') }}</th><th>{{ __('Manage Stock') }}</th><th>{{ __('Available') }}</th></tr></thead>
                                <tbody>
                                    @foreach($p['variations'] as $v)
                                    <tr>
                                        <td>{{ $v['sku'] }}</td>
                                        <td>{{ e($v['name']) }}</td>
                                        <td>{{ $v['manage_stock'] ? __('Yes') : __('No') }}</td>
                                        <td>{{ $v['available_stock'] === null ? __('Unlimited') : $v['available_stock'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection