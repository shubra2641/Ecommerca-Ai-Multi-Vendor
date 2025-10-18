<!-- Form Toolbar -->
<div class="form-toolbar sticky-top bg-body pb-3 mb-3 border-bottom z-6">
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <div class="fw-semibold text-muted small">{{ __('Tag Form') }}</div>
        <div class="ms-auto d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" data-collapse-all>
                <span class="d-none d-sm-inline">{{ __('Collapse All') }}</span>
                <span class="d-sm-none">{{ __('Collapse') }}</span>
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" data-expand-all>
                <span class="d-none d-sm-inline">{{ __('Expand All') }}</span>
                <span class="d-sm-none">{{ __('Expand') }}</span>
            </button>
        </div>
    </div>
</div>

<!-- Basic Information Section -->
<div class="inner-section" data-section>
    <div class="inner-section-header d-flex justify-content-between align-items-center cursor-pointer" data-toggle-section>
        <h6 class="mb-0 small text-uppercase text-muted">
            <i class="fas fa-tag me-1 text-primary"></i>{{ __('Basic Information') }}
        </h6>
        <i class="fas fa-chevron-up section-caret text-muted small"></i>
    </div>
    <div class="inner-section-body pt-3">
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label class="form-label required">{{ __('Name') }}</label>
                <input type="text" name="name" id="tag-name" 
                       value="{{ old('name', $model->name ?? '') }}" 
                       class="form-control @error('name') is-invalid @enderror" 
                       placeholder="{{ __('Enter tag name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label required">{{ __('Slug') }}</label>
                <input type="text" name="slug" id="tag-slug" readonly
                       value="{{ old('slug', $model->slug ?? '') }}" 
                       class="form-control @error('slug') is-invalid @enderror" 
                       placeholder="{{ __('Auto-generated from name') }}" required>
                @error('slug')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">{{ __('URL-friendly version of the name. Leave empty to auto-generate.') }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Tag Configuration Section -->
<div class="inner-section" data-section>
    <div class="inner-section-header d-flex justify-content-between align-items-center cursor-pointer" data-toggle-section>
        <h6 class="mb-0 small text-uppercase text-muted">
            <i class="fas fa-cogs me-1 text-info"></i>{{ __('Tag Configuration') }}
        </h6>
        <i class="fas fa-chevron-up section-caret text-muted small"></i>
    </div>
    <div class="inner-section-body pt-3">
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label class="form-label">{{ __('Color') }}</label>
                <input type="color" name="color" 
                       value="{{ old('color', $model->color ?? '#007bff') }}" 
                       class="form-control form-control-color @error('color') is-invalid @enderror">
                @error('color')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">{{ __('Choose a color to represent this tag') }}</div>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">{{ __('Display Order') }}</label>
                <input type="number" name="sort_order" 
                       value="{{ old('sort_order', $model->sort_order ?? 0) }}" 
                       class="form-control @error('sort_order') is-invalid @enderror" 
                       min="0" placeholder="0">
                @error('sort_order')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">{{ __('Lower numbers appear first') }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Advanced Settings Section -->
<div class="inner-section" data-section>
    <div class="inner-section-header d-flex justify-content-between align-items-center cursor-pointer" data-toggle-section>
        <h6 class="mb-0 small text-uppercase text-muted">
            <i class="fas fa-sliders-h me-1 text-warning"></i>{{ __('Advanced Settings') }}
        </h6>
        <i class="fas fa-chevron-up section-caret text-muted small"></i>
    </div>
    <div class="inner-section-body pt-3">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">{{ __('Description') }}</label>
                <textarea name="description" rows="3" 
                          class="form-control @error('description') is-invalid @enderror" 
                          placeholder="{{ __('Optional description for this tag') }}">{{ old('description', $model->description ?? '') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12 col-md-6">
                <div class="form-check form-switch">
                    <input type="hidden" name="is_featured" value="0">
                    <input type="checkbox" name="is_featured" value="1" 
                           class="form-check-input @error('is_featured') is-invalid @enderror" 
                           id="is_featured" {{ old('is_featured', $model->is_featured ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_featured">
                        {{ __('Featured Tag') }}
                    </label>
                    @error('is_featured')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">{{ __('Featured tags appear prominently in listings') }}</div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-check form-switch">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" 
                           class="form-check-input @error('is_active') is-invalid @enderror" 
                           id="is_active" {{ old('is_active', $model->is_active ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        {{ __('Active') }}
                    </label>
                    @error('is_active')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">{{ __('Only active tags can be assigned to products') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
