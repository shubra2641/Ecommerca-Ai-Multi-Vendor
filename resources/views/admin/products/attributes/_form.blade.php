<div class="admin-form-toolbar sticky-top bg-body pb-3 mb-3 border-bottom z-6">
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <div class="admin-text-muted small fw-semibold">{{ __('Attribute Form') }}</div>
        <div class="ms-auto d-flex gap-2">
            <button type="button" class="admin-btn admin-btn-small admin-btn-outline" data-collapse-all>
                <i class="fas fa-compress d-sm-none"></i>
                <span class="d-none d-sm-inline">{{ __('Collapse All') }}</span>
            </button>
            <button type="button" class="admin-btn admin-btn-small admin-btn-outline" data-expand-all>
                <i class="fas fa-compress d-sm-none"></i>
                <span class="d-none d-sm-inline">{{ __('Expand All') }}</span>
            </button>
        </div>
    </div>
</div>

<!-- Basic Information -->
<div class="admin-modern-card mb-4" data-section>
    <div class="admin-card-header d-flex justify-content-between align-items-center cursor-pointer" data-toggle-section>
        <h3 class="admin-card-title">
            <i class="fas fa-clock"></i>
            {{ __('Basic Information') }}
        </h3>
        <i class="fas fa-chevron-down section-caret admin-text-muted"></i>
    </div>
    <div class="admin-card-body">
        <div class="admin-form-grid">
            <div class="admin-form-group">
                <label class="admin-form-label required">{{ __('Attribute Name') }}</label>
                <input name="name" value="{{ old('name',$model->name ?? '') }}" class="admin-form-input" placeholder="{{ __('Enter attribute name') }}" required>
                <div class="admin-text-muted">{{ __('The display name for this attribute (e.g., Color, Size)') }}</div>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">{{ __('Slug') }}</label>
                <input name="slug" value="{{ old('slug',$model->slug ?? '') }}" class="admin-form-input" placeholder="{{ __('Auto-generated from name') }}" readonly>
                <div class="admin-text-muted small">{{ __('Slug will be generated from the attribute name.') }}</div>
                <div class="admin-text-muted">{{ __('URL-friendly version of the name (leave empty to auto-generate)') }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Attribute Configuration -->
<div class="admin-modern-card mb-4" data-section>
    <div class="admin-card-header d-flex justify-content-between align-items-center cursor-pointer" data-toggle-section>
        <h3 class="admin-card-title">
            <i class="fas fa-cog"></i>
            {{ __('Configuration') }}
        </h3>
        <i class="fas fa-chevron-down section-caret admin-text-muted"></i>
    </div>
    <div class="admin-card-body">
        <div class="admin-form-grid">
            <div class="admin-form-group">
                <label class="admin-form-label">{{ __('Attribute Type') }}</label>
                <select name="type" class="admin-form-input">
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
                <div class="admin-text-muted">{{ __('How customers will select this attribute') }}</div>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">{{ __('Display Position') }}</label>
                <input name="position" type="number" value="{{ old('position',$model->position ?? 0) }}" class="admin-form-input" min="0">
                <div class="admin-text-muted">{{ __('Order in which this attribute appears') }}</div>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">{{ __('Status') }}</label>
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" name="active" value="1"
                        @checked(old('active', $model->active ?? true)) id="attribute-active">
                    <label class="form-check-label" for="attribute-active">
                        {{ __('Active') }}
                    </label>
                </div>
                <div class="admin-text-muted">{{ __('Whether this attribute is available for use') }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Advanced Settings -->
<div class="admin-modern-card" data-section>
    <div class="admin-card-header d-flex justify-content-between align-items-center cursor-pointer" data-toggle-section>
        <h3 class="admin-card-title">
            <i class="fas fa-sliders-h"></i>
            {{ __('Advanced Settings') }}
        </h3>
        <i class="fas fa-chevron-down section-caret admin-text-muted"></i>
    </div>
    <div class="admin-card-body">
        <div class="admin-form-grid">
            <div class="admin-form-group">
                <label class="admin-form-label">{{ __('Description') }}</label>
                <textarea name="description" class="admin-form-input" rows="3" placeholder="{{ __('Optional description for this attribute') }}">{{ old('description',$model->description ?? '') }}</textarea>
                <div class="admin-text-muted">{{ __('Internal description to help identify this attribute') }}</div>
            </div>
            <div class="admin-form-group">
                <div class="admin-form-grid">
                    <div class="admin-form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="required" value="1"
                                @checked(old('required', $model->required ?? false)) id="attribute-required">
                            <label class="form-check-label" for="attribute-required">
                                {{ __('Required for products') }}
                            </label>
                            <div class="admin-text-muted">{{ __('Customers must select a value for this attribute') }}</div>
                        </div>
                    </div>
                    <div class="admin-form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="filterable" value="1"
                                @checked(old('filterable', $model->filterable ?? true)) id="attribute-filterable">
                            <label class="form-check-label" for="attribute-filterable">
                                {{ __('Use in product filters') }}
                            </label>
                            <div class="admin-text-muted">{{ __('Allow customers to filter products by this attribute') }}</div>
                        </div>
                    </div>
                    <div class="admin-form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="visible_on_product" value="1"
                                @checked(old('visible_on_product', $model->visible_on_product ?? true)) id="attribute-visible">
                            <label class="form-check-label" for="attribute-visible">
                                {{ __('Show on product page') }}
                            </label>
                            <div class="admin-text-muted">{{ __('Display this attribute on the product details page') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>