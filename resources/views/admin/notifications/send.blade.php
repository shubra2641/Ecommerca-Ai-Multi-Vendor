@extends('layouts.admin')

@section('title', __('Send Notification'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">

        <!-- Header Section -->
        <div class="admin-order-header">
            <div class="header-left">
                <h1 class="admin-order-title">
                    <i class="fas fa-bell"></i>
                    {{ __('Send Notification') }}
                </h1>
                <p class="admin-order-subtitle">{{ __('Send a notification to users or vendors') }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.notifications.index') }}" class="admin-btn admin-btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Back') }}
                </a>
            </div>
        </div>

        <!-- Send Form -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <i class="fas fa-bell"></i>
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
                            <i class="fas fa-paper-plane"></i>
                            {{ __('Send Notification') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</section>
@endsection