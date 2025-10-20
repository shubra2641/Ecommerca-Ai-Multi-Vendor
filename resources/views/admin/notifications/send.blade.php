@extends('layouts.admin')

@section('title', __('Send Notification'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">

        <!-- Header Section -->
        <div class="admin-order-header">
            <div class="header-left">
                <h1 class="admin-order-title">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    {{ __('Send Notification') }}
                </h1>
                <p class="admin-order-subtitle">{{ __('Send a notification to users or vendors') }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.notifications.index') }}" class="admin-btn admin-btn-secondary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('Back') }}
                </a>
            </div>
        </div>

        <!-- Send Form -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <h3 class="admin-card-title">{{ __('Notification Details') }}</h3>
            </div>
            <div class="admin-card-body">
                <form method="POST" action="{{ route('admin.notifications.send.store') }}" class="admin-form">
                    @csrf
                    
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Target role') }}</label>
                        <select name="role" class="admin-form-select" required>
                            <option value="vendor">{{ __('Vendors') }}</option>
                            <option value="user">{{ __('Users') }}</option>
                        </select>
                    </div>

                    @php($langs = $languages)
                    <div class="admin-form-group admin-form-group-wide">
                        <label class="admin-form-label">{{ __('Notification Content') }}</label>
                        <ul class="nav nav-tabs" role="tablist">
                            @foreach($langs as $i => $lang)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $i==0? 'active':'' }}" id="tab-{{ $lang->code }}" data-bs-toggle="tab" data-bs-target="#panel-{{ $lang->code }}" type="button">
                                        {{ $lang->code }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                        <div class="tab-content">
                            @foreach($langs as $i => $lang)
                                <div class="tab-pane fade {{ $i==0? 'show active':'' }}" id="panel-{{ $lang->code }}" role="tabpanel">
                                    <div class="admin-form-group">
                                        <label class="admin-form-label">{{ __('Title') }} ({{ $lang->code }})</label>
                                        <input type="text" name="title[{{ $lang->code }}]" class="admin-form-input" />
                                    </div>
                                    <div class="admin-form-group">
                                        <label class="admin-form-label">{{ __('Message') }} ({{ $lang->code }})</label>
                                        <textarea name="message[{{ $lang->code }}]" class="admin-form-input" rows="4"></textarea>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Optional URL') }}</label>
                        <input type="url" name="url" class="admin-form-input" placeholder="https://..." />
                    </div>

                    <div class="admin-form-actions">
                        <button type="submit" class="admin-btn admin-btn-success admin-btn-large">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            {{ __('Send Notification') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</section>
@endsection
