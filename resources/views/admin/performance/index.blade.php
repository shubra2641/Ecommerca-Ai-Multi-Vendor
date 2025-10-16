@extends('layouts.admin')
@section('title', __('Performance Dashboard'))
@section('content')
<div class="container mx-auto p-4">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold">{{ __('Performance Dashboard') }}</h1>
        <button id="refreshBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">{{ __('Refresh') }}</button>
    </div>
    <div id="perfGrid" class="row gx-3 gy-3">
        @foreach($snapshot as $metric => $row)
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card modern-card stats-card stats-card-primary h-100">
                    <div class="stats-card-body">
                        <div class="stats-card-content">
                            <div class="stats-number font-mono" data-metric="{{ $metric }}" data-field="sum">{{ $row['sum'] }}</div>
                            <div class="stats-label text-sm">{{ str_replace('_',' ', $metric) }}</div>
                            <div class="text-xs text-gray-500 mt-2">{{ __('Count') }}: <span class="font-mono" data-metric="{{ $metric }}" data-field="count">{{ $row['count'] }}</span></div>
                            <div class="text-xs text-gray-500">{{ __('Avg (ms)') }}: <span class="font-mono" data-metric="{{ $metric }}" data-field="avg_time_ms">{{ $row['avg_time_ms'] }}</span></div>
                        </div>
                        <div class="stats-icon"><i class="fas fa-tachometer-alt"></i></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <p class="text-[11px] text-gray-400 mt-6">{{ __('Window') }}: {{ config('performance.snapshot_window') }} {{ __('minutes (rolling)') }}</p>
    <div id="adminPerformanceConfig" data-perf-url="{{ route('admin.performance.snapshot') }}" hidden></div>
    </div>
@endsection
