<div class="form-toolbar sticky-top bg-body pb-3 mb-3 border-bottom z-6 shadow-sm toolbar-sticky-top">
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <div class="fw-semibold text-muted small">{{ __('Category Form') }}</div>
        <div class="ms-auto d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" data-collapse-all>{{ __('Collapse All') }}</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" data-expand-all>{{ __('Expand All') }}</button>
        </div>
    </div>
</div>
</div>
<div class="toolbar-spacer mb-3"></div>

<div class="inner-section" data-section>
    <div class="inner-section-header d-flex justify-content-between align-items-center cursor-pointer" data-toggle-section>
        <h6 class="mb-0 small text-uppercase text-muted"><i class="fas fa-folder-open me-1 text-primary"></i>{{ __('Basic Info') }}</h6>
        <i class="fas fa-chevron-up section-caret text-muted small"></i>
    </div>
    <div class="inner-section-body pt-3">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">{{ __('Parent') }}</label>
                <select name="parent_id" class="form-select">
                    <option value="">-- {{ __('None') }} --</option>
                    @foreach($parents ?? [] as $p)
                    <option value="{{ $p->id }}" @selected(old('parent_id',$model->parent_id ?? null)==$p->id)>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('Name (Default Locale)') }}</label>
                <input name="name" value="{{ old('name',$model->name ?? '') }}" class="form-control">
                <div class="form-text small">{{ __('Base name used as fallback when translation missing.') }}</div>
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('Slug') }}</label>
                <input name="slug" value="{{ old('slug',$model->slug ?? '') }}" class="form-control" readonly>
                <div class="form-text small">{{ __('Slug is auto-generated from the name.') }}</div>
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('Position') }}</label>
                <input type="number" name="position" value="{{ old('position',$model->position ?? 0) }}" class="form-control">
            </div>
            <div class="col-12">
                <label class="form-label d-flex justify-content-between align-items-center">
                    <span>{{ __('Description (Base)') }}</span>
                    <button type="submit" form="category-form" formaction="{{ route('admin.product-categories.ai.suggest') }}?target=base" formmethod="get" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-magic me-1"></i>{{ __('AI Generate') }}
                    </button>
                </label>
                <textarea name="description" rows="3" class="form-control js-cat-description-base">{{ old('description',$model->description ?? '') }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('Commission Rate (%)') }}</label>
                <input type="number" step="0.01" name="commission_rate" value="{{ old('commission_rate',$model->commission_rate ?? '') }}" class="form-control" placeholder="5">
                <div class="form-text small">{{ __('Leave empty to fallback to global rate.') }}</div>
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('Image') }}</label>
                <div class="input-group">
                    <input name="image" value="{{ old('image',$model->image ?? '') }}" class="form-control" placeholder="/storage/uploads/cat.jpg">
                    <button type="button" class="btn btn-outline-secondary" data-open-media="image"><i class="fas fa-folder-open"></i></button>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('Active') }}</label>
                <select name="active" class="form-select">
                    <option value="1" @selected(old('active',$model->active ?? 1)==1)>{{ __('Yes') }}</option>
                    <option value="0" @selected(old('active',$model->active ?? 1)==0)>{{ __('No') }}</option>
                </select>
            </div>
        </div>
    </div>
</div>

@php($langs = $activeLanguages ?? (\App\Models\Language::where('is_active',1)->orderByDesc('is_default')->get()))
<div class="inner-section mt-4" data-section>
    <div class="inner-section-header d-flex justify-content-between align-items-center cursor-pointer" data-toggle-section>
        <h6 class="mb-0 small text-uppercase text-muted"><i class="fas fa-language me-1 text-primary"></i>{{ __('Translations') }}</h6>
        <i class="fas fa-chevron-up section-caret text-muted small"></i>
    </div>
    <div class="inner-section-body pt-3">
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
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small mb-1">{{ __('Name') }}</label>
                        <input name="name_i18n[{{ $lang->code }}]" value="{{ old('name_i18n.'.$lang->code, $model->name_translations[$lang->code] ?? '') }}" class="form-control form-control-sm" placeholder="{{ __('Translated name') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small mb-1 d-flex justify-content-between align-items-center">
                            <span>{{ __('Description') }}</span>
                            <button type="submit" form="category-form" formaction="{{ route('admin.product-categories.ai.suggest') }}?target=i18n&locale={{ $lang->code }}" formmethod="get" class="btn btn-xs btn-outline-primary">
                                <i class="fas fa-magic"></i> AI
                            </button>
                        </label>
                        <textarea name="description_i18n[{{ $lang->code }}]" rows="2" class="form-control form-control-sm js-cat-description-i18n" placeholder="{{ __('Translated description') }}">{{ old('description_i18n.'.$lang->code, $model->description_translations[$lang->code] ?? '') }}</textarea>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="inner-section mt-4" data-section>
    <div class="inner-section-header d-flex justify-content-between align-items-center cursor-pointer" data-toggle-section>
        <h6 class="mb-0 small text-uppercase text-muted"><i class="fas fa-search me-1 text-primary"></i>{{ __('SEO') }}</h6>
        <i class="fas fa-chevron-up section-caret text-muted small"></i>
    </div>
    <div class="inner-section-body pt-3">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">{{ __('SEO Title') }}</label>
                <input name="seo_title" value="{{ old('seo_title',$model->seo_title ?? '') }}" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">{{ __('SEO Keywords (comma)') }}</label>
                <input name="seo_keywords" value="{{ old('seo_keywords',$model->seo_keywords ?? '') }}" class="form-control">
            </div>
            <div class="col-12">
                <label class="form-label d-flex justify-content-between align-items-center">
                    <span>{{ __('SEO Description') }}</span>
                    <button type="submit" form="category-form" formaction="{{ route('admin.product-categories.ai.suggest') }}?target=seo" formmethod="get" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-magic me-1"></i>{{ __('AI Generate') }}
                    </button>
                </label>
                <textarea name="seo_description" rows="3" class="form-control js-cat-description-seo">{{ old('seo_description',$model->seo_description ?? '') }}</textarea>
            </div>
        </div>
    </div>
</div>