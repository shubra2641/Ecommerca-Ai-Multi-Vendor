@extends('front.layout')
@section('title', __('Invoices').' - '.config('app.name'))
@section('content')

<section class="account-section">
 <div class="container account-grid">
  @include('front.account._sidebar')
  <main class="account-main">
    <div class="invoices-wrapper">
      <h3>{{ __('Invoices / Payments') }}</h3>
      @if(!$payments->count())
        <div class="alert alert-info small">{{ __('No payments yet.') }}</div>
      @else
      <div class="invoices-table-wrapper">
        <table class="invoices-table">
          <thead><tr><th>#</th><th>{{ __('Order') }}</th><th>{{ __('Amount') }}</th><th>{{ __('Status') }}</th><th>{{ __('Method') }}</th></tr></thead>
          <tbody>
            @foreach($payments as $p)
            <tr>
              <td>{{ $p->id }}</td>
              <td><a href="{{ route('user.orders.show',$p->order_id) }}">#{{ $p->order_id }}</a></td>
              <td>{{ number_format($p->amount,2) }} {{ $p->currency }}</td>
              <td>{{ ucfirst($p->status) }}</td>
              <td>{{ $p->method }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      {{ $payments->links() }}
      @endif
    </div>
  </main>
 </div>
</section>
@endsection