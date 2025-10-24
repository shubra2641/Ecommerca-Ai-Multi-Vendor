@extends('layouts.admin')
@section('title', __('Price Change History'))
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h1 class="h4 mb-0">{{ __('Price History') }}: {{ $product->name }}</h1>
        <div class="btn-group">
            <button class="btn btn-outline-secondary btn-sm" id="btnTogglePercent">% {{ __('Change') }}</button>
            <button class="btn btn-outline-secondary btn-sm" id="btnToggleInterest">{{ __('Interest') }}</button>
        </div>
    </div>

    <form method="GET" class="row g-3 mb-2 align-items-end">
        <div class="col-6 col-md-2">
            <label class="form-label small text-muted">{{ __('From') }}</label>
            <input type="date" name="from" value="{{ $from }}" class="form-control form-control-sm">
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label small text-muted">{{ __('To') }}</label>
            <input type="date" name="to" value="{{ $to }}" class="form-control form-control-sm">
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label small text-muted">{{ __('MA Window') }}</label>
            <input type="number" min="2" max="60" name="ma" value="{{ $window }}" class="form-control form-control-sm">
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label small text-muted d-block">{{ __('Show Averages') }}</label>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="show_sma" value="1" id="showSma" {{ $showSma?'checked':'' }}>
                <label class="form-check-label" for="showSma">SMA</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="show_ema" value="1" id="showEma" {{ $showEma?'checked':'' }}>
                <label class="form-check-label" for="showEma">EMA</label>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label small text-muted">{{ __('Drop Threshold %') }}</label>
            <input type="number" min="1" max="90" name="thr" value="{{ $threshold }}" class="form-control form-control-sm">
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label small text-muted d-block">&nbsp;</label>
            <button class="btn btn-primary btn-sm w-100">{{ __('Apply') }}</button>
        </div>
    </form>
    <div class="row g-3 mb-2">
        <div class="col-6 col-md-2">
            <div class="border rounded p-2 small bg-white">
                <div class="text-muted">{{ __('Total Changes') }}</div>
                <div class="fs-5 fw-semibold">{{ $count }}</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="border rounded p-2 small bg-white">
                <div class="text-muted">{{ __('Net Change') }}</div>
                <div class="fs-5 fw-semibold {{ $netChange>0?'text-success':($netChange<0?'text-danger':'') }}">{{ number_format($netChange,2) }}</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="border rounded p-2 small bg-white">
                <div class="text-muted">{{ __('Net %') }}</div>
                <div class="fs-5 fw-semibold {{ $netPercent>0?'text-success':($netPercent<0?'text-danger':'') }}">{{ number_format($netPercent,2) }}%</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="border rounded p-2 small bg-white">
                <div class="text-muted">{{ __('Largest Drop') }}</div>
                <div class="fs-6 fw-semibold">@if($biggestDrop) {{ number_format($biggestDrop->percent,2) }}% ({{ number_format($biggestDrop->old_price,2) }} → {{ number_format($biggestDrop->new_price,2) }}) @else — @endif</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="border rounded p-2 small bg-white">
                <div class="text-muted">{{ __('Largest Increase') }}</div>
                <div class="fs-6 fw-semibold">@if($maxIncrease) {{ number_format($maxIncrease->percent,2) }}% ({{ number_format($maxIncrease->old_price,2) }} → {{ number_format($maxIncrease->new_price,2) }}) @else — @endif</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="border rounded p-2 small bg-white">
                <div class="text-muted">{{ __('Largest Abs Drop') }}</div>
                <div class="fs-6 fw-semibold">@if($largestAbsDrop) -{{ number_format($largestAbsDropDiff,2) }} ({{ number_format($largestAbsDrop->old_price,2) }} → {{ number_format($largestAbsDrop->new_price,2) }}) @else — @endif</div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body h-340">
            <canvas id="priceChart" aria-label="Price changes" role="img"></canvas>
        </div>
    </div>

    <div class="table-responsive bg-white border rounded">
        <table class="table table-sm mb-0" id="priceChangesTable">
            <thead><tr><th>{{ __('Date') }}</th><th>{{ __('Old') }}</th><th>{{ __('New') }}</th><th>{{ __('% Change') }}</th></tr></thead>
            <tbody>
            @forelse($changes as $c)
                <tr>
                    <td>{{ $c->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ number_format($c->old_price,2) }}</td>
                    <td>{{ number_format($c->new_price,2) }}</td>
                    <td class="{{ $c->percent<0?'text-danger':($c->percent>0?'text-success':'') }}">{{ number_format($c->percent,2) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted py-4">{{ __('No price changes recorded') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection