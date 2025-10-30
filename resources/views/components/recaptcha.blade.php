@php
$recaptchaService = app(\App\Services\RecaptchaService::class);
@endphp

@if($recaptchaService->isEnabled())
<div class="admin-form-group">
    <div class="g-recaptcha" data-sitekey="{{ $recaptchaService->getSiteKey() }}"></div>
    @error('g-recaptcha-response')
    <div class="admin-text-danger">{{ $message }}</div>
    @enderror
</div>
@endif

