@extends('front.layout')
@section('title', __('My Account').' - '.config('app.name'))
@section('content')

<section class="account-section">
    <div class="container account-grid">
        @include('front.account._sidebar')
        <main class="account-main">
            <div class="dashboard-page">
                <div class="dashboard-main">
                    <div class="top-intro">
                        <h1 class="page-title">{{ __('Overview') }}</h1>
                    </div>

                    <div class="dashboard-overview">
                        <div class="dash-card">
                            <div class="meta">
                                <div class="icon icon-blue">🏷</div>
                                <div class="dash-stats">
                                    <div class="big">{{ $stats['orders_total'] }}</div>
                                    <div class="small muted">{{ __('Orders') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="dash-card">
                            <div class="meta">
                                <div class="icon icon-green">✓</div>
                                <div class="dash-stats">
                                    <div class="big">{{ $stats['orders_completed'] }}</div>
                                    <div class="small muted">{{ __('Completed') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="dash-card">
                            <div class="meta">
                                <div class="icon icon-yellow">＋</div>
                                <div class="dash-stats">
                                    <div class="big">{{ $stats['orders_pending'] }}</div>
                                    <div class="small muted">{{ __('In Progress') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="dash-card">
                            <div class="meta">
                                <div class="icon icon-blue">💳</div>
                                <div class="dash-stats">
                                    <div class="big">{{ number_format($stats['payments_total']) }}</div>
                                    <div class="small muted">{{ __('Payments') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="dash-card">
                            <div class="meta">
                                <div class="icon icon-violet">🏦</div>
                                <div class="dash-stats">
                                    <div class="big">{{ auth()->user()->formatted_balance ?? auth()->user()->formatted_balance }}</div>
                                    <div class="small muted">{{ __('Balance') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="dash-card">
                            <div class="meta">
                                <div class="icon icon-red">💸</div>
                                <div class="dash-stats">
                                    <div class="big">{{ number_format($stats['payments_completed'],2) }}</div>
                                    <div class="small muted">{{ __('Spent') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="dash-card">
                            <div class="meta">
                                <div class="icon icon-violet">⚪</div>
                                <div class="dash-stats">
                                    <div class="big">{{ $stats['profile_completion'] }}%</div>
                                    <div class="small muted">{{ __('Profile') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-main-content">
                        <div class="panel">
                            <h4>{{ __('Recent Orders') }}</h4>
                            @if(!$recentOrders->count())<div class="muted small">{{ __('No orders yet.') }}</div>@else
                            <div class="items-table recent-list">
                                @foreach($recentOrders as $o)
                                <div class="row">
                                    <div class="label">#{{ $o->id }} · {{ $o->created_at->format('d M') }} ·
                                        {{ $o->items->count() }} {{ __('items') }}</div>
                                    <div class="value">{{ number_format($o->total,2) }} {{ $o->currency }} <a
                                            class="btn btn-primary btn-place"
                                            href="{{ route('user.orders.show',$o) }}">{{ __('View') }}</a></div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>

                        <div class="panel">
                            <h4>{{ __('Recent Payments') }}</h4>
                            @if(!$recentPayments->count())<div class="muted small">{{ __('No payments yet.') }}</div>
                            @else
                            <div class="items-table recent-list">
                                @foreach($recentPayments as $p)
                                <div class="row">
                                    <div class="label">#{{ $p->id }} · {{ $p->created_at->format('d M') }}</div>
                                    <div class="value">{{ number_format($p->amount,2) }} {{ $p->currency }}</div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>
</section>
@endsection