@extends('layouts.admin')

@section('title', __('Balance History'))

@section('content')
<div class="container-fluid">
    @include('admin.partials.page-header', [
        'title' => __('Balance History') . ' - ' . $user->name,
        'subtitle' => __('View all balance transactions for this user'),
        'actions' => '<a href="'.route('admin.users.balance', $user).'" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> '.e(__('Back to Balance')).'</a> <a href="'.route('admin.users.show', $user).'" class="btn btn-outline-primary"><i class="fas fa-user me-1"></i> '.e(__('View User')).'</a>'
    ])

    <div class="row">
        <div class="col-12">
            <div class="card modern-card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-history text-info"></i>
                    <h5 class="card-title mb-0">{{ __('Transaction History') }}</h5>
                    <div class="ms-auto">
                        <span class="badge bg-primary">{{ $balanceHistories->count() }} {{ __('Transactions') }}</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($balanceHistories->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Type') }}</th>
                                        <th>{{ __('Amount') }}</th>
                                        <th>{{ __('Previous Balance') }}</th>
                                        <th>{{ __('New Balance') }}</th>
                                        <th>{{ __('Admin') }}</th>
                                        <th>{{ __('Note') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($balanceHistories as $transaction)
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold">{{ $transaction->created_at->format('M d, Y') }}</span>
                                                <small class="text-muted">{{ $transaction->created_at->format('H:i:s') }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $transaction->type === 'credit' ? 'success' : 'warning' }}">
                                                <i class="fas {{ $transaction->type === 'credit' ? 'fa-plus' : 'fa-minus' }} me-1"></i>
                                                {{ ucfirst($transaction->type) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-bold {{ $transaction->type === 'credit' ? 'text-success' : 'text-danger' }}">
                                                {{ $transaction->type === 'credit' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($transaction->previous_balance, 2) }}</td>
                                        <td>
                                            <span class="fw-bold">{{ number_format($transaction->new_balance, 2) }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted">
                                                {{ $transaction->admin ? $transaction->admin->name : __('System') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $transaction->note ?? __('No note') }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state text-center py-5">
                            <i class="fas fa-history fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">{{ __('No Transactions Found') }}</h5>
                            <p class="text-muted">{{ __('This user has no balance transactions yet.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
