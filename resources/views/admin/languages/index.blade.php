@extends('layouts.admin')

@section('title', __('Languages Management'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="header-left">
                <div class="admin-header-content">
                    <div class="admin-header-icon">
                        <i class="fas fa-language"></i>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ __('Languages Management') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Manage system languages and translations') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.languages.create') }}" class="admin-btn admin-btn-primary">
                    <i class="fas fa-plus"></i>
                    {{ __('Add New Language') }}
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="admin-stats-grid">
            <div class="admin-stat-card">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-language"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-language"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup data-target="{{ (int)$languages->count() }}">{{ $languages->count() }}</div>
                    <div class="admin-stat-label">{{ __('Total Languages') }}</div>
                    <div class="admin-stat-description">{{ __('System Languages') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <i class="fas fa-language"></i>
                        <span>{{ __('System Languages') }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup data-target="{{ (int)$languages->where('active', true)->count() }}">{{ $languages->where('active', true)->count() }}</div>
                    <div class="admin-stat-label">{{ __('Active Languages') }}</div>
                    <div class="admin-stat-description">{{ number_format((($languages->where('active', true)->count() / max($languages->count(), 1)) * 100), 1) }}% {{ __('active') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-up">
                        <i class="fas fa-chart-line"></i>
                        <span>{{ number_format((($languages->where('active', true)->count() / max($languages->count(), 1)) * 100), 1) }}% {{ __('active') }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup data-target="{{ (int)$languages->where('is_default', true)->count() }}">{{ $languages->where('is_default', true)->count() }}</div>
                    <div class="admin-stat-label">{{ __('Default Language') }}</div>
                    <div class="admin-stat-description">{{ __('Primary language') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-neutral">
                        <i class="fas fa-star"></i>
                        <span>{{ __('Primary language') }}</span>
                    </div>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-header">
                    <div class="admin-stat-icon-wrapper">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="admin-stat-badge">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
                <div class="admin-stat-content">
                    <div class="admin-stat-value" data-countup data-target="{{ (int)($totalTranslations ?? 0) }}">{{ $totalTranslations ?? 0 }}</div>
                    <div class="admin-stat-label">{{ __('Total Translations') }}</div>
                    <div class="admin-stat-description">{{ __('Translation keys') }}</div>
                </div>
                <div class="admin-stat-footer">
                    <div class="admin-stat-trend admin-stat-trend-neutral">
                        <i class="fas fa-file-alt"></i>
                        <span>{{ __('Translation keys') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Languages Table -->
        <div class="admin-modern-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">
                    <i class="fas fa-language"></i>
                    {{ __('All Languages') }}
                </h2>
                <div class="admin-card-body">
                    @if($languages->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th width="40">
                                        <input type="checkbox" class="select-all">
                                    </th>
                                    <th>{{ __('Language') }}</th>
                                    <th>{{ __('Code') }}</th>
                                    <th>{{ __('Flag') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Default') }}</th>
                                    <th>{{ __('Direction') }}</th>
                                    <th>{{ __('Translations') }}</th>
                                    <th width="200">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($languages as $language)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="row-checkbox" value="{{ $language->id }}">
                                    </td>
                                    <td>
                                        <div class="user-info">
                                            <div class="user-avatar">
                                                @if($language->flag)
                                                {{ $language->flag }}
                                                @else
                                                <i class="fas fa-language"></i>
                                                @endif
                                            </div>
                                            <div class="user-name">{{ $language->name }}</div>
                                            <div class="user-email">{{ $language->native_name ?? $language->name }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="admin-badge">{{ strtoupper($language->code) }}</span>
                                    </td>
                                    <td>
                                        @if($language->flag)
                                        <div class="language-flag">
                                            {{ $language->flag }}
                                        </div>
                                        @else
                                        <span class="admin-text-muted">{{ __('No Flag') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($language->active)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle"></i>
                                            {{ __('Active') }}
                                        </span>
                                        @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times"></i>
                                            {{ __('Inactive') }}
                                        </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($language->is_default)
                                        <span class="badge bg-warning">
                                            <i class="fas fa-star"></i>
                                            {{ __('Default') }}
                                        </span>
                                        @else
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-action="set-default"
                                            data-language-id="{{ $language->id }}">
                                            {{ __('Set Default') }}
                                        </button>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            <i class="fas fa-folder"></i>
                                            {{ strtoupper($language->direction ?? 'ltr') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="admin-stock-value">{{ $language->translations_count ?? 0 }}</div>
                                        <a href="{{ route('admin.languages.translations', $language) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                            {{ __('Manage') }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.languages.edit', $language) }}" class="btn btn-sm btn-outline-secondary"
                                                title="{{ __('Edit') }}">
                                                <i class="fas fa-edit"></i>
                                                {{ __('Edit') }}
                                            </a>

                                            <a href="{{ route('admin.languages.translations', $language) }}"
                                                class="btn btn-sm btn-outline-secondary" title="{{ __('Translations') }}">
                                                <i class="fas fa-language"></i>
                                                {{ __('Translations') }}
                                            </a>

                                            @if(!$language->is_default)
                                            <form action="{{ route('admin.languages.destroy', $language) }}" method="POST"
                                                class="delete-form d-inline js-confirm" data-confirm="{{ __('Are you sure you want to delete this language?') }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}">
                                                    <i class="fas fa-trash"></i>
                                                    {{ __('Delete') }}
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Bulk Actions -->
                    <div class="bulk-actions">
                        <div class="bulk-actions-content">
                            <span class="selected-text">
                                {{ __('Selected') }}: <span class="selected-count">0</span> {{ __('items') }}
                            </span>
                            <div class="bulk-buttons">
                                <button type="button" class="btn btn-sm btn-success" data-action="bulk-activate">
                                    <i class="fas fa-check-circle"></i>
                                    {{ __('Activate') }}
                                </button>
                                <button type="button" class="btn btn-sm btn-warning" data-action="bulk-deactivate">
                                    <i class="fas fa-times"></i>
                                    {{ __('Deactivate') }}
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-action="bulk-delete">
                                    <i class="fas fa-trash"></i>
                                    {{ __('Delete') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    @if($languages->hasPages())
                    <div class="admin-card-footer-pagination">
                        <div class="pagination-info">
                            {{ $languages->links() }}
                        </div>
                    </div>
                    @endif
                    @else
                    <div class="admin-empty-state">
                        <i class="fas fa-language"></i>
                        <h3>{{ __('No Languages Found') }}</h3>
                        <p>{{ __('Start by adding your first language to the system.') }}</p>
                        <a href="{{ route('admin.languages.create') }}" class="admin-btn admin-btn-primary">
                            <i class="fas fa-plus"></i>
                            {{ __('Add Language') }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
</section>
@endsection