<!-- Toast Container -->
<div class="toast-stack" id="toast-container"></div>

<!-- Flash Messages Data -->
@if(session('success'))
<div id="flash-success" data-message="{{ session('success') }}" class="d-none"></div>
@endif

@if(session('info'))
<div id="flash-info" data-message="{{ session('info') }}" class="d-none"></div>
@endif

@if(session('warning'))
<div id="flash-warning" data-message="{{ session('warning') }}" class="d-none"></div>
@endif

@if(session('error'))
<div id="flash-error" data-message="{{ session('error') }}" class="d-none"></div>
@endif

@if($errors->any())
<div id="flash-errors" data-errors="{{ json_encode($errors->all()) }}" class="d-none"></div>
@endif