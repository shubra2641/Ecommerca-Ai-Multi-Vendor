@extends('layouts.admin')
@section('content')
<div class="admin-container">
    <h1>{{ __('Payouts') }}</h1>
    <table class="table">
        <thead><tr><th>{{ __('ID') }}</th><th>{{ __('User') }}</th><th>{{ __('Amount') }}</th><th>{{ __('Status') }}</th><th></th></tr></thead>
        <tbody>
        @foreach($payouts as $p)
            <tr>
                <td>{{ $p->id }}</td>
                <td>{{ $p->user?->name }}</td>
                <td>{{ $p->amount }} {{ $p->currency }}</td>
                <td>{{ ucfirst($p->status) }}</td>
                <td>
                    @if($p->status === 'pending')
                        <form method="post" action="{{ route('admin.vendor.withdrawals.payouts.execute', $p) }}">
                            @csrf
                            <button class="btn btn-sm btn-success">{{ __('Execute') }}</button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $payouts->links() }}
</div>
@endsection
