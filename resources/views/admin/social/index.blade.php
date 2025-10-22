@extends('layouts.admin')

@section('title', __('Social Links'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="header-left">
                <div class="admin-header-content">
                    <div class="admin-header-icon">
                        <i class="fas fa-share-alt"></i>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Social Links') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Manage social media links and their display order') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.social.create') }}" class="admin-btn admin-btn-primary">
                    <i class="fas fa-plus"></i>
                    {{ __('Add Link') }}
                </a>
            </div>
        </div>

        <!-- Social Links Table -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">
                    <i class="fas fa-share-alt"></i>
                    {{ __('Social Links') }}
                </h2>
                <div class="admin-badge-count">{{ $links->count() }} {{ __('Links') }}</div>
            </div>
            <div class="admin-card-body">
                @if(!$links->count())
                <div class="admin-empty-state">
                    <i class="fas fa-share-alt admin-notification-icon"></i>
                    <h3>{{ __('No Social Links') }}</h3>
                    <p>{{ __('No social links yet. Click Add Link to create one.') }}</p>
                    <a href="{{ route('admin.social.create') }}" class="admin-btn admin-btn-primary">
                        <i class="fas fa-plus"></i>
                        {{ __('Add First Link') }}
                    </a>
                </div>
                @else
                <form method="post" action="{{ route('admin.social.reorder') }}" id="reorder-form">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="w-40"></th>
                                    <th>{{ __('Platform') }}</th>
                                    <th>{{ __('Label') }}</th>
                                    <th>{{ __('URL') }}</th>
                                    <th>{{ __('Icon') }}</th>
                                    <th>{{ __('Active') }}</th>
                                    <th class="text-end w-160">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody id="sortable-body">
                                @foreach($links as $link)
                                <tr data-id="{{ $link->id }}">
                                    <td class="text-muted cursor-move">
                                        <i class="fas fa-arrows-alt-v"></i>
                                    </td>
                                    <td>
                                        <div class="user-info">
                                            <div class="user-avatar">
                                                @if($link->icon)
                                                <i class="{{ $link->icon }}"></i>
                                                @else
                                                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 0h10m-10 0a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V6a2 2 0 00-2-2M9 12l2 2 4-4" />
                                                </svg>
                                                @endif
                                            </div>
                                            <div class="user-name">{{ $link->platform }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $link->label }}</div>
                                    </td>
                                    <td>
                                        <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer" class="admin-product-link">{{ Str::limit($link->url, 30) }}</a>
                                    </td>
                                    <td>
                                        @if($link->icon)
                                        <i class="{{ $link->icon }}" title="{{ $link->platform }}"></i>
                                        @else
                                        <span class="admin-text-muted">{{ __('No Icon') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($link->is_active)
                                        <span class="badge bg-success">
                                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ __('Yes') }}
                                        </span>
                                        @else
                                        <span class="badge bg-secondary">
                                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            {{ __('No') }}
                                        </span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.social.edit', $link) }}" class="btn btn-sm btn-outline-secondary">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                {{ __('Edit') }}
                                            </a>
                                            <form action="{{ route('admin.social.destroy', $link) }}" method="post" class="d-inline-block js-confirm" data-confirm="{{ __('Are you sure you want to delete this social link?') }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                    {{ __('Delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-secondary" id="save-order" disabled>
                            <i class="fas fa-check"></i>
                            {{ __('Save Order') }}
                        </button>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection