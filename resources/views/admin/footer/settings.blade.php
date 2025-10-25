@extends('layouts.admin')

@section('title', __('Footer Settings'))

@section('content')
<section class="admin-order-details-modern">
    <div class="admin-order-wrapper">
        <!-- Header -->
        <div class="admin-order-header">
            <div class="header-left">
                <div class="admin-header-content">
                    <div class="admin-header-icon">
                        <i class="fas fa-cog"></i>
                    </div>
                    <div class="admin-header-text">
                        <h1 class="admin-order-title">{{ $footerSettingsTitle ?? __('Footer Settings') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Configure footer content, sections, and display options') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.footer-settings.update') }}" enctype="multipart/form-data" class="admin-modern-card">
            @csrf
            @method('PUT')

            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <i class="fas fa-cog"></i>
                    {{ __('Footer Configuration') }}
                </h3>
            </div>

            <div class="admin-card-body">
                <!-- Sections Visibility -->
                <div class="admin-form-group">
                    <label class="admin-form-label">{{ __('Sections Visibility') }}</label>
                    <div class="admin-form-grid">
                        @foreach(['support_bar'=>'Support Bar','apps'=>'Apps / Downloads','social'=>'Social Links','pages'=>'Pages','payments'=>'Payments'] as $k=>$lbl)
                        <div class="admin-form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="sections[{{ $k }}]" value="1" @checked($sections[$k])>
                                <label class="form-check-label">
                                    <i class="fas fa-check-circle"></i>
                                    {{ __($lbl) }}
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Support Text (multilingual) -->
                <div class="admin-form-group">
                    <label class="admin-form-label">{{ __('Support Text (multilingual)') }}</label>
                    <div class="accordion" id="supportTexts">
                        @foreach($activeLanguages as $lang)
                        <div class="accordion-item mb-2">
                            <h2 class="accordion-header" id="heading-{{ $lang->code }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $lang->code }}">
                                    <i class="fas fa-globe"></i>
                                    {{ strtoupper($lang->code) }}
                                </button>
                            </h2>
                            <div id="collapse-{{ $lang->code }}" class="accordion-collapse collapse" data-bs-parent="#supportTexts">
                                <div class="accordion-body">
                                    <div class="admin-form-group">
                                        <label class="admin-form-label">{{ __('Support Heading') }}</label>
                                        <input name="footer_support_heading[{{ $lang->code }}]" value="{{ old('footer_support_heading.'.$lang->code, $setting->footer_support_heading[$lang->code] ?? '') }}" class="admin-form-input" maxlength="120">
                                    </div>
                                    <div class="admin-form-group">
                                        <label class="admin-form-label">{{ __('Support Subheading') }}</label>
                                        <input name="footer_support_subheading[{{ $lang->code }}]" value="{{ old('footer_support_subheading.'.$lang->code, $setting->footer_support_subheading[$lang->code] ?? '') }}" class="admin-form-input" maxlength="180">
                                    </div>
                                    <div class="admin-form-group">
                                        <label class="admin-form-label">{{ __('Rights Line') }}</label>
                                        <input name="rights_i18n[{{ $lang->code }}]" value="{{ old('rights_i18n.'.$lang->code, $setting->rights_i18n[$lang->code] ?? ($lang->is_default ? $setting->rights : '')) }}" class="admin-form-input" maxlength="255">
                                    </div>
                                    <div class="admin-form-grid">
                                        <div class="admin-form-group">
                                            <label class="admin-form-label">{{ __('Help Center Label') }}</label>
                                            <input name="footer_labels[help_center][{{ $lang->code }}]" value="{{ old('footer_labels.help_center.'.$lang->code, $setting->footer_labels['help_center'][$lang->code] ?? '') }}" class="admin-form-input" maxlength="120">
                                        </div>
                                        <div class="admin-form-group">
                                            <label class="admin-form-label">{{ __('Email Support Label') }}</label>
                                            <input name="footer_labels[email_support][{{ $lang->code }}]" value="{{ old('footer_labels.email_support.'.$lang->code, $setting->footer_labels['email_support'][$lang->code] ?? '') }}" class="admin-form-input" maxlength="120">
                                        </div>
                                        <div class="admin-form-group">
                                            <label class="admin-form-label">{{ __('Phone Support Label') }}</label>
                                            <input name="footer_labels[phone_support][{{ $lang->code }}]" value="{{ old('footer_labels.phone_support.'.$lang->code, $setting->footer_labels['phone_support'][$lang->code] ?? '') }}" class="admin-form-input" maxlength="120">
                                        </div>
                                        <div class="admin-form-group">
                                            <label class="admin-form-label">{{ __('Apps Section Heading') }}</label>
                                            <input name="footer_labels[apps_heading][{{ $lang->code }}]" value="{{ old('footer_labels.apps_heading.'.$lang->code, $setting->footer_labels['apps_heading'][$lang->code] ?? '') }}" class="admin-form-input" maxlength="120">
                                        </div>
                                        <div class="admin-form-group">
                                            <label class="admin-form-label">{{ __('Social Section Heading') }}</label>
                                            <input name="footer_labels[social_heading][{{ $lang->code }}]" value="{{ old('footer_labels.social_heading.'.$lang->code, $setting->footer_labels['social_heading'][$lang->code] ?? '') }}" class="admin-form-input" maxlength="120">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- App Download Badges -->
                <div class="admin-form-group">
                    <label class="admin-form-label">{{ __('App Download Badges') }}</label>
                    <div class="admin-form-grid">
                        @foreach($appLinks as $platform=>$link)
                        <div class="admin-modern-card">
                            <div class="admin-card-header">
                                <h4 class="admin-card-title">
                                    <i class="fas fa-mobile-alt"></i>
                                    {{ ucfirst($platform) }}
                                </h4>
                            </div>
                            <div class="admin-card-body">
                                <div class="admin-form-group">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="app_links[{{ $platform }}][enabled]" value="1" @checked($link['enabled'])>
                                        <label class="form-check-label">
                                            <i class="fas fa-check-circle"></i>
                                            {{ __('Enabled') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="admin-form-group">
                                    <label class="admin-form-label">URL</label>
                                    <input type="url" class="admin-form-input" name="app_links[{{ $platform }}][url]" value="{{ $link['url'] }}" placeholder="https://...">
                                </div>
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('Order') }}</label>
                                    <input type="number" class="admin-form-input" name="app_links[{{ $platform }}][order]" value="{{ $link['order'] }}" min="0" max="50">
                                </div>
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('Badge Image (max 180x54 suggested)') }}</label>
                                    @if(!empty($link['image']))
                                    <div class="admin-mb-half">
                                        <img src="{{ \App\Helpers\GlobalHelper::storageImageUrl($link['image']) }}" alt="badge" class="img-badge-thumb">
                                    </div>
                                    <input type="hidden" name="app_links[{{ $platform }}][existing_image]" value="{{ $link['image'] }}">
                                    @endif
                                    <input type="file" class="admin-form-input" name="app_links[{{ $platform }}][image]" accept="image/*">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Footer Pages -->
                <div class="admin-form-group">
                    <label class="admin-form-label">{{ __('Footer Pages') }}</label>
                    <div class="admin-text-muted">{{ __('Select pages to display (max 8). Order will follow selection order.') }}</div>
                    @if($pages->count() > 0)
                    <select name="footer_pages[]" class="admin-form-input" multiple size="8">
                        @foreach($pages as $p)
                        <option value="{{ $p->id }}" @selected(in_array($p->id, $setting->footer_pages ?? []))>{{ $footerPageTitles[$p->id] ?? ('#'.$p->id) }} @if(isset($p->identifier) && $p->identifier) ({{ $p->identifier }}) @endif</option>
                        @endforeach
                    </select>
                    @else
                    <div class="admin-empty-state">
                        <div class="admin-notification-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <p>{{ __('No pages available. Pages will be available once the pages system is set up.') }}</p>
                    </div>
                    @endif
                </div>

                <!-- Payment Methods -->
                <div class="admin-form-group">
                    <label class="admin-form-label">{{ __('Payment Methods (one per line, max 6 shown)') }}</label>
                    <textarea name="footer_payment_methods" class="admin-form-input" rows="3" placeholder="VISA\nMC\nPAYPAL">{{ implode("\n", $setting->footer_payment_methods ?? []) }}</textarea>
                </div>
            </div>

            <div class="admin-card-footer">
                <div class="admin-flex-end">
                    <button type="submit" class="admin-btn admin-btn-primary">
                        <i class="fas fa-save"></i>
                        {{ __('Save Footer Settings') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection