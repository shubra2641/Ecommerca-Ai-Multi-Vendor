<!-- Basic Information Section -->
<div class="admin-form-grid">
    <div class="admin-form-group">
        <label class="admin-form-label required">{{ __('Name') }}</label>
        <input type="text" name="name" id="tag-name"
            value="{{ old('name', $model->name ?? '') }}"
            class="admin-form-input @error('name') is-invalid @enderror"
            placeholder="{{ __('Enter tag name') }}" required>
        @error('name')
        <div class="admin-text-danger">{{ $message }}</div>
        @enderror
    </div>
    <div class="admin-form-group">
        <label class="admin-form-label required">{{ __('Slug') }}</label>
        <input type="text" name="slug" id="tag-slug" readonly
            value="{{ old('slug', $model->slug ?? '') }}"
            class="admin-form-input @error('slug') is-invalid @enderror"
            placeholder="{{ __('Auto-generated from name') }}" required>
        @error('slug')
        <div class="admin-text-danger">{{ $message }}</div>
        @enderror
        <div class="admin-text-muted">{{ __('URL-friendly version of the name. Leave empty to auto-generate.') }}</div>
    </div>
</div>

<!-- Tag Configuration Section -->
<div class="admin-form-grid">
    <div class="admin-form-group">
        <label class="admin-form-label">{{ __('Color') }}</label>
        <input type="color" name="color"
            value="{{ old('color', $model->color ?? '#007bff') }}"
            class="admin-form-input @error('color') is-invalid @enderror">
        @error('color')
        <div class="admin-text-danger">{{ $message }}</div>
        @enderror
        <div class="admin-text-muted">{{ __('Choose a color to represent this tag') }}</div>
    </div>
    <div class="admin-form-group">
        <label class="admin-form-label">{{ __('Display Order') }}</label>
        <input type="number" name="sort_order"
            value="{{ old('sort_order', $model->sort_order ?? 0) }}"
            class="admin-form-input @error('sort_order') is-invalid @enderror"
            min="0" placeholder="0">
        @error('sort_order')
        <div class="admin-text-danger">{{ $message }}</div>
        @enderror
        <div class="admin-text-muted">{{ __('Lower numbers appear first') }}</div>
    </div>
</div>

<!-- Advanced Settings Section -->
<div class="admin-form-group">
    <label class="admin-form-label">{{ __('Description') }}</label>
    <textarea name="description" rows="3"
        class="admin-form-input @error('description') is-invalid @enderror"
        placeholder="{{ __('Optional description for this tag') }}">{{ old('description', $model->description ?? '') }}</textarea>
    @error('description')
    <div class="admin-text-danger">{{ $message }}</div>
    @enderror
</div>

<div class="admin-form-grid">
    <div class="admin-form-group">
        <div class="form-check">
            <input type="hidden" name="is_featured" value="0">
            <input type="checkbox" name="is_featured" value="1"
                class="form-check-input @error('is_featured') is-invalid @enderror"
                id="is_featured" {{ old('is_featured', $model->is_featured ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_featured">
                <i class="fas fa-star"></i>
                {{ __('Featured Tag') }}
            </label>
            @error('is_featured')
            <div class="admin-text-danger">{{ $message }}</div>
            @enderror
            <div class="admin-text-muted">{{ __('Featured tags appear prominently in listings') }}</div>
        </div>
    </div>
    <div class="admin-form-group">
        <div class="form-check">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1"
                class="form-check-input @error('is_active') is-invalid @enderror"
                id="is_active" {{ old('is_active', $model->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">
                <i class="fas fa-check"></i>
                {{ __('Active') }}
            </label>
            @error('is_active')
            <div class="admin-text-danger">{{ $message }}</div>
            @enderror
            <div class="admin-text-muted">{{ __('Only active tags can be assigned to products') }}</div>
        </div>
    </div>
</div>