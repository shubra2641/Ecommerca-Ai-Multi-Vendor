@extends('layouts.admin')

@section('content')
<h1>{{ __('Payments') }}</h1>
<table class="table">
    <thead><tr><th>{{ __('ID') }}</th><th>{{ __('Order') }}</th><th>{{ __('User') }}</th><th>{{ __('Method') }}</th><th>{{ __('Amount') }}</th><th>{{ __('Status') }}</th><th>{{ __('Created') }}</th></tr></thead>
    <tbody>
    @foreach($payments as $p)
        <tr>
            <td>{{ $p->id }}</td>
            <td><a href="{{ route('admin.orders.show', $p->order_id) }}">#{{ $p->order_id }}</a></td>
            <td>{{ $p->user->email ?? __('Guest') }}</td>
            <td>{{ $p->method }}</td>
            <td>{{ $p->amount }}</td>
            <td>{{ $p->status }}</td>
            <td>{{ $p->created_at }}</td>
        </tr>
    @endforeach
    </tbody>
    </table>
    {{ $payments->links() }}
@endsection
