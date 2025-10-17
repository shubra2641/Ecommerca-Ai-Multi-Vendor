<div class="contact-form-wrapper">
 @if(config('contact.form_enabled', true))
  <form method="POST" action="{{ route('contact.submit') }}" class="modern-contact-form" novalidate>
    @csrf
    <div class="form-grid">
      <div class="field">
        <label>{{ __('Name') }} *</label>
        <input type="text" name="name" required maxlength="150" value="{{ old('name') }}">
        @error('name')<span class="error">{{ $message }}</span>@enderror
      </div>
      <div class="field">
        <label>{{ __('Email') }} *</label>
        <input type="email" name="email" required maxlength="190" value="{{ old('email') }}">
        @error('email')<span class="error">{{ $message }}</span>@enderror
      </div>
      <div class="field full">
        <label>{{ __('Subject') }}</label>
        <input type="text" name="subject" maxlength="190" value="{{ old('subject') }}">
        @error('subject')<span class="error">{{ $message }}</span>@enderror
      </div>
      <div class="field full">
        <label>{{ __('Message') }} *</label>
        <textarea name="message" rows="6" required maxlength="5000">{{ old('message') }}</textarea>
        @error('message')<span class="error">{{ $message }}</span>@enderror
      </div>
  <div class="honeypot">
        <input type="text" name="website" value="">
      </div>
      <div class="field full actions">
        <button class="submit-btn"><i class="fas fa-paper-plane"></i> {{ __('Send') }}</button>
      </div>
    </div>
  </form>
 @else
  <div class="text-muted small">{{ __('Contact form is disabled') }}</div>
 @endif
</div>
