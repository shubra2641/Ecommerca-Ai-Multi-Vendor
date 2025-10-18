<div class="form-toolbar sticky-top bg-body pb-3 mb-3 border-bottom z-6">
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <div class="fw-semibold text-muted small">{{ __('Attribute Form') }}</div>
        <div class="ms-auto d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" data-collapse-all>
                <i class="fas fa-compress-alt d-sm-none"></i>
                <span class="d-none d-sm-inline">{{ __('Collapse All') }}</span>
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" data-expand-all>
                <i class="fas fa-expand-alt d-sm-none"></i>
                <span class="d-none d-sm-inline">{{ __('Expand All') }}</span>
            </button>
        </div>
    </div>
</div>

<!-- Basic Information -->
<div class="inner-section" data-section>
    <div class="inner-section-header d-flex justify-content-between align-items-center cursor-pointer" data-toggle-section>
        <h6 class="mb-0 small text-uppercase text-muted">
            <i class="fas fa-info-circle me-1 text-primary"></i>
            {{ __('Basic Information') }}
        </h6>
        <i class="fas fa-chevron-up section-caret text-muted small"></i>
    </div>
    <div class="inner-section-body pt-3">
        <div class="row g-3">
            <div class="col-lg-6 col-md-12">
                <label class="form-label required">{{ __('Attribute Name') }}</label>
                <input name="name" value="{{ old('name',$model->name ?? '') }}" class="form-control" placeholder="{{ __('Enter attribute name') }}" required>
                <div class="form-text">{{ __('The display name for this attribute (e.g., Color, Size)') }}</div>
            </div>
            <div class="col-lg-6 col-md-12">
                <label class="form-label">{{ __('Slug') }}</label>
                <input name="slug" value="{{ old('slug',$model->slug ?? '') }}" class="form-control" placeholder="{{ __('Auto-generated from name') }}" readonly>
                <div class="form-text small">{{ __('Slug will be generated from the attribute name.') }}</div>
                <div class="form-text">{{ __('URL-friendly version of the name (leave empty to auto-generate)') }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Attribute Configuration -->
<div class="inner-section" data-section>
    <div class="inner-section-header d-flex justify-content-between align-items-center cursor-pointer" data-toggle-section>
        <h6 class="mb-0 small text-uppercase text-muted">
            <i class="fas fa-cogs me-1 text-success"></i>
            {{ __('Configuration') }}
        </h6>
        <i class="fas fa-chevron-up section-caret text-muted small"></i>
    </div>
    <div class="inner-section-body pt-3">
        <div class="row g-3">
            <div class="col-lg-4 col-md-6">
                <label class="form-label">{{ __('Attribute Type') }}</label>
                <select name="type" class="form-select">
                    <option value="select" @selected(old('type', $model->type ?? 'select') === 'select')>
                        {{ __('Select (Dropdown)') }}
                    </option>
                    <option value="color" @selected(old('type', $model->type ?? '') === 'color')>
                        {{ __('Color Picker') }}
                    </option>
                    <option value="text" @selected(old('type', $model->type ?? '') === 'text')>
                        {{ __('Text Input') }}
                    </option>
                    <option value="number" @selected(old('type', $model->type ?? '') === 'number')>
                        {{ __('Number Input') }}
                    </option>
                </select>
                <div class="form-text">{{ __('How customers will select this attribute') }}</div>
            </div>
            <div class="col-lg-4 col-md-6">
                <label class="form-label">{{ __('Display Position') }}</label>
                <input name="position" type="number" value="{{ old('position',$model->position ?? 0) }}" class="form-control" min="0">
                <div class="form-text">{{ __('Order in which this attribute appears') }}</div>
            </div>
            <div class="col-lg-4 col-md-12">
                <label class="form-label">{{ __('Status') }}</label>
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" name="active" value="1" 
                           @checked(old('active', $model->active ?? true)) id="attribute-active">
                    <label class="form-check-label" for="attribute-active">
                        {{ __('Active') }}
                    </label>
                </div>
                <div class="form-text">{{ __('Whether this attribute is available for use') }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Advanced Settings -->
<div class="inner-section" data-section>
    <div class="inner-section-header d-flex justify-content-between align-items-center cursor-pointer" data-toggle-section>
        <h6 class="mb-0 small text-uppercase text-muted">
            <i class="fas fa-sliders-h me-1 text-warning"></i>
            {{ __('Advanced Settings') }}
        </h6>
        <i class="fas fa-chevron-up section-caret text-muted small"></i>
    </div>
    <div class="inner-section-body pt-3">
        <div class="row g-3">
            <div class="col-lg-6 col-md-12">
                <label class="form-label">{{ __('Description') }}</label>
                <textarea name="description" class="form-control" rows="3" placeholder="{{ __('Optional description for this attribute') }}">{{ old('description',$model->description ?? '') }}</textarea>
                <div class="form-text">{{ __('Internal description to help identify this attribute') }}</div>
            </div>
            <div class="col-lg-6 col-md-12">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="required" value="1" 
                                   @checked(old('required', $model->required ?? false)) id="attribute-required">
                            <label class="form-check-label" for="attribute-required">
                                {{ __('Required for products') }}
                            </label>
                            <div class="form-text">{{ __('Customers must select a value for this attribute') }}</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="filterable" value="1" 
                                   @checked(old('filterable', $model->filterable ?? true)) id="attribute-filterable">
                            <label class="form-check-label" for="attribute-filterable">
                                {{ __('Use in product filters') }}
                            </label>
                            <div class="form-text">{{ __('Allow customers to filter products by this attribute') }}</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="visible_on_product" value="1" 
                                   @checked(old('visible_on_product', $model->visible_on_product ?? true)) id="attribute-visible">
                            <label class="form-check-label" for="attribute-visible">
                                {{ __('Show on product page') }}
                            </label>
                            <div class="form-text">{{ __('Display this attribute on the product details page') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>