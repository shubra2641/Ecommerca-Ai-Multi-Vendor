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
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
                            <path d="M9 9H15V15H9V9Z" stroke="currentColor" stroke-width="2"/>
                            <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2"/>
                        </svg>
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
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
                        <path d="M9 9H15V15H9V9Z" stroke="currentColor" stroke-width="2"/>
                        <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2"/>
                    </svg>
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
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2"/>
                                    </svg>
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
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                        <line x1="2" y1="12" x2="22" y2="12" stroke="currentColor" stroke-width="2"/>
                                        <path d="M12 2C14.5013 4.73835 15.9228 8.29203 16 12C15.9228 15.708 14.5013 19.2616 12 22C9.49872 19.2616 8.07725 15.708 8 12C8.07725 8.29203 9.49872 4.73835 12 2Z" stroke="currentColor" stroke-width="2"/>
                                    </svg>
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
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
                                        <line x1="8" y1="21" x2="16" y2="21" stroke="currentColor" stroke-width="2"/>
                                        <line x1="12" y1="17" x2="12" y2="21" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                    {{ ucfirst($platform) }}
                                </h4>
                            </div>
                            <div class="admin-card-body">
                                <div class="admin-form-group">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="app_links[{{ $platform }}][enabled]" value="1" @checked($link['enabled'])>
                                        <label class="form-check-label">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2"/>
                                            </svg>
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
                                        <img src="{{ asset('storage/'.$link['image']) }}" alt="badge" class="img-badge-thumb">
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
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                <line x1="12" y1="8" x2="12" y2="12" stroke="currentColor" stroke-width="2"/>
                                <line x1="12" y1="16" x2="12.01" y2="16" stroke="currentColor" stroke-width="2"/>
                            </svg>
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
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19 21H5C3.89543 21 3 20.1046 3 19V5C3 3.89543 3.89543 3 5 3H16L21 8V19C21 20.1046 20.1046 21 19 21Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M17 21V13H7V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M7 3V8H15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        {{ __('Save Footer Settings') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection
