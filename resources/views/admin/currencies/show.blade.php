@extends('layouts.admin')

@section('title', __('Currency Details'))

@section('content')
@include('admin.partials.page-header', ['title'=>__('Currency Details'),'subtitle'=>__('View and manage currency information'),'actions'=>'<a href="'.route('admin.currencies.edit', $currency).'" class="btn btn-primary"><i class="fas fa-edit"></i> '.__('Edit Currency').'</a> <a href="'.route('admin.currencies.index').'" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> '.__('Back to List').'</a>'])

<div class="row">
    <div class="col-md-8">
    <div class="card modern-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle text-primary"></i>
                    {{ __('Currency Information') }}
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-group">
                            <label class="info-label">{{ __('Name') }}</label>
                            <div class="info-value">{{ $currency->name }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-group">
                            <label class="info-label">{{ __('Code') }}</label>
                            <div class="info-value">{{ $currency->code }}</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-group">
                            <label class="info-label">{{ __('Symbol') }}</label>
                            <div class="info-value">{{ $currency->symbol }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-group">
                            <label class="info-label">{{ __('Exchange Rate') }}</label>
                            <div class="info-value">{{ number_format($currency->exchange_rate, 4) }}</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-group">
                            <label class="info-label">{{ __('Status') }}</label>
                            <div class="info-value">
                                @if($currency->is_active)
                                <span class="badge bg-success">{{ __('Active') }}</span>
            @else
                <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-group">
                            <label class="info-label">{{ __('Default Currency') }}</label>
                            <div class="info-value">
                                @if($currency->is_default)
                                <span class="badge badge-primary">{{ __('Yes') }}</span>
                                @else
                                <span class="badge bg-secondary">{{ __('No') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-group">
                            <label class="info-label">{{ __('Created At') }}</label>
                            <div class="info-value">{{ $currency->created_at->format('Y-m-d H:i:s') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-group">
                            <label class="info-label">{{ __('Last Updated') }}</label>
                            <div class="info-value">{{ $currency->updated_at->format('Y-m-d H:i:s') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
    <div class="card modern-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bolt text-primary"></i>
                    {{ __('Quick Actions') }}
                </h3>
            </div>
            <div class="card-body">
                @if(!$currency->is_default)
                <form action="{{ route('admin.currencies.set-default', $currency) }}" method="POST" class="mb-3">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-star"></i>
                        {{ __('Set as Default') }}
                    </button>
                </form>
                @endif

                <a href="{{ route('admin.currencies.edit', $currency) }}" class="btn btn-warning btn-block mb-3">
                    <i class="fas fa-edit"></i>
                    {{ __('Edit Currency') }}
                </a>

                @if(!$currency->is_default)
                <form action="{{ route('admin.currencies.destroy', $currency) }}" method="POST" class="js-confirm" data-confirm="{{ __('Are you sure you want to delete this currency?') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-secondary btn-block">
                        <i class="fas fa-trash"></i>
                        {{ __('Delete Currency') }}
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection