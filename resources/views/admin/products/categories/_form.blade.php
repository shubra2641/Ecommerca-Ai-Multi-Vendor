<button type="submit" form="category-form" formaction="{{ route('admin.product-categories.ai.suggest') }}?target=base" formmethod="get" class="admin-btn admin-btn-small admin-btn">
    <i class="fas fa-lightbulb"></i>
    {{ __('AI Generate') }}
</button>

<div class="toolbar-spacer mb-3"></div>

<div class="admin-modern-card mb-4" data-section>
    <div class="admin-card-header d-flex justify-content-between align-items-center cursor-pointer" data-toggle-section>
        <h3 class="admin-card-title">
            <i class="fas fa-file-alt"></i>
            {{ __('Basic Info') }}
        </h3>
        <i class="fas fa-chevron-down section-caret admin-text-muted"></i>
    </div>
    <div class="admin-card-body">
        <div class="admin-form-grid">
            <div class="admin-form-group">
                <label class="admin-form-label">{{ __('Parent') }}</label>
                <select name="parent_id" class="admin-form-input">
                    <option value="">-- {{ __('None') }} --</option>
                    @foreach($parents ?? [] as $p)
                    <option value="{{ $p->id }}" @selected(old('parent_id',$model->parent_id ?? null)==$p->id)>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">{{ __('Name (Default Locale)') }}</label>
                <input name="name" value="{{ old('name',$model->name ?? '') }}" class="admin-form-input">
                <div class="admin-text-muted small">{{ __('Base name used as fallback when translation missing.') }}</div>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">{{ __('Slug') }}</label>
                <input name="slug" value="{{ old('slug',$model->slug ?? '') }}" class="admin-form-input" readonly>
                <div class="admin-text-muted small">{{ __('Slug is auto-generated from the name.') }}</div>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">{{ __('Position') }}</label>
                <input type="number" name="position" value="{{ old('position',$model->position ?? 0) }}" class="admin-form-input">
            </div>
            <div class="admin-form-group admin-form-group-wide">
                <label class="admin-form-label d-flex justify-content-between align-items-center">
                    <span>{{ __('Description (Base)') }}</span>
                </label>
                <textarea name="description" rows="3" class="admin-form-input js-cat-description-base">{{ old('description',$model->description ?? '') }}</textarea>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">{{ __('Commission Rate (%)') }}</label>
                <input type="number" step="0.01" name="commission_rate" value="{{ old('commission_rate',$model->commission_rate ?? '') }}" class="admin-form-input" placeholder="5">
                <div class="admin-text-muted small">{{ __('Leave empty to fallback to global rate.') }}</div>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">{{ __('Image') }}</label>
                <div class="input-group">
                    <input name="image" value="{{ old('image',$model->image ?? '') }}" class="admin-form-input" placeholder="/storage/uploads/cat.jpg">
                    <button type="button" class="admin-btn admin-btn-outline" data-open-media="image">
                        <i class="fas fa-folder-open"></i>
                    </button>
                </div>
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">{{ __('Active') }}</label>
                <select name="active" class="admin-form-input">
                    <option value="1" @selected(old('active',$model->active ?? 1)==1)>{{ __('Yes') }}</option>
                    <option value="0" @selected(old('active',$model->active ?? 1)==0)>{{ __('No') }}</option>
                </select>
            </div>
        </div>
    </div>
</div>

@php($langs = $activeLanguages ?? (\App\Models\Language::where('is_active',1)->orderByDesc('is_default')->get()))
<div class="admin-modern-card mb-4" data-section>
    <div class="admin-card-header d-flex justify-content-between align-items-center cursor-pointer" data-toggle-section>
        <h3 class="admin-card-title">
            <i class="fas fa-star"></i>
            {{ __('Translations') }}
        </h3>
        <i class="fas fa-chevron-down section-caret admin-text-muted"></i>
    </div>
    <div class="admin-card-body">
        <div class="lang-tabs-wrapper" data-lang-tabs-variant="primary">
            <ul class="nav nav-tabs small flex-nowrap overflow-auto gap-2 lang-tabs px-1" role="tablist">
                @foreach($langs as $i=>$lang)
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-1 px-3 @if($i===0) active @endif" data-bs-toggle="tab" data-bs-target="#pcat-lang-{{ $lang->code }}" type="button" role="tab">{{ strtoupper($lang->code) }}</button>
                </li>
                @endforeach
            </ul>
        </div>
        <div class="tab-content border rounded-bottom p-3 mt-2">
            @foreach($langs as $i=>$lang)
            <div class="tab-pane fade @if($i===0) show active @endif" id="pcat-lang-{{ $lang->code }}" role="tabpanel">
                <div class="admin-form-grid">
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Name') }}</label>
                        <input name="name_i18n[{{ $lang->code }}]" value="{{ is_string(old('name_i18n.'.$lang->code)) ? old('name_i18n.'.$lang->code) : (is_array($model->name_translations ?? null) ? ($model->name_translations[$lang->code] ?? '') : '') }}" class="admin-form-input" placeholder="{{ __('Translated name') }}">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label d-flex justify-content-between align-items-center">
                            <span>{{ __('Description') }}</span>
                        </label>
                        <textarea name="description_i18n[{{ $lang->code }}]" rows="2" class="admin-form-input js-cat-description-i18n" placeholder="{{ __('Translated description') }}">{{ is_string(old('description_i18n.'.$lang->code)) ? old('description_i18n.'.$lang->code) : (is_array($model->description_translations ?? null) ? ($model->description_translations[$lang->code] ?? '') : '') }}</textarea>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="admin-modern-card" data-section>
    <div class="admin-card-header d-flex justify-content-between align-items-center cursor-pointer" data-toggle-section>
        <h3 class="admin-card-title">
            <i class="fas fa-search"></i>
            {{ __('SEO') }}
        </h3>
        <i class="fas fa-chevron-down section-caret admin-text-muted"></i>
    </div>
    <div class="admin-card-body">
        <div class="admin-form-grid">
            <div class="admin-form-group">
                <label class="admin-form-label">{{ __('SEO Title') }}</label>
                <input name="seo_title" value="{{ is_string(old('seo_title')) ? old('seo_title') : ($model->seo_title ?? '') }}" class="admin-form-input">
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">{{ __('SEO Keywords (comma)') }}</label>
                <input name="seo_keywords" value="{{ is_string(old('seo_keywords')) ? old('seo_keywords') : ($model->seo_keywords ?? '') }}" class="admin-form-input">
            </div>
            <div class="admin-form-group admin-form-group-wide">
                <textarea name="seo_description" rows="3" class="admin-form-input js-cat-description-seo">{{ is_string(old('seo_description')) ? old('seo_description') : ($model->seo_description ?? '') }}</textarea>
            </div>
        </div>
    </div>
</div>