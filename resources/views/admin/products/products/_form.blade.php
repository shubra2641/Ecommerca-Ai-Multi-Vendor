{{-- Data supplied via AdminProductFormComposer (ProductFormBuilder) --}}

<div class="row g-4" data-default-locale="{{ $defaultLocale }}">
    <!-- Main Content -->
    <div class="col-xl-8 col-lg-7">
        <!-- Basic Information -->
        <div class="admin-modern-card mb-4">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('Basic Information') }}
                </h3>
            </div>
            <div class="admin-card-body">
                <div class="row g-3">
                    {{-- languages provided: $pfLanguages --}}
                    <div class="col-12">
                        <div class="lang-tabs-wrapper" data-lang-tabs-variant="success">
                            <ul class="nav nav-tabs small flex-nowrap overflow-auto gap-2 lang-tabs px-1" id="prodLangTabs" role="tablist">
                                @php $activeLocale = $currentLocale ?? app()->getLocale(); @endphp
                                @foreach($languages as $i => $lang)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link py-1 px-3 @if($lang->code===$activeLocale) active @endif" id="prod-tab-{{ $lang->code }}"
                                        data-bs-toggle="tab" data-bs-target="#prod-panel-{{ $lang->code }}" type="button"
                                        role="tab">{{ strtoupper($lang->code) }}</button>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="tab-content border rounded-bottom p-3">
                            @foreach($languages as $i => $lang)
                            <div class="tab-pane fade @if($lang->code===$activeLocale) show active @endif" id="prod-panel-{{ $lang->code }}"
                                role="tabpanel">
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('Name') }}</label>
                                    <input name="name[{{ $lang->code }}]" value="{{ $pfLangMeta[$lang->code]['name_val'] }}" class="admin-form-input" @if($lang->code===$defaultLocale) required @endif placeholder="{{ $pfLangMeta[$lang->code]['ph_name'] }}">
                                </div>
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('Short Description') }}</label>
                                    <textarea name="short_description[{{ $lang->code }}]" class="admin-form-input" rows="3" placeholder="{{ $pfLangMeta[$lang->code]['ph_short'] }}">{{ $pfLangMeta[$lang->code]['short_val'] }}</textarea>
                                </div>
                                <div class="admin-form-group">
                                    <label class="admin-form-label d-flex justify-content-between align-items-center">
                                        <span>{{ __('Description') }}</span>
                                        <button type="submit" form="product-form" formaction="{{ route('admin.products.ai.suggest') }}?target=description&locale={{ $lang->code }}" formmethod="get" class="admin-btn admin-btn-small admin-btn-outline">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            {{ __('AI Generate') }}
                                        </button>
                                    </label>
                                    <textarea name="description[{{ $lang->code }}]" class="admin-form-input js-ai-description" rows="6" placeholder="{{ $pfLangMeta[$lang->code]['ph_desc'] }}">{{ $pfLangMeta[$lang->code]['desc_val'] }}</textarea>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Configuration -->
        <div class="admin-modern-card mb-4">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" />
                        <path d="M12 1V3M12 21V23M4.22 4.22L5.64 5.64M18.36 18.36L19.78 19.78M1 12H3M21 12H23M4.22 19.78L5.64 18.36M18.36 5.64L19.78 4.22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('Product Configuration') }}
                </h3>
            </div>
            <div class="admin-card-body">
                <div class="admin-form-grid">
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Type') }}</label>
                        <select name="type" class="admin-form-input" id="type-select">
                            <option value="simple" @selected(old('type',$m?->type ??
                                'simple')==='simple')>{{ __('Simple') }}</option>
                            <option value="variable" @selected(old('type',$m?->type ??
                                '')==='variable')>{{ __('Variable') }}</option>
                        </select>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Physical/Digital') }}</label>
                        <select name="physical_type" class="admin-form-input">
                            <option value="physical" @selected(old('physical_type',$m?->physical_type ??
                                'physical')==='physical')>{{ __('Physical') }}</option>
                            <option value="digital" @selected(old('physical_type',$m?->physical_type ??
                                '')==='digital')>{{ __('Digital') }}</option>
                        </select>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('SKU') }}</label>
                        <input name="sku" value="{{ old('sku',$m?->sku) }}" class="admin-form-input">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Category') }}</label>
                        <select name="product_category_id" class="admin-form-input">
                            @foreach($categories as $c)
                            <option value="{{ $c->id }}" @selected(old('product_category_id',$m?->product_category_id ??
                                '')==$c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Simple Product Pricing -->
                <div class="admin-form-grid mt-4 simple-only">
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Price') }}</label>
                        <input type="number" step="0.01" name="price" value="{{ old('price',$m?->price ?? 0) }}"
                            class="admin-form-input">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Sale Price') }}</label>
                        <input type="number" step="0.01" name="sale_price"
                            value="{{ old('sale_price',$m?->sale_price) }}" class="admin-form-input">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Sale Start') }}</label>
                        <input type="datetime-local" name="sale_start"
                            value="{{ old('sale_start',optional($m?->sale_start)->format('Y-m-d\\TH:i')) }}"
                            class="admin-form-input">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Sale End') }}</label>
                        <input type="datetime-local" name="sale_end"
                            value="{{ old('sale_end',optional($m?->sale_end)->format('Y-m-d\\TH:i')) }}"
                            class="admin-form-input">
                    </div>
                </div>

                <!-- Variable Product Variations -->
                <div class="variable-only mt-4 envato-hidden">
                    <h6 class="admin-card-title mb-3">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 6H21M3 12H21M3 18H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        {{ __('Variations') }}
                    </h6>
                    @if($errors->has('variations') ||
                    collect($errors->keys())->filter(fn($k)=>str_starts_with($k,'variations.'))->isNotEmpty())
                    <div class="alert alert-danger small">
                        <ul class="mb-0">
                            @foreach($errors->all() as $e)
                            @if(str_starts_with($e,'Duplicate attribute combination') || str_contains($e,'variations'))
                            <li>{{ $e }}</li>
                            @endif
                            @endforeach
                            @foreach($errors->keys() as $k)
                            @if(str_starts_with($k,'variations.'))
                            @foreach($errors->get($k) as $msg)
                            <li>{{ $k }}: {{ $msg }}</li>
                            @endforeach
                            @endif
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Variation Attributes') }}</label>
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            @foreach($pfAttrData ?? [] as $a)
                            <label class="form-check form-check-inline">
                                <input class="form-check-input used-attr-checkbox" type="checkbox" name="used_attributes[]" value="{{ $a['slug'] }}" @if(in_array($a['slug'],$pfUsedAttributes ?? [])) checked @endif>
                                <span class="form-check-label">{{ $a['name'] }}</span>
                            </label>
                            @endforeach
                        </div>
                        <div class="admin-text-muted mb-3">{{ __('Select which attributes will be used to create variations. Unchecked attributes will not appear on each variation row.') }}</div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped" id="variations-table">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">{{ __('Active') }}</th>
                                    <th>{{ __('Attributes / Name') }}</th>
                                    <th class="d-none d-md-table-cell">{{ __('SKU') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th class="d-none d-lg-table-cell">{{ __('Sale') }}</th>
                                    <th class="d-none d-md-table-cell">{{ __('Stock') }}</th>
                                    <th class="text-center">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <button type="button" class="admin-btn admin-btn-small admin-btn-secondary" id="add-variation">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span class="d-none d-sm-inline">{{ __('Add Variation') }}</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- SEO Section -->
        <div class="admin-modern-card mb-4">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2" />
                        <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('SEO Settings') }}
                </h3>
            </div>
            <div class="admin-card-body">
                {{-- languages provided: $pfLanguages --}}
                <div class="lang-tabs-wrapper mt-2" data-lang-tabs-variant="primary">
                    <ul class="nav nav-tabs small flex-nowrap overflow-auto gap-2 lang-tabs px-1" role="tablist">
                        @foreach($languages as $i=>$lang)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link py-1 px-3 @if($lang->code===$activeLocale) active @endif" data-bs-toggle="tab" data-bs-target="#seo-tab-{{ $lang->code }}" type="button" role="tab">{{ strtoupper($lang->code) }}</button>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="tab-content border rounded-bottom p-3">
                    @foreach($languages as $i=>$lang)
                    <div class="tab-pane fade @if($lang->code===$activeLocale) show active @endif" id="seo-tab-{{ $lang->code }}" role="tabpanel">
                        <div class="admin-form-grid">
                            <div class="admin-form-group">
                                <label class="admin-form-label">{{ __('SEO Title') }}</label>
                                <input name="seo_title[{{ $lang->code }}]" value="{{ $pfLangMeta[$lang->code]['seo_title'] }}" class="admin-form-input" placeholder="{{ $pfLangMeta[$lang->code]['ph_seo_title'] }}">
                            </div>
                            <div class="admin-form-group">
                                <label class="admin-form-label">{{ __('SEO Keywords') }}</label>
                                <input name="seo_keywords[{{ $lang->code }}]" value="{{ $pfLangMeta[$lang->code]['seo_keywords'] }}" class="admin-form-input" placeholder="{{ $pfLangMeta[$lang->code]['ph_seo_keywords'] }}">
                            </div>
                            <div class="admin-form-group admin-form-group-wide">
                                <label class="admin-form-label d-flex justify-content-between align-items-center">
                                    <span>{{ __('SEO Description') }}</span>
                                    <button type="submit" form="product-form" formaction="{{ route('admin.products.ai.suggest') }}?target=seo&locale={{ $lang->code }}" formmethod="get" class="admin-btn admin-btn-small admin-btn-outline">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        {{ __('AI Generate') }}
                                    </button>
                                </label>
                                <textarea name="seo_description[{{ $lang->code }}]" class="admin-form-input js-ai-seo-description" rows="3" placeholder="{{ $pfLangMeta[$lang->code]['ph_seo_description'] }}">{{ $pfLangMeta[$lang->code]['seo_description'] }}</textarea>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-xl-4 col-lg-5">
        <!-- Media Section -->
        <div class="admin-modern-card mb-4">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" />
                        <circle cx="8.5" cy="8.5" r="1.5" stroke="currentColor" stroke-width="2" />
                        <path d="M21 15L16 10L5 21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('Media') }}
                </h3>
            </div>
            <div class="admin-card-body">
                <div class="admin-form-group">
                    <label class="admin-form-label">{{ __('Main Image') }}</label>
                    <div class="input-group">
                        <input name="main_image" value="{{ old('main_image',$m?->main_image) }}" class="admin-form-input"
                            placeholder="/storage/...">
                        <button type="button" class="admin-btn admin-btn-outline" data-open-media="main_image">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M22 19C22 19.5304 21.7893 20.0391 21.4142 20.4142C21.0391 20.7893 20.5304 21 20 21H4C3.46957 21 2.96086 20.7893 2.58579 20.4142C2.21071 20.0391 2 19.5304 2 19V5C2 4.46957 2.21071 3.96086 2.58579 3.58579C2.96086 3.21071 3.46957 3 4 3H15L22 10V19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M15 3V10H22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div id="product-variation-meta" data-existing='{{ e(json_encode($pfClientVariations, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)) }}'
                    data-attributes='{{ e(json_encode($pfAttrData, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)) }}'
                    data-used='{{ e(json_encode($pfUsedAttributes, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)) }}'></div>

                <div class="admin-form-group">
                    <label class="admin-form-label">{{ __('Gallery') }}</label>
                    <div id="gallery-manager" class="d-flex flex-wrap gap-2 mb-2"></div>
                    <button type="button" class="admin-btn admin-btn-small admin-btn-outline" id="add-gallery-image">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        {{ __('Add Image') }}
                    </button>
                    <input type="hidden" name="gallery" id="gallery-input"
                        value="{{ old('gallery', json_encode($m?->gallery ?? [])) }}">
                    <div class="admin-text-muted mt-2">
                        {{ __('You can leave it empty. Click Add Image multiple times to append more.') }}
                    </div>
                </div>

                <!-- Digital Download Settings (only shown for digital products) -->
                <div class="admin-form-group digital-only envato-hidden">
                    <h6 class="admin-card-title mb-2">{{ __('Digital Download') }}</h6>
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Download File (path)') }}</label>
                        <div class="input-group">
                            <input name="download_file" value="{{ old('download_file', $m?->download_file) }}"
                                class="admin-form-input" placeholder="/storage/downloads/file.zip">
                            <button type="button" class="admin-btn admin-btn-outline" data-open-media="download_file">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M22 19C22 19.5304 21.7893 20.0391 21.4142 20.4142C21.0391 20.7893 20.5304 21 20 21H4C3.46957 21 2.96086 20.7893 2.58579 20.4142C2.21071 20.0391 2 19.5304 2 19V5C2 4.46957 2.21071 3.96086 2.58579 3.58579C2.96086 3.21071 3.46957 3 4 3H15L22 10V19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M15 3V10H22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                        </div>
                        <div class="admin-text-muted">
                            {{ __('If you upload a file ensure it is ZIP or PDF. Provide a storage path.') }}
                        </div>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">{{ __('Download URL') }}</label>
                        <input name="download_url" value="{{ old('download_url', $m?->download_url) }}"
                            class="admin-form-input" placeholder="https://...">
                    </div>
                    <div class="form-check admin-form-group">
                        <input class="form-check-input" type="checkbox" name="has_serials" value="1"
                            @checked(old('has_serials', $m?->has_serials ?? false)) id="has_serials_checkbox">
                        <label
                            class="form-check-label">{{ __('This product uses serial codes (one per sale)') }}</label>
                    </div>
                    <div class="admin-form-group serials-only {{ $pfHasSerials ? '' : 'envato-hidden' }}">
                        <label class="admin-form-label">{{ __('Serials (one per line)') }}</label>
                        <textarea name="serials" class="admin-form-input" rows="4"
                            placeholder="SERIAL1\nSERIAL2">{{ old('serials') }}</textarea>
                        <div class="admin-text-muted">
                            {{ __('Add serial codes here to seed stock; one code per line. When sold each serial is marked sold and won\'t be reused.') }}
                        </div>
                    </div>
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label">{{ __('Tags') }}</label>
                    <select name="tag_ids[]" multiple class="admin-form-input" size="6">
                        @foreach($tags as $t)
                        <option value="{{ $t->id }}" @selected(in_array($t->id,
                            old('tag_ids',$m?->tags->pluck('id')->toArray() ?? [])))>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Product Flags -->
        <div class="admin-modern-card mb-4">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 15S1 12 1 8S4 1 4 1H20C20 1 23 4 23 8S20 15 20 15H4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M4 22V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('Product Flags') }}
                </h3>
            </div>
            <div class="admin-card-body">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="is_featured" value="1"
                        @checked(old('is_featured',$m?->is_featured ?? false))>
                    <label class="form-check-label">{{ __('Featured Product') }}</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="is_best_seller" value="1"
                        @checked(old('is_best_seller',$m?->is_best_seller ?? false))>
                    <label class="form-check-label">{{ __('Best Seller') }}</label>
                </div>
                @if($pfShowActive)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="active" value="1"
                        @checked(old('active',$m?->active ?? true))>
                    <label class="form-check-label">{{ __('Active') }}</label>
                </div>
                @else
                <input type="hidden" name="active" value="0">
                @endif
            </div>
        </div>

        <!-- Inventory Management -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-boxes me-2"></i>
                    {{ __('Inventory') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="manage_stock" value="1"
                        @checked(old('manage_stock',$m?->manage_stock ?? false))>
                    <label class="form-check-label">{{ __('Manage Stock') }}</label>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small">{{ __('Stock Qty') }}</label>
                        <input type="number" name="stock_qty" value="{{ old('stock_qty',$m?->stock_qty ?? 0) }}"
                            class="form-control">
                    </div>
                    <div class="col-6">
                        <label class="form-label small">{{ __('Reserved') }}</label>
                        <input type="number" name="reserved_qty"
                            value="{{ old('reserved_qty',$m?->reserved_qty ?? 0) }}" class="form-control">
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small">{{ __('Refund Days') }}</label>
                        <input type="number" min="0" name="refund_days"
                            value="{{ old('refund_days',$m?->refund_days ?? 0) }}" class="form-control">
                        <div class="form-text small">
                            {{ __('Number of days customers can request a refund; 0 = no refunds') }}
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="form-label small">{{ __('Weight') }}</label>
                        <input type="number" step="0.01" name="weight" value="{{ old('weight',$m?->weight) }}"
                            class="form-control">
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-4">
                        <label class="form-label small">{{ __('Length') }}</label>
                        <input type="number" step="0.01" name="length" value="{{ old('length',$m?->length) }}"
                            class="form-control">
                    </div>
                    <div class="col-4">
                        <label class="form-label small">{{ __('Width') }}</label>
                        <input type="number" step="0.01" name="width" value="{{ old('width',$m?->width) }}"
                            class="form-control">
                    </div>
                    <div class="col-4">
                        <label class="form-label small">{{ __('Height') }}</label>
                        <input type="number" step="0.01" name="height" value="{{ old('height',$m?->height) }}"
                            class="form-control">
                    </div>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="backorder" value="1"
                        @checked(old('backorder',$m?->backorder ?? false))>
                    <label class="form-check-label">{{ __('Allow Backorder') }}</label>
                </div>
            </div>
        </div>

        <!-- Variation Attributes Reference -->
        <div class="card modern-card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    {{ __('Variation Attributes') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="small text-muted">
                    {{ __('Use attribute dropdown inside each variation row for variable products.') }}
                </div>
            </div>
        </div>
    </div>
</div>