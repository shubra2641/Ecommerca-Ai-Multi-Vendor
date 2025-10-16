@extends('front.layout')

@section('content')
<div class="container section">
    <div class="row">
        <div class="col-md-3">
            @include('front.account._sidebar')
        </div>
        <div class="col-md-9">
            <h1 class="mb-4">{{ __('returns.title') }}</h1>

            @if($items->isEmpty())
            <div class="empty-state p-4 text-center border rounded bg-light">
                <p class="mb-0">{{ __('returns.empty') }}</p>
            </div>
            @endif

            <div class="row gy-3">
                @foreach($items as $item)
                <div class="col-12">
                    <div class="card product-card shadow-sm">
                        <div class="card-body d-flex gap-3 align-items-start">
                            <div class="thumb thumb-fixed">
                                @if($item->product && $item->product->main_image)
                                <img src="{{ storage_image_url($item->product->main_image) }}" class="img-fluid rounded"
                                    alt="{{ $item->name }}">
                                @else
                                <div class="placeholder rounded bg-secondary text-white text-center"
                                    class="d-flex align-items-center justify-content-center h-100">
                                    {{ __('returns.no_image') }}</div>
                                @endif
                            </div>

                            <div class="flex-fill">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="mb-1">{{ $item->name }}</h5>
                                        <div class="meta text-muted">{{ __('returns.order') }} <a
                                                href="{{ route('orders.show', $item->order) }}">#{{ $item->order_id }}</a>
                                            · {{ $item->qty }} x {{ format_price($item->price ?? 0) }}</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="small text-muted">{{ $item->purchased_at?->toDateString() ?? '-' }}
                                        </div>
                                        <div class="small text-muted">{{ __('returns.return_until') }}
                                            {{ $item->refund_expires_at?->toDateString() ?? __('No return') }}</div>
                                    </div>
                                </div>

                                <div class="mt-3 d-flex gap-2">
                                    @if($item->isWithinReturnWindow() && ! $item->return_requested)
                                    <button class="btn btn-outline-danger" data-bs-toggle="collapse"
                                        data-bs-target="#return-form-{{ $item->id }}">{{ __('returns.request_button') }}</button>
                                    @else
                                    <span
                                        class="badge bg-secondary">{{ $item->return_requested ? __('returns.requested') : __('returns.return_expired') }}</span>
                                    @endif
                                    @if($item->return_status)
                                    <span class="badge bg-info text-dark">{{ $item->return_status }}</span>
                                    @endif
                                </div>

                                @if($item->isWithinReturnWindow() && ! $item->return_requested)
                                <div class="collapse mt-3" id="return-form-{{ $item->id }}">
                                    <form method="post" action="{{ route('user.returns.request', $item) }}"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-2">
                                            <label class="form-label">{{ __('returns.reason') }}</label>
                                            <textarea name="reason" class="form-control" rows="3" required></textarea>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">{{ __('returns.attach_image_optional') }}</label>
                                            <input type="file" name="image" accept="image/*" class="form-control">
                                        </div>
                                        <div>
                                            <button class="btn btn-danger">{{ __('returns.submit_request') }}</button>
                                        </div>
                                    </form>
                                </div>
                                @endif

                                @if(!empty($item->meta['user_images']) || !empty($item->meta['admin_images']))
                                <div class="mt-3 small">
                                    <div class="row gy-2">
                                            @foreach(array_merge($item->meta['user_images'] ?? [],
                                        $item->meta['admin_images'] ?? []) as $img)
                                        <div class="col-auto">
                            <a href="{{ storage_image_url($img) }}" target="_blank"><img
                                src="{{ storage_image_url($img) }}" class="img-fluid max-w-100 rounded shadow-sm"></a>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                @if(!empty($item->meta['history']))
                                <div class="mt-3">
                                    <strong>{{ __('History') }}</strong>
                                    <ul class="small mb-0">
                                        @foreach($item->meta['history'] as $h)
                                        <li>[{{ $h['when'] }}] <strong>{{ ucfirst($h['actor']) }}</strong> —
                                            {{ $h['action'] }}{{ $h['note'] ? ': '.$h['note'] : '' }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            @if($items instanceof \Illuminate\Contracts\Pagination\Paginator || $items instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
                <div class="mt-4">{{ $items->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection