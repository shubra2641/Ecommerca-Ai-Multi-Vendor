@extends('vendor.layout')
@section('title', __('Performance'))
@section('content')
<div class="container mx-auto p-4">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-lg font-semibold">{{ __('Performance Snapshot') }}</h1>
    <button id="refreshBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">{{ __('Refresh') }}</button>
  </div>
  <div id="perfGrid" class="grid gap-4 md:grid-cols-2">
    @foreach($snapshot as $metric => $row)
      <div class="border rounded p-3 bg-white dark:bg-slate-800 shadow-sm">
        <h2 class="font-medium text-sm mb-2 uppercase tracking-wide">{{ str_replace('_',' ', $metric) }}</h2>
        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('Sum (window)') }}: <span class="font-mono" data-metric="{{ $metric }}" data-field="sum">{{ $row['sum'] }}</span></div>
        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('Count') }}: <span class="font-mono" data-metric="{{ $metric }}" data-field="count">{{ $row['count'] }}</span></div>
        <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('Avg Time (ms)') }}: <span class="font-mono" data-metric="{{ $metric }}" data-field="avg_time_ms">{{ $row['avg_time_ms'] }}</span></div>
      </div>
    @endforeach
  </div>
  <p class="text-[11px] text-gray-400 mt-6">{{ __('Window') }}: {{ config('performance.snapshot_window') }} {{ __('minutes (rolling)') }}</p>
  <div id="vendorPerformanceConfig" data-perf-url="{{ route('vendor.performance.snapshot') }}" hidden></div>
  <script src="{{ asset('admin/js/performance-refresh.js') }}" defer></script>
  </div>
@endsection
