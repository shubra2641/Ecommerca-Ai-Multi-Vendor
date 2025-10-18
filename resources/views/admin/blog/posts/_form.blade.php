@php($langs = $blogPostLanguages ?? ($blogPostLanguages = \App\Models\Language::where('is_active',1)->orderByDesc('is_default')->get()))
@php($defaultLang = $langs->firstWhere('is_default',1) ?? $langs->first())
<input type="hidden" id="blog-post-default-lang" value="{{ $defaultLang?->code }}">
@if($errors->any())
  <div class="alert alert-danger small"><ul class="mb-0">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>
@endif
<div class="card mb-4">
  <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
    <h5 class="mb-0"><i class="fas fa-language me-2 text-primary"></i>{{ __('Post Translations') }}</h5>
    <div class="d-flex flex-wrap gap-2">
      <button type="button" class="btn btn-sm btn-outline-secondary" data-copy-default>{{ __('Copy Default to Empty') }}</button>
    </div>
  </div>
  <div class="card-body p-0">
    <div class="lang-tabs-wrapper border-bottom">
      <ul class="nav nav-tabs small flex-nowrap overflow-auto gap-2 lang-tabs px-3" role="tablist">
        @foreach($langs as $i=>$lang)
          <li class="nav-item" role="presentation">
            <button class="nav-link py-1 px-3 @if($i===0) active @endif" data-bs-toggle="tab" data-bs-target="#blog-post-lang-{{ $lang->code }}" type="button" role="tab">
              {{ strtoupper($lang->code) }} @if($lang->is_default)<span class="badge bg-primary ms-1">{{ __('Default') }}</span>@endif
            </button>
          </li>
        @endforeach
      </ul>
    </div>
    <div class="tab-content p-3 border-top">
      @php($titleTr = isset($post)? ($post->title_translations ?? []) : [])
      @php($slugTr = isset($post)? ($post->slug_translations ?? []) : [])
      @php($excerptTr = isset($post)? ($post->excerpt_translations ?? []) : [])
      @php($bodyTr = isset($post)? ($post->body_translations ?? []) : [])
      @php($seoTitleTr = isset($post)? ($post->seo_title_translations ?? []) : [])
      @php($seoDescTr = isset($post)? ($post->seo_description_translations ?? []) : [])
      @php($seoTagsTr = isset($post)? ($post->seo_tags_translations ?? []) : [])
      @foreach($langs as $i=>$lang)
        @php($code = $lang->code)
        <div class="tab-pane fade @if($i===0) show active @endif" id="blog-post-lang-{{ $code }}" role="tabpanel">
          <div class="row g-3">
            <div class="col-md-8">
              <label class="form-label small fw-semibold">{{ __('Title') }}</label>
              <input name="title[{{ $code }}]" value="{{ old('title.'.$code, $titleTr[$code] ?? ($lang->is_default && isset($post) ? $post->getRawOriginal('title') : '')) }}" class="form-control form-control-sm" @if($lang->is_default) required @endif placeholder="{{ __('Post title') }}">
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-semibold">{{ __('Slug') }}</label>
              <input name="slug[{{ $code }}]" value="{{ old('slug.'.$code, $slugTr[$code] ?? ($lang->is_default && isset($post) ? $post->getRawOriginal('slug') : '')) }}" class="form-control form-control-sm" placeholder="auto" readonly>
              @if($lang->is_default)<div class="form-text small">{{ __('Auto from title') }}</div>@endif
            </div>
            <div class="col-12">
              <label class="form-label small fw-semibold d-flex justify-content-between align-items-center">
                <span>{{ __('Excerpt') }} <small class="text-muted" data-counter-display="excerpt-{{ $code }}">0/300</small></span>
                <button type="button" class="btn btn-xs btn-outline-primary js-ai-generate-post" data-lang="{{ $code }}" data-target="excerpt" data-loading="0"><i class="fas fa-magic"></i> AI</button>
              </label>
              <textarea name="excerpt[{{ $code }}]" rows="2" class="form-control form-control-sm js-post-excerpt" data-counter="excerpt-{{ $code }}" data-max="300" placeholder="{{ __('Short summary (<=300 chars)') }}">{{ old('excerpt.'.$code, $excerptTr[$code] ?? ($lang->is_default && isset($post) ? $post->getRawOriginal('excerpt') : '')) }}</textarea>
            </div>
            <div class="col-12">
              <label class="form-label small fw-semibold d-flex justify-content-between align-items-center">
                <span>{{ __('Body') }}</span>
                <button type="button" class="btn btn-xs btn-outline-primary js-ai-generate-post" data-lang="{{ $code }}" data-target="body" data-loading="0"><i class="fas fa-magic"></i> AI</button>
              </label>
              <textarea name="body[{{ $code }}]" rows="10" class="form-control form-control-sm js-post-body" placeholder="{{ __('Main article content') }}">{{ old('body.'.$code, $bodyTr[$code] ?? ($lang->is_default && isset($post) ? $post->getRawOriginal('body') : '')) }}</textarea>
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-semibold">{{ __('SEO Title') }}</label>
              <input name="seo_title[{{ $code }}]" value="{{ old('seo_title.'.$code, $seoTitleTr[$code] ?? ($lang->is_default && isset($post) ? $post->getRawOriginal('seo_title') : '')) }}" class="form-control form-control-sm" placeholder="{{ __('Optional SEO title') }}">
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-semibold d-flex justify-content-between align-items-center">
                <span>{{ __('SEO Description') }} <small class="text-muted" data-counter-display="seodesc-{{ $code }}">0/160</small></span>
                <button type="button" class="btn btn-xs btn-outline-primary js-ai-generate-post-seo" data-lang="{{ $code }}" data-loading="0"><i class="fas fa-magic"></i> AI</button>
              </label>
              <input name="seo_description[{{ $code }}]" value="{{ old('seo_description.'.$code, $seoDescTr[$code] ?? ($lang->is_default && isset($post) ? $post->getRawOriginal('seo_description') : '')) }}" class="form-control form-control-sm js-post-seo-description" data-counter="seodesc-{{ $code }}" data-max="160" placeholder="{{ __('Meta description') }}">
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-semibold">{{ __('SEO Tags') }}</label>
              <input name="seo_tags[{{ $code }}]" value="{{ old('seo_tags.'.$code, $seoTagsTr[$code] ?? ($lang->is_default && isset($post) ? $post->getRawOriginal('seo_tags') : '')) }}" class="form-control form-control-sm" placeholder="tag1,tag2">
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</div>
<div class="card mb-4">
  <div class="card-header"><h5 class="mb-0"><i class="fas fa-cogs me-2 text-primary"></i>{{ __('Post Settings') }}</h5></div>
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label small fw-semibold">{{ __('Category') }}</label>
        <select name="category_id" class="form-select form-select-sm">
          <option value="">--</option>
          @foreach($categories as $c)
            <option value="{{ $c->id }}" @if(old('category_id', isset($post)?$post->category_id:null)==$c->id) selected @endif>{{ $c->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label small fw-semibold">{{ __('Tags') }}</label>
        <div class="border rounded p-2 h-120 overflow-auto small">
          @foreach($tags as $t)
            @php($checked = in_array($t->id, old('tags', isset($post)? $post->tags->pluck('id')->all(): [])))
            <label class="d-block"><input type="checkbox" name="tags[]" value="{{ $t->id }}" @if($checked) checked @endif> {{ $t->name }}</label>
          @endforeach
        </div>
      </div>
      <div class="col-md-4">
        <label class="form-label small fw-semibold">{{ __('Featured Image') }}</label>
        <div class="border rounded p-2 text-center h-120 position-relative bg-light" id="featPreview">
          @if(isset($post) && $post->featured_image)
            <img src="{{ asset('storage/'.$post->featured_image) }}" class="obj-cover w-100 h-100">
          @else
            <div class="small text-muted pt-4">{{ __('Choose Image') }}</div>
          @endif
        </div>
        <input type="hidden" name="featured_image_path" id="featured_image_path" value="{{ isset($post)&&$post->featured_image ? asset('storage/'.$post->featured_image) : '' }}">
        <small class="text-muted d-block mt-1">{{ __('Click box to pick / upload') }}</small>
      </div>
    </div>
  </div>
</div>
@if(isset($post))
  <div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h6 class="mb-0"><i class="fas fa-bolt me-2 text-primary"></i>{{ __('Publishing') }}</h6>
      <form method="POST" action="{{ route('admin.blog.posts.publish',$post) }}" class="d-inline">@csrf <button type="submit" class="btn btn-sm btn-outline-secondary">@if($post->published) {{ __('Unpublish') }} @else {{ __('Publish') }} @endif</button></form>
    </div>
    <div class="card-body small text-muted">
      @if($post->published)
        {{ __('Published at:') }} {{ $post->published_at }}
      @else
        {{ __('Not published yet.') }}
      @endif
    </div>
  </div>
@endif
