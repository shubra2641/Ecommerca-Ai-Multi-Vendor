<div id="addressEditModal" class="modal-modern">
  <div class="modal-content-modern">
    <div class="modal-header-modern">
      <div class="modal-title-wrapper">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="modal-icon">
          <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
        </svg>
        <h3 class="modal-title">{{ __('Edit Address') }}</h3>
      </div>
      <button type="button" class="modal-close-modern" data-action="close-modal">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
    <div class="modal-body">
      <form id="addressEditForm" method="post" action="">
        @csrf
        @method('PUT')
        <input type="hidden" name="id" id="modal_address_id">

        <div class="form-row">
          <div class="form-group">
            <label class="field-label">{{ __('Title') }}</label>
            <input type="text" name="title" id="modal_label" class="form-input">
          </div>
          <div class="form-group">
            <label class="field-label">{{ __('Name') }}</label>
            <input type="text" name="name" id="modal_name" class="form-input">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="field-label">{{ __('Phone') }}</label>
            <input type="text" name="phone" id="modal_phone" class="form-input">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="field-label">{{ __('Country') }}</label>
            <select name="country_id" id="modal_country" class="form-input" data-loading-text="{{ __('Loading...') }}">
              <option value="">{{ __('Select country') }}</option>
              @foreach($countries as $c)
              <option value="{{ $c->id }}">{{ $c->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label class="field-label">{{ __('Governorate') }}</label>
            <select name="governorate_id" id="modal_governorate" class="form-input" disabled></select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="field-label">{{ __('City') }}</label>
            <select name="city_id" id="modal_city" class="form-input" disabled></select>
          </div>
          <div class="form-group">
            <label class="field-label">{{ __('Postal Code') }}</label>
            <input type="text" name="postal_code" id="modal_postal_code" class="form-input">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="field-label">{{ __('Address Line 1') }}</label>
            <input type="text" name="line1" id="modal_line1" class="form-input">
          </div>
          <div class="form-group">
            <label class="field-label">{{ __('Address Line 2') }}</label>
            <input type="text" name="line2" id="modal_line2" class="form-input">
          </div>
        </div>

        <div class="modal-actions">
          <button class="btn-action-modern btn-primary" type="submit">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ __('Save') }}
          </button>
          <button type="button" class="btn-action-modern btn-secondary" data-action="close-modal">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M6 18L18 6M6 6l12 12" />
            </svg>
            {{ __('Cancel') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>