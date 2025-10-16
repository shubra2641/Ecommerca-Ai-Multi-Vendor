@extends('layouts.admin')
@section('content')
<div class="admin-container">
    <h1>{{ __('Vendor Withdrawals') }}</h1>
    <form method="GET" class="mb-3 d-flex align-items-center gap-3">
        <label class="form-check">
            <input type="checkbox" name="held" value="1" class="form-check-input" {{ ($heldOnly ?? false) ? 'checked' : '' }}>
            <span class="form-check-label">{{ __('Held Only') }}</span>
        </label>
        <button class="btn btn-sm btn-primary" type="submit">{{ __('Filter') }}</button>
        @if(($heldOnly ?? false))
            <a href="{{ route('admin.vendor.withdrawals.index') }}" class="btn btn-sm btn-outline-secondary">{{ __('Clear') }}</a>
        @endif
    </form>
    <table class="table">
        <thead><tr>
            <th>{{ __('User') }}</th>
            <th>{{ __('Amount') }}</th>
            <th>{{ __('Commission (Exact)') }}</th>
            <th>{{ __('Status') }}</th>
            <th>{{ __('Held At') }}</th>
            <th>{{ __('Requested At') }}</th>
            <th></th>
        </tr></thead>
        <tbody>
        @foreach($withdrawals as $w)
            <tr>
                <td>{{ $w->user?->name }}</td>
                <td>{{ number_format($w->amount,2) }} {{ $w->currency }}</td>
                <td>{{ $w->commission_amount_exact ? number_format($w->commission_amount_exact,4) : '—' }}</td>
                <td>{{ ucfirst($w->status) }}</td>
                <td>{{ $w->held_at?->format('Y-m-d H:i') ?? '—' }}</td>
                <td>{{ $w->created_at }}</td>
                <td><a href="{{ route('admin.vendor.withdrawals.show', $w) }}">{{ __('View') }}</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $withdrawals->links() }}
</div>
@endsection
