<!-- Toast Container -->
<div class="toast-stack" id="toast-container"></div>

<!-- Flash Messages Data -->
@if(session('success'))
    <div id="flash-success" data-message="{{ session('success') }}" style="display: none;"></div>
@endif

@if(session('info'))
    <div id="flash-info" data-message="{{ session('info') }}" style="display: none;"></div>
@endif

@if(session('warning'))
    <div id="flash-warning" data-message="{{ session('warning') }}" style="display: none;"></div>
@endif

@if(session('error'))
    <div id="flash-error" data-message="{{ session('error') }}" style="display: none;"></div>
@endif

@if($errors->any())
    <div id="flash-errors" data-errors="{{ json_encode($errors->all()) }}" style="display: none;"></div>
@endif