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
                    <button type="button" class="btn btn-sm btn-outline-primary js-ai-generate-category" data-target-prefix="base" data-loading="0">
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
@push('scripts')
<script>
(function(){
    'use strict';
    function getCsrfToken(){
        var m = document.querySelector('meta[name="csrf-token"]');
        if (m && m.content) return m.content;
        var i = document.querySelector('input[name="_token"]');
        return i ? i.value : '';
    }
    function showFlash(type, message){
        var alert = document.createElement('div');
        alert.className = 'alert alert-' + (type === 'success' ? 'success' : 'danger') + ' alert-dismissible fade show';
        alert.role = 'alert';
        alert.innerText = message;
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn-close';
        btn.setAttribute('data-bs-dismiss', 'alert');
        btn.setAttribute('aria-label', 'Close');
        alert.appendChild(btn);
        var container = document.querySelector('.container, .page-container') || document.body;
        container.insertBefore(alert, container.firstChild);
        setTimeout(function(){
            if (alert && alert.parentNode) alert.parentNode.removeChild(alert);
        }, 6000);
    }
    function bindCategoryAiButtons(){
        document.querySelectorAll('.js-ai-generate-category, .js-ai-generate-category-i18n').forEach(function(btn){
            if (btn.dataset.bound === '1') return; btn.dataset.bound = '1';
            btn.addEventListener('click', function(ev){
                ev.preventDefault();
                if (btn.getAttribute('data-loading') === '1') return;
                var locale = btn.classList.contains('js-ai-generate-category-i18n') ? (btn.getAttribute('data-lang') || '') : (document.documentElement.lang || '');
                var prefix = btn.getAttribute('data-target-prefix') || 'base';
                var nameInput = prefix === 'base' ? document.querySelector('input[name="name"]') : document.querySelector('input[name="name_i18n[' + locale + ']"]');
                var title = nameInput ? nameInput.value.trim() : '';
                var descSelector = prefix === 'seo' ? 'textarea.js-cat-description-seo' : (prefix === 'base' ? 'textarea.js-cat-description-base' : 'textarea[name="description_i18n[' + locale + ']"], textarea.js-cat-description-i18n');
                var descArea = document.querySelector(descSelector);
                if (!title){
                    showFlash('error', '{{ __('Please enter the name first to generate description') }}');
                    return;
                }
                btn.setAttribute('data-loading', '1');
                btn.disabled = true; btn.classList.add('disabled');
                fetch("{{ route('admin.product-categories.ai.suggest') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify({ title: title, locale: locale })
                })
                .then(function(r){ return r.json().then(function(j){ return { ok: r.ok, status: r.status, body: j }; }); })
                .then(function(res){
                    if (res.ok){
                        try {
                            var description = (res.body && res.body.description) ? res.body.description : '';
                            if (descArea && description){ descArea.value = description; }
                        } catch(e){}
                        showFlash('success', '{{ __('AI description generated successfully') }}');
                    } else {
                        var msg = (res.body && (res.body.error || res.body.message)) || 'AI request failed (' + res.status + ')';
                        showFlash('error', msg);
                    }
                })
                .catch(function(err){
                    showFlash('error', err && err.message ? err.message : 'Network error');
                })
                .finally(function(){
                    btn.setAttribute('data-loading', '0');
                    btn.disabled = false; btn.classList.remove('disabled');
                });
            });
        });
    }
    if (document.readyState === 'loading'){
        document.addEventListener('DOMContentLoaded', bindCategoryAiButtons);
    } else {
        bindCategoryAiButtons();
    }
})();
</script>
@endpush

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
                                <button type="button" class="btn btn-xs btn-outline-primary js-ai-generate-category-i18n" data-lang="{{ $lang->code }}" data-loading="0"><i class="fas fa-magic"></i> AI</button>
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
                    <button type="button" class="btn btn-sm btn-outline-primary js-ai-generate-category" data-target-prefix="seo" data-loading="0">
                        <i class="fas fa-magic me-1"></i>{{ __('AI Generate') }}
                    </button>
                </label>
                <textarea name="seo_description" rows="3" class="form-control js-cat-description-seo">{{ old('seo_description',$model->seo_description ?? '') }}</textarea>
            </div>
        </div>
    </div>
</div>