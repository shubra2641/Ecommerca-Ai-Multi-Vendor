<div id="addressEditModal" class="modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('Edit Address') }}</h5>
        <button type="button" class="modal-close" data-action="close-modal">×</button>
      </div>
      <div class="modal-body">
        <form id="addressEditForm" method="post" action="">
          @csrf
          @method('PUT')
          <input type="hidden" name="id" id="modal_address_id">
          <div class="two-cols">
            <div class="field">
              <label>{{ __('Title') }}</label>
              <input type="text" name="title" id="modal_label" class="form-control">
            </div>
            <div class="field">
              <label>{{ __('Name') }}</label>
              <input type="text" name="name" id="modal_name" class="form-control">
            </div>
          </div>
          <div class="two-cols">
            <div class="field">
              <label>{{ __('Phone') }}</label>
              <input type="text" name="phone" id="modal_phone" class="form-control">
            </div>
          </div>

          <div class="two-cols">
            <div class="field">
              <label>{{ __('Country') }}</label>
              <select name="country_id" id="modal_country" class="form-control" data-loading-text="{{ __('Loading...') }}">
                <option value="">{{ __('Select country') }}</option>
                @foreach($countries as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="field">
              <label>{{ __('Governorate') }}</label>
              <select name="governorate_id" id="modal_governorate" class="form-control" disabled></select>
            </div>
          </div>

          <div class="two-cols">
            <div class="field">
              <label>{{ __('City') }}</label>
              <select name="city_id" id="modal_city" class="form-control" disabled></select>
            </div>
            <div class="field">
              <label>{{ __('Postal Code') }}</label>
              <input type="text" name="postal_code" id="modal_postal_code" class="form-control">
            </div>
          </div>

          <div class="two-cols">
            <div class="field">
              <label>{{ __('Address Line 1') }}</label>
              <input type="text" name="line1" id="modal_line1" class="form-control">
            </div>
            <div class="field">
              <label>{{ __('Address Line 2') }}</label>
              <input type="text" name="line2" id="modal_line2" class="form-control">
            </div>
          </div>

          <div class="mt-2 actions">
            <button class="btn btn-primary" type="submit">{{ __('Save') }}</button>
            <button type="button" class="btn btn-secondary" data-action="close-modal">{{ __('Cancel') }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
