<div class="admin-form-toolbar sticky-top bg-body pb-3 mb-3 border-bottom z-6 shadow-sm">
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <div class="admin-text-muted small fw-semibold">{{ __('Category Form') }}</div>
        <div class="ms-auto d-flex gap-2">
            <button type="button" class="admin-btn admin-btn-small admin-btn-outline" data-collapse-all>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 12H16M12 8V16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                {{ __('Collapse All') }}
            </button>
            <button type="button" class="admin-btn admin-btn-small admin-btn-outline" data-expand-all>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 12H16M12 8V16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                {{ __('Expand All') }}
            </button>
        </div>
    </div>
</div>
<div class="toolbar-spacer mb-3"></div>

<div class="admin-modern-card mb-4" data-section>
    <div class="admin-card-header d-flex justify-content-between align-items-center cursor-pointer" data-toggle-section>
        <h3 class="admin-card-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M22 19C22 19.5304 21.7893 20.0391 21.4142 20.4142C21.0391 20.7893 20.5304 21 20 21H4C3.46957 21 2.96086 20.7893 2.58579 20.4142C2.21071 20.0391 2 19.5304 2 19V5C2 4.46957 2.21071 3.96086 2.58579 3.58579C2.96086 3.21071 3.46957 3 4 3H15L22 10V19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M15 3V10H22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            {{ __('Basic Info') }}
        </h3>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="section-caret admin-text-muted">
            <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
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
                    <button type="submit" form="category-form" formaction="{{ route('admin.product-categories.ai.suggest') }}?target=base" formmethod="get" class="admin-btn admin-btn-small admin-btn-outline">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        {{ __('AI Generate') }}
                    </button>
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
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22 19C22 19.5304 21.7893 20.0391 21.4142 20.4142C21.0391 20.7893 20.5304 21 20 21H4C3.46957 21 2.96086 20.7893 2.58579 20.4142C2.21071 20.0391 2 19.5304 2 19V5C2 4.46957 2.21071 3.96086 2.58579 3.58579C2.96086 3.21071 3.46957 3 4 3H15L22 10V19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M15 3V10H22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
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
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            {{ __('Translations') }}
        </h3>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="section-caret admin-text-muted">
            <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
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
                        <input name="name_i18n[{{ $lang->code }}]" value="{{ old('name_i18n.'.$lang->code, $model->name_translations[$lang->code] ?? '') }}" class="admin-form-input" placeholder="{{ __('Translated name') }}">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label d-flex justify-content-between align-items-center">
                            <span>{{ __('Description') }}</span>
                            <button type="submit" form="category-form" formaction="{{ route('admin.product-categories.ai.suggest') }}?target=i18n&locale={{ $lang->code }}" formmethod="get" class="admin-btn admin-btn-small admin-btn-outline">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                AI
                            </button>
                        </label>
                        <textarea name="description_i18n[{{ $lang->code }}]" rows="2" class="admin-form-input js-cat-description-i18n" placeholder="{{ __('Translated description') }}">{{ old('description_i18n.'.$lang->code, $model->description_translations[$lang->code] ?? '') }}</textarea>
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
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2" />
                <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            {{ __('SEO') }}
        </h3>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="section-caret admin-text-muted">
            <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </div>
    <div class="admin-card-body">
        <div class="admin-form-grid">
            <div class="admin-form-group">
                <label class="admin-form-label">{{ __('SEO Title') }}</label>
                <input name="seo_title" value="{{ old('seo_title',$model->seo_title ?? '') }}" class="admin-form-input">
            </div>
            <div class="admin-form-group">
                <label class="admin-form-label">{{ __('SEO Keywords (comma)') }}</label>
                <input name="seo_keywords" value="{{ old('seo_keywords',$model->seo_keywords ?? '') }}" class="admin-form-input">
            </div>
            <div class="admin-form-group admin-form-group-wide">
                <label class="admin-form-label d-flex justify-content-between align-items-center">
                    <span>{{ __('SEO Description') }}</span>
                    <button type="submit" form="category-form" formaction="{{ route('admin.product-categories.ai.suggest') }}?target=seo" formmethod="get" class="admin-btn admin-btn-small admin-btn-outline">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        {{ __('AI Generate') }}
                    </button>
                </label>
                <textarea name="seo_description" rows="3" class="admin-form-input js-cat-description-seo">{{ old('seo_description',$model->seo_description ?? '') }}</textarea>
            </div>
        </div>
    </div>
</div>