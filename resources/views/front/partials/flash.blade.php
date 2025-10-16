{{-- Notifications are displayed via client-side toasts. Provide a noscript fallback. --}}
@if(!app()->runningInConsole())
  <noscript>
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
  </noscript>
@endif