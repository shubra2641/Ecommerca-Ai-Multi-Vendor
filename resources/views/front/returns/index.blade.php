@extends('front.layout')

@section('content')
<section class="account-section">
    <div class="container account-grid">
        @include('front.account._sidebar')
        <main class="account-main">
            <div class="dashboard-page">

                <!-- Header -->
                <div class="order-title-card">
                    <div class="title-row">
                        <div class="title-content">
                            <h1 class="modern-order-title">
                                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="title-icon">
                                    <path d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                                    <path d="M8 5a2 2 0 012-2h4a2 2 0 012 2v4H8V5z" />
                                    <path d="M9 12l2 2 4-4" />
                                </svg>
                                {{ __('title') }}
                            </h1>
                            <p class="order-date-modern">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('Manage your return requests and track their status') }}
                            </p>
                        </div>
                    </div>
                </div>

                @if($items->isEmpty())
                <div class="modern-card">
                    <div class="empty-state">
                        <svg width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" class="empty-icon">
                            <path d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                            <path d="M8 5a2 2 0 012-2h4a2 2 0 012 2v4H8V5z" />
                            <path d="M9 12l2 2 4-4" />
                        </svg>
                        <p class="empty-text">{{ __('empty') }}</p>
                    </div>
                </div>
                @endif

                @if(!$items->isEmpty())
                <div class="modern-card">
                    <div class="card-header-modern">
                        <h3 class="card-title-modern">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            {{ __('Return Items') }}
                        </h3>
                    </div>
                    <div class="items-list-modern">
                        @foreach($items as $item)
                        <div class="item-card-modern">
                            <div class="item-img-wrapper">
                                @if($item['image'])
                                <img src="{{ storage_image_url($item['image']) }}" class="item-img" alt="{{ $item['name'] }}">
                                @else
                                <div class="item-img-placeholder gradient-blue">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                @endif
                            </div>

                            <div class="item-info-modern">
                                <h4 class="item-name-modern">{{ $item['name'] }}</h4>
                                <p class="item-variant-modern">
                                    {{ __('order') }}
                                    <a href="{{ route('orders.show', $item['order']) }}" class="order-link">#{{ $item['order_id'] }}</a>
                                    · {{ $item['qty'] }} x {{ format_price($item['price'] ?? 0) }}
                                </p>
                                <div class="item-meta-modern">
                                    <span class="meta-item">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ $item['purchased_at'] ?? '-' }}
                                    </span>
                                    <span class="meta-item">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ __('return_until') }} {{ $item['return_expires'] ?? __('No return') }}
                                    </span>
                                </div>
                            </div>

                            <div class="item-total-modern">
                                @if($item['within_window'] && ! $item['return_requested'])
                                <button class="btn-action-modern btn-secondary" data-bs-toggle="collapse" data-bs-target="#return-form-{{ $item['id'] }}">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    {{ __('request_button') }}
                                </button>
                                @else
                                <span class="status-badge-modern status-{{ $item['return_requested'] ? 'requested' : 'expired' }}">
                                    {{ $item['return_requested'] ? __('requested') : __('return_expired') }}
                                </span>
                                @endif
                                @if($item['return_status'])
                                <span class="payment-badge-modern status-{{ $item['return_status'] }}">
                                    {{ $item['return_status'] }}
                                </span>
                                @endif
                            </div>
                        </div>

                        @if($item['within_window'] && ! $item['return_requested'])
                        <div class="collapse mt-3" id="return-form-{{ $item['id'] }}">
                            <div class="modern-card">
                                <div class="card-header-modern">
                                    <h4 class="card-title-modern">
                                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ __('Submit Return Request') }}
                                    </h4>
                                </div>
                                <form method="post" action="{{ route('request', $item['id']) }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="field-label">{{ __('reason') }}</label>
                                            <textarea name="reason" class="form-input" rows="3" required placeholder="{{ __('Please explain why you want to return this item') }}"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="field-label">{{ __('attach_image_optional') }}</label>
                                            <input type="file" name="image" accept="image/*" class="form-input">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <button type="submit" class="btn-action-modern btn-primary btn-full">
                                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ __('submit_request') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endif

                        @if(!empty($item['images']))
                        <div class="modern-card">
                            <div class="card-header-modern">
                                <h4 class="card-title-modern">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ __('Return Images') }}
                                </h4>
                            </div>
                            <div class="items-list-modern">
                                @foreach($item['images'] as $img)
                                <div class="item-card-modern">
                                    <div class="item-img-wrapper">
                                        <a href="{{ storage_image_url($img) }}" target="_blank">
                                            <img src="{{ storage_image_url($img) }}" class="item-img" alt="Return Image">
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if(!empty($item['history']))
                        <div class="modern-card">
                            <div class="card-header-modern">
                                <h4 class="card-title-modern">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ __('Return History') }}
                                </h4>
                            </div>
                            <div class="items-list-modern">
                                @foreach($item['history'] as $h)
                                <div class="item-card-modern">
                                    <div class="item-info-modern">
                                        <h4 class="item-name-modern">{{ ucfirst($h['actor']) }}</h4>
                                        <p class="item-variant-modern">{{ $h['action'] }}{{ $h['note'] ? ': '.$h['note'] : '' }}</p>
                                        <div class="item-meta-modern">
                                            <span class="meta-item">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ $h['when'] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
                @endif

                @if($items instanceof \Illuminate\Contracts\Pagination\Paginator || $items instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
                <div class="pagination-wrapper">
                    {{ $items->links() }}
                </div>
                @endif
            </div>
        </main>
    </div>
</section>
@endsection