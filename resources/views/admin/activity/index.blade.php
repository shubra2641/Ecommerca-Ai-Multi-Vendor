@extends('layouts.admin')
@section('title', __('Activity Center'))
@section('content')
<div class="container py-4" id="activity-app" data-endpoint="{{ route('admin.activity') }}">
  <div class="row align-items-center mb-3">
    <div class="col-md-6 col-12 mb-2 mb-md-0">
      <h1 class="h5 m-0">{{ __('Activity Center') }}</h1>
    </div>
    <div class="col-md-6 col-12 text-md-end">
      <div class="d-inline-flex gap-2 align-items-center">
        <select class="form-select form-select-sm" v-model="filters.type" @change="reload()">
          <option value="">{{ __('All Types') }}</option>
          <option v-for="t in types" :key="t" :value="t">@{{ t }}</option>
        </select>
        <button class="btn btn-sm btn-outline-secondary" @click="reload()"><i class="fas fa-rotate"></i> {{ __('Refresh') }}</button>
      </div>
    </div>
  </div>

  <div class="card modern-card mb-3">
    <div class="card-body p-2">
      <div class="row g-2 small text-muted">
        <div class="col-auto">{{ __('Showing') }} @{{ items.length }} / @{{ total }}</div>
        <div class="col-auto">{{ __('Auto refresh') }}: <span :class="{'text-success':autoRefresh,'text-danger':!autoRefresh}">@{{ autoRefresh? 'ON':'OFF' }}</span></div>
        <div class="col-auto"><button class="btn btn-xs btn-link p-0" @click="toggleAuto()">@{{ autoRefresh? '{{ __('Stop') }}':'{{ __('Start') }}' }}</button></div>
      </div>
    </div>
  </div>

  <div class="activity-list activity-scroll-box card modern-card p-0" ref="scrollBox" @scroll="onScroll">
    <div v-if="loading && page===1" class="text-center py-5 text-muted"><div class="spinner-border spinner-border-sm"></div> {{ __('Loading') }}...</div>
    <template v-else>
      <div v-for="a in items" :key="a.id" class="border-bottom py-2 d-flex gap-2 align-items-start">
        <div class="flex-shrink-0">
          <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center icon-avatar-sm">
            <i class="fas fa-bolt"></i>
          </div>
        </div>
        <div class="flex-grow-1">
          <div class="d-flex justify-content-between">
            <strong class="me-2">@{{ a.type }}</strong>
            <small class="text-muted">@{{ a.time }}</small>
          </div>
          <div class="small">@{{ a.description }}</div>
          <div v-if="a.data_html" class="mt-1 small" v-html="a.data_html"></div>
          <div v-else-if="a.data && Object.keys(a.data).length" class="mt-1">
            <details class="small">
              <summary class="cursor-pointer text-muted">JSON</summary>
              <pre class="small bg-light p-2 rounded mb-0 pre-wrap">@{{ pretty(a.data) }}</pre>
            </details>
          </div>
          <div class="mt-1 text-muted small">
            <i class="fas fa-user"></i> @{{ a.user || '—' }} • <i class="fas fa-globe ms-1"></i> @{{ a.ip || '—' }}
          </div>
        </div>
      </div>
      <div v-if="loading && page>1" class="text-center py-2 text-muted"><div class="spinner-border spinner-border-sm"></div> {{ __('Loading more') }}...</div>
      <div v-if="!loading && items.length===0" class="py-5 text-center text-muted">{{ __('No activity found') }}</div>
    </template>
  </div>
  <div class="text-center mt-3">
    <button v-if="!allLoaded && !loading" class="btn btn-sm btn-outline-primary" @click="next()">{{ __('Load more') }}</button>
    <span v-else-if="allLoaded" class="text-muted small">{{ __('All loaded') }}</span>
  </div>
</div>

@push('scripts')
<script src="{{ asset('admin/js/activity-center.js') }}" defer></script>
@endpush
@endsection