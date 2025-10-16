@extends('layouts.admin')

@section('title', __('Languages Management'))

@section('content')
@include('admin.partials.page-header', ['title'=>__('Languages Management'),'subtitle'=>__('Manage system languages and translations'),'actions'=>'<a href="'.route('admin.languages.create').'" class="btn btn-primary"><i class="fas fa-plus"></i> '.__('Add New Language').'</a> <button type="button" class="btn btn-secondary" data-action="refresh-translations"><i class="fas fa-sync-alt"></i> '.__('Refresh Cache').'</button>'])

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="card modern-card stats-card">
        <div class="stats-card-body">
            <div class="stats-icon">
                <i class="fas fa-language"></i>
            </div>
            <div class="stats-card-content">
                <div class="stats-number">{{ $languages->count() }}</div>
                <div class="stats-label">{{ __('Total Languages') }}</div>
            </div>
        </div>
    </div>
    <div class="card modern-card stats-card">
        <div class="stats-card-body">
            <div class="stats-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stats-card-content">
                <div class="stats-number">{{ $languages->where('active', true)->count() }}</div>
                <div class="stats-label">{{ __('Active Languages') }}</div>
            </div>
        </div>
    </div>
    <div class="card modern-card stats-card">
        <div class="stats-card-body">
            <div class="stats-icon">
                <i class="fas fa-star"></i>
            </div>
            <div class="stats-card-content">
                <div class="stats-number">{{ $languages->where('is_default', true)->count() }}</div>
                <div class="stats-label">{{ __('Default Language') }}</div>
            </div>
        </div>
    </div>
    <div class="card modern-card stats-card">
        <div class="stats-card-body">
            <div class="stats-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stats-card-content">
                <div class="stats-number">{{ $totalTranslations ?? 0 }}</div>
                <div class="stats-label">{{ __('Total Translations') }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card modern-card">
    <div class="card-header">
        <h3 class="card-title">{{ __('All Languages') }}</h3>
        <div class="card-actions">
            <div class="search-box">
                <input type="text" class="form-control table-search" placeholder="{{ __('Search languages...') }}">
                <i class="fas fa-search"></i>
            </div>
        </div>
    </div>

    <div class="card-body">
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
                            <div class="language-info">
                                <div class="language-name">{{ $language->name }}</div>
                                <div class="language-native">{{ $language->native_name ?? $language->name }}</div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ strtoupper($language->code) }}</span>
                        </td>
                        <td>
                            @if($language->flag)
                            <div class="language-flag">
                                {{ $language->flag }}
                            </div>
                            @else
                            <span class="text-muted">{{ __('No Flag') }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="status-badge">
                                @if($language->active)
                                <span class="badge bg-success">
                                    <i class="fas fa-check"></i>
                                    {{ __('Active') }}
                                </span>
                                @else
                                <span class="badge bg-danger">
                                    <i class="fas fa-times"></i>
                                    {{ __('Inactive') }}
                                </span>
                                @endif
                            </div>
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
                                <i
                                    class="fas fa-{{ $language->direction === 'rtl' ? 'align-right' : 'align-left' }}"></i>
                                {{ strtoupper($language->direction ?? 'ltr') }}
                            </span>
                        </td>
                        <td>
                            <div class="translation-status">
                                <span class="translation-count">{{ $language->translations_count ?? 0 }}</span>
                                <a href="{{ route('admin.languages.translations', $language) }}"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                    {{ __('Manage') }}
                                </a>
                            </div>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.languages.edit', $language) }}" class="btn btn-outline-secondary"
                                        title="{{ __('Edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <a href="{{ route('admin.languages.translations', $language) }}"
                                        class="btn btn-outline-secondary" title="{{ __('Translations') }}">
                                        <i class="fas fa-language"></i>
                                    </a>

                                    @if(!$language->is_default)
                                    <form action="{{ route('admin.languages.destroy', $language) }}" method="POST"
                                        class="delete-form d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-secondary" title="{{ __('Delete') }}"
                                            data-confirm="{{ __('Are you sure you want to delete this language?') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
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
                        <i class="fas fa-check"></i>
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
        <div class="pagination-wrapper">
            {{ $languages->links() }}
        </div>
        @endif
        @else
        <div class="empty-state">
            <i class="fas fa-language fa-3x"></i>
            <h3>{{ __('No Languages Found') }}</h3>
            <p>{{ __('Start by adding your first language to the system.') }}</p>
            <a href="{{ route('admin.languages.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                {{ __('Add Language') }}
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('admin/css/languages.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('admin/js/languages.js') }}"></script>
@endpush