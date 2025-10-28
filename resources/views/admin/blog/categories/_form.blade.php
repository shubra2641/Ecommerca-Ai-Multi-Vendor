@php($langs = $blogPostLanguages ?? ($blogPostLanguages = \App\Models\Language::where('is_active',1)->orderByDesc('is_default')->get()))
@php($defaultLang = $blogPostDefaultLanguage ?? ($langs->firstWhere('is_default',1) ?? $langs->first()))
<input type="hidden" id="blog-cat-default-lang" value="{{ $defaultLang?->code }}">
<div class="card mb-4">
  <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
    <h5 class="mb-0"><i class="fas fa-language me-2 text-primary"></i>{{ __('Category Translations') }}</h5>
    <div class="d-flex flex-wrap gap-2">
      <button type="button" class="btn btn-sm btn-outline-secondary" data-copy-default>{{ __('Copy Default to Empty') }}</button>
    </div>
  </div>
  <div class="card-body p-0">
    <div class="lang-tabs-wrapper border-bottom">
      <ul class="nav nav-tabs small flex-nowrap overflow-auto gap-2 lang-tabs px-3" role="tablist">
        @foreach($langs as $i=>$lang)
        <li class="nav-item" role="presentation">
          <button class="nav-link py-1 px-3 @if($i===0) active @endif" data-bs-toggle="tab" data-bs-target="#blog-cat-lang-{{ $lang->code }}" type="button" role="tab">
            {{ strtoupper($lang->code) }} @if($lang->is_default)<span class="badge bg-primary ms-1">{{ __('Default') }}</span>@endif
          </button>
        </li>
        @endforeach
      </ul>
    </div>
    <div class="tab-content p-3 border-top">
      @foreach($langs as $i=>$lang)
      @php($code = $lang->code)
      @php($nameTranslations = isset($category) ? ($category->name_translations ?? []) : [])
      @php($slugTranslations = isset($category) ? ($category->slug_translations ?? []) : [])
      @php($descTranslations = isset($category) ? ($category->description_translations ?? []) : [])
      @php($seoTitleTranslations = isset($category) ? ($category->seo_title_translations ?? []) : [])
      @php($seoDescTranslations = isset($category) ? ($category->seo_description_translations ?? []) : [])
      @php($seoTagsTranslations = isset($category) ? ($category->seo_tags_translations ?? []) : [])
      <div class="tab-pane fade @if($i===0) show active @endif" id="blog-cat-lang-{{ $code }}" role="tabpanel">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label small fw-semibold">{{ __('Name') }}</label>
            <input name="name[{{ $code }}]" value="{{ is_string(old('name.'.$code)) ? old('name.'.$code) : ($nameTranslations[$code] ?? ($lang->is_default && isset($category) ? $category->getRawOriginal('name') : '')) }}" class="form-control form-control-sm" @if($lang->is_default) required @endif placeholder="{{ __('Category name') }}">
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-semibold">{{ __('Slug') }}</label>
            <input name="slug[{{ $code }}]" value="{{ old('slug.'.$code, $slugTranslations[$code] ?? ($lang->is_default && isset($category) ? $category->getRawOriginal('slug') : '')) }}" class="form-control form-control-sm" placeholder="auto" readonly>
            @if($lang->is_default)
            <div class="form-text small">{{ __('Auto-generated from name') }}</div>
            @endif
          </div>
          <div class="col-12">
            <label class="form-label small fw-semibold d-flex justify-content-between align-items-center">
              <span>{{ __('Description') }}</span>
              <button type="submit" form="category-form-enhanced" formaction="{{ route('admin.blog.categories.ai.suggest') }}?target=description&locale={{ $code }}" formmethod="get" class="btn btn-xs btn-outline-primary"><i class="fas fa-magic"></i> AI</button>
            </label>
            <textarea name="description[{{ $code }}]" rows="3" class="form-control form-control-sm js-blog-cat-description" placeholder="{{ __('Short descriptive text about this category') }}">{{ is_string(old('description.'.$code)) ? old('description.'.$code) : ($descTranslations[$code] ?? ($lang->is_default && isset($category) ? $category->getRawOriginal('description') : '')) }}</textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-semibold">{{ __('SEO Title') }}</label>
            <input name="seo_title[{{ $code }}]" value="{{ is_string(old('seo_title.'.$code)) ? old('seo_title.'.$code) : ($seoTitleTranslations[$code] ?? ($lang->is_default && isset($category) ? $category->getRawOriginal('seo_title') : '')) }}" class="form-control form-control-sm" placeholder="{{ __('Optional SEO title') }}">
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-semibold d-flex justify-content-between align-items-center">
              <span>{{ __('SEO Description') }}</span>
              <button type="submit" form="category-form-enhanced" formaction="{{ route('admin.blog.categories.ai.suggest') }}?target=seo&locale={{ $code }}" formmethod="get" class="btn btn-xs btn-outline-primary"><i class="fas fa-magic"></i> AI</button>
            </label>
            <input name="seo_description[{{ $code }}]" value="{{ is_string(old('seo_description.'.$code)) ? old('seo_description.'.$code) : ($seoDescTranslations[$code] ?? ($lang->is_default && isset($category) ? $category->getRawOriginal('seo_description') : '')) }}" class="form-control form-control-sm js-blog-cat-seo-description" placeholder="{{ __('Meta description (<=160 chars)') }}">
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-semibold">{{ __('SEO Tags') }}</label>
            <input name="seo_tags[{{ $code }}]" value="{{ is_string(old('seo_tags.'.$code)) ? old('seo_tags.'.$code) : ($seoTagsTranslations[$code] ?? ($lang->is_default && isset($category) ? $category->getRawOriginal('seo_tags') : '')) }}" class="form-control form-control-sm" placeholder="tag1,tag2">
            <div class="form-text small">{{ __('Comma separated keywords') }}</div>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</div>
<div class="card mb-4">
  <div class="card-header">
    <h5 class="mb-0"><i class="fas fa-sitemap me-2 text-primary"></i>{{ __('Hierarchy & Settings') }}</h5>
  </div>
  <div class="card-body">
    <div class="row g-3 align-items-end">
      <div class="col-md-4">
        <label class="form-label small fw-semibold">{{ __('Parent Category') }}</label>
        <select name="parent_id" class="form-select form-select-sm">
          <option value="">-- {{ __('None') }} --</option>
          @foreach($parents as $p)
          <option value="{{ $p->id }}" @if(old('parent_id', isset($category)?$category->parent_id:null)==$p->id) selected @endif>{{ $p->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-8">
        <div class="alert alert-info py-2 px-3 small mb-0"><i class="fas fa-info-circle me-1"></i>{{ __('Set a parent to build nested blog category navigation.') }}</div>
      </div>
    </div>
  </div>
</div>