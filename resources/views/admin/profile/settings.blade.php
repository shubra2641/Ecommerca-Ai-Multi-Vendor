@extends('layouts.admin')

@section('title', __('System Settings'))

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
                        <h1 class="admin-order-title">{{ __('System Settings') }}</h1>
                        <p class="admin-order-subtitle">{{ __('Manage system configuration and preferences') }}</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
            </div>
        </div>

        <div class="row">
            <!-- General Settings -->
            <div class="col-lg-8">
                <div class="admin-modern-card">
                    <div class="admin-card-header">
                        <h2 class="admin-card-title">
                            <i class="fas fa-cog"></i>
                            {{ __('General Settings') }}
                        </h2>
                    </div>
                    <div class="admin-card-body">
                        <form action="{{ route('admin.settings.update') }}" method="POST" class="settings-form admin-settings-form"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="admin-form-grid">
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('Site Name') }}</label>
                                    <input type="text" name="site_name" class="admin-form-input @error('site_name') is-invalid @enderror" value="{{ old('site_name', $setting->site_name ?? '') }}">
                                    @error('site_name')
                                    <div class="admin-text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('Logo') }}</label>
                                    <input type="file" name="logo" class="admin-form-input">
                                    @if(!empty($setting->logo))
                                    <div class="admin-mt-half">
                                        <img src="{{ asset('storage/'.$setting->logo) }}" alt="Logo" class="img-thumbnail">
                                        <div class="admin-text-muted small">{{ __('Current logo will be replaced when you upload a new one.') }}</div>
                                    </div>
                                    @endif
                                    @error('logo')
                                    <div class="admin-text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="admin-form-group">
                                <label class="admin-form-label">{{ __('SEO Description') }}</label>
                                <textarea name="seo_description" rows="3" class="admin-form-input @error('seo_description') is-invalid @enderror">{{ old('seo_description', $setting->seo_description ?? '') }}</textarea>
                                @error('seo_description')
                                <div class="admin-text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="admin-form-grid">
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('Contact Email') }}</label>
                                    <input type="email" name="contact_email" class="admin-form-input @error('contact_email') is-invalid @enderror" value="{{ old('contact_email', $setting->contact_email ?? '') }}">
                                    @error('contact_email')
                                    <div class="admin-text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('Contact Phone') }}</label>
                                    <input type="text" name="contact_phone" class="admin-form-input @error('contact_phone') is-invalid @enderror" value="{{ old('contact_phone', $setting->contact_phone ?? '') }}">
                                    @error('contact_phone')
                                    <div class="admin-text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- Legacy social media fields removed. Manage links via Social Links section. -->
                            <div class="admin-form-group">
                                <label class="admin-form-label">{{ __('Custom CSS') }}</label>
                                <textarea name="custom_css" rows="4" class="admin-form-input @error('custom_css') is-invalid @enderror">{{ old('custom_css', $setting->custom_css ?? '') }}</textarea>
                                @error('custom_css')
                                <div class="admin-text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="admin-form-group">
                                <label class="admin-form-label">{{ __('Custom JS') }}</label>
                                <textarea name="custom_js" rows="4" class="admin-form-input @error('custom_js') is-invalid @enderror">{{ old('custom_js', $setting->custom_js ?? '') }}</textarea>
                                @error('custom_js')
                                <div class="admin-text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="admin-form-group">
                                <label class="admin-form-label">{{ __('Footer Rights Text') }}</label>
                                <input type="text" name="rights" maxlength="255" class="admin-form-input @error('rights') is-invalid @enderror" value="{{ old('rights', $setting->rights ?? '') }}" placeholder="© {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}">
                                <div class="admin-text-muted small">{{ __('Shown in the site footer. Basic text only.') }}</div>
                                @error('rights')
                                <div class="admin-text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="admin-form-grid">
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('Timezone') }}</label>
                                    <select name="app_timezone" class="admin-form-input @error('app_timezone') is-invalid @enderror" required>
                                        <option value="UTC" {{ config('app.timezone') === 'UTC' ? 'selected' : '' }}>UTC</option>
                                        <option value="America/New_York" {{ config('app.timezone') === 'America/New_York' ? 'selected' : '' }}>Eastern Time (UTC-5)</option>
                                        <option value="America/Chicago" {{ config('app.timezone') === 'America/Chicago' ? 'selected' : '' }}>Central Time (UTC-6)</option>
                                        <option value="America/Los_Angeles" {{ config('app.timezone') === 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time (UTC-8)</option>
                                        <option value="Europe/London" {{ config('app.timezone') === 'Europe/London' ? 'selected' : '' }}>London (UTC+0)</option>
                                        <option value="Europe/Paris" {{ config('app.timezone') === 'Europe/Paris' ? 'selected' : '' }}>Paris (UTC+1)</option>
                                        <option value="Asia/Dubai" {{ config('app.timezone') === 'Asia/Dubai' ? 'selected' : '' }}>Dubai (UTC+4)</option>
                                        <option value="Asia/Riyadh" {{ config('app.timezone') === 'Asia/Riyadh' ? 'selected' : '' }}>Riyadh (UTC+3)</option>
                                        <option value="Asia/Cairo" {{ config('app.timezone') === 'Asia/Cairo' ? 'selected' : '' }}>Cairo (UTC+2)</option>
                                    </select>
                                    @error('app_timezone')
                                    <div class="admin-text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="admin-form-group">
                                    <label class="admin-form-label">{{ __('Default Language') }}</label>
                                    <select name="app_locale" class="admin-form-input @error('app_locale') is-invalid @enderror" required>
                                        <option value="en" {{ config('app.locale') === 'en' ? 'selected' : '' }}>{{ __('English') }}</option>
                                        <option value="ar" {{ config('app.locale') === 'ar' ? 'selected' : '' }}>{{ __('Arabic') }}</option>
                                    </select>
                                    @error('app_locale')
                                    <div class="admin-text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="admin-form-group">
                                <label class="admin-form-label">{{ __('Font Family') }}</label>
                                <select name="font_family" class="admin-form-input js-preview-font @error('font_family') is-invalid @enderror">
                                    @foreach(($profileAvailableFonts ?? []) as $font)
                                    <option value="{{ $font }}" {{ old('font_family', $setting->font_family ?? 'Inter') === $font ? 'selected' : '' }}>{{ $font }}@if($font==='Inter') ({{ __('Default') }})@endif</option>
                                    @endforeach
                                </select>
                                <div class="admin-text-muted small">{{ __('Only locally bundled fonts are listed to ensure CSP & SRI compliance.') }}</div>
                                <input type="hidden" id="current_font_loaded" value="{{ old('font_family', $setting->font_family ?? 'Inter') }}">
                                @error('font_family')
                                <div class="admin-text-danger">{{ $message }}</div>
                                @enderror
                            </div>


                            <div class="row">
                                <div class="col-md-12">
                                    <div class="font-preview-container envato-hidden">
                                        <label class="form-label">{{ __('Font Preview') }}</label>
                                        <div class="font-preview-text" id="fontPreview">
                                            <p data-sample="label" class="mb-2 fs-16 fw-600">
                                                {{ $setting->font_family ?? 'Inter' }} - {{ __('Font Preview') }}
                                            </p>
                                            <p data-sample="latin" class="mb-1 fs-14">The quick brown fox
                                                jumps over the lazy dog 1234567890 !?&amp;*</p>
                                            <p data-sample="arabic" class="mb-0 fs-14">نص عربي للتجربة يظهر
                                                تنسيق الخط واختبار الحروف الموسعة</p>
                                        </div>
                                        <small
                                            class="text-muted d-block mt-2">{{ __('Preview only. Click Save Settings to apply site-wide.') }}</small>
                                    </div>
                                </div>
                            </div>

                            <hr />
                            <h4 class="mt-4">{{ __('AI Assistant') }}</h4>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group form-check mt-2">
                                        <input type="hidden" name="ai_enabled" value="0">
                                        <input type="checkbox" id="ai_enabled" name="ai_enabled" value="1" class="form-check-input" {{ old('ai_enabled', $setting->ai_enabled ?? false) ? 'checked' : '' }}>
                                        <label for="ai_enabled" class="form-check-label">{{ __('Enable AI') }}</label>
                                        <div class="form-text small">{{ __('Toggle product description & SEO generation.') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="ai_provider" class="form-label">{{ __('Provider') }}</label>
                                        <select id="ai_provider" name="ai_provider" class="form-select">
                                            <option value="" @selected(old('ai_provider',$setting->ai_provider ?? '')==='')>{{ __('Select') }}</option>
                                            <option value="openai" @selected(old('ai_provider',$setting->ai_provider ?? '')==='openai')>OpenAI</option>
                                            <option value="grok" @selected(old('ai_provider',$setting->ai_provider ?? '')==='grok')>Grok</option>
                                            <option value="gemini" @selected(old('ai_provider',$setting->ai_provider ?? '')==='gemini')>Gemini</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ai_openai_api_key" class="form-label">{{ __('AI API Key') }}</label>
                                        <input type="text" id="ai_openai_api_key" name="ai_openai_api_key" class="form-control" value="{{ old('ai_openai_api_key', $setting->ai_openai_api_key ? '••••••••' : '') }}" placeholder="sk-... or grok-...">
                                        <div class="form-text small">{{ __('Stored encrypted. Never expose to vendors.') }}</div>
                                    </div>
                                </div>
                            </div>
                            <hr />
                            <h4 class="mt-3">{{ __('Withdrawal Settings') }}</h4>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="min_withdrawal_amount" class="form-label">{{ __('Minimum Withdrawal Amount') }}</label>
                                        <input type="number" step="0.01" id="min_withdrawal_amount" name="min_withdrawal_amount" class="form-control" value="{{ old('min_withdrawal_amount', $setting->min_withdrawal_amount ?? 10) }}">
                                        <small class="form-text text-muted">{{ __('Minimum amount vendors can request for withdrawal.') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="withdrawal_gateways" class="form-label">{{ __('Withdrawal Gateways (one per line)') }}</label>

                                        <textarea id="withdrawal_gateways" name="withdrawal_gateways" rows="3" class="form-control">{{ implode("\n", $setting->withdrawal_gateways ?? []) }} </textarea>
                                        <div class="form-text">{{ __('Enter each method name on a separate line (e.g. Bank Transfer, PayPal, Wise). It will be saved automatically.') }}</div>
                                        <small class="form-text text-muted">{{ __('List simple gateway titles vendors can choose from.') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="hidden" name="withdrawal_commission_enabled" value="0">
                                        <div class="form-check">
                                            <input type="checkbox" id="withdrawal_commission_enabled" name="withdrawal_commission_enabled" value="1" class="form-check-input" {{ old('withdrawal_commission_enabled', $setting->withdrawal_commission_enabled ?? false) ? 'checked' : '' }}>
                                            <label for="withdrawal_commission_enabled" class="form-check-label">{{ __('Enable Withdrawal Commission') }}</label>
                                        </div>
                                        <div class="mt-2">
                                            <label for="withdrawal_commission_rate" class="form-label">{{ __('Commission Rate (%)') }}</label>
                                            <input type="number" step="0.01" id="withdrawal_commission_rate" name="withdrawal_commission_rate" class="form-control" value="{{ old('withdrawal_commission_rate', $setting->withdrawal_commission_rate ?? 0) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr />
                            <h4 class="mt-4">{{ __('Sales Commission') }}</h4>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="commission_mode" class="form-label">{{ __('Commission Mode') }}</label>
                                        <select id="commission_mode" name="commission_mode" class="form-select">
                                            <option value="flat" {{ old('commission_mode', $setting->commission_mode ?? 'flat')==='flat' ? 'selected' : '' }}>{{ __('Flat (Global Rate)') }}</option>
                                            <option value="category" {{ old('commission_mode', $setting->commission_mode ?? 'flat')==='category' ? 'selected' : '' }}>{{ __('Per Category') }}</option>
                                        </select>
                                        <div class="form-text small">{{ __('Choose how vendor commission is determined.') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="commission_flat_rate" class="form-label">{{ __('Global Commission Rate (%)') }}</label>
                                        <input type="number" step="0.01" id="commission_flat_rate" name="commission_flat_rate" class="form-control" value="{{ old('commission_flat_rate', $setting->commission_flat_rate ?? '') }}">
                                        <div class="form-text small">{{ __('Used when mode is Flat or when category has no override.') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <div class="alert alert-info py-2 px-3 w-100 mb-0 small">
                                        <strong>{{ __('Tip:') }}</strong> {{ __('Set per-category rate in category form when mode = Per Category.') }}
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group form-check">
                                        <input type="hidden" name="auto_publish_reviews" value="0">
                                        <input type="checkbox" id="auto_publish_reviews" name="auto_publish_reviews" value="1"
                                            class="form-check-input"
                                            {{ old('auto_publish_reviews', $setting->auto_publish_reviews ?? 0) ? 'checked' : '' }}>
                                        <label for="auto_publish_reviews"
                                            class="form-check-label">{{ __('Auto-publish product reviews') }}</label>
                                        <small
                                            class="form-text text-muted">{{ __('If enabled, reviews submitted by authenticated users will be published immediately. Otherwise admin approval is required.') }}</small>
                                    </div>
                                </div>
                            </div>

                            <hr />
                            <h4 class="mt-4">{{ __('External Payment Settings') }}</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-check">
                                        <input type="hidden" name="enable_external_payment_redirect" value="0">
                                        <input type="checkbox" id="enable_external_payment_redirect" name="enable_external_payment_redirect" value="1"
                                            class="form-check-input"
                                            {{ old('enable_external_payment_redirect', $setting->enable_external_payment_redirect ?? false) ? 'checked' : '' }}>
                                        <label for="enable_external_payment_redirect"
                                            class="form-check-label">{{ __('Enable External Payment Redirect') }}</label>
                                        <small
                                            class="form-text text-muted">{{ __('When enabled, customers will be redirected directly to external payment gateways instead of using the internal payment handler.') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="alert alert-info py-2 px-3 w-100 mb-0 small">
                                        <strong>{{ __('Note:') }}</strong> {{ __('This setting affects how payment gateways handle customer redirections. Enable for direct gateway integration.') }}
                                    </div>
                                </div>
                            </div>

                            <div class="admin-card-footer">
                                <div class="admin-flex-end">
                                    <button type="submit" class="admin-btn admin-btn-primary">
                                        <i class="fas fa-save"></i>
                                        {{ __('Save Settings') }}
                                    </button>
                                    <button type="button" class="admin-btn admin-btn-secondary js-reset-form" data-action="reset-settings-form">
                                        <i class="fas fa-sync-alt"></i>
                                        {{ __('Reset') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- System Information -->
            <div class="col-lg-4">
                <div class="admin-modern-card">
                    <div class="admin-card-header">
                        <h2 class="admin-card-title">
                            <i class="fas fa-info-circle"></i>
                            {{ __('System Information') }}
                        </h2>
                    </div>
                    <div class="admin-card-body">
                        <div class="info-list">
                            <div class="info-item d-flex justify-content-between align-items-center py-2">
                                <span class="fw-bold">{{ __('Laravel Version') }}</span>
                                <span class="badge bg-primary">{{ app()->version() }}</span>
                            </div>
                            <div class="info-item d-flex justify-content-between align-items-center py-2">
                                <span class="fw-bold">{{ __('PHP Version') }}</span>
                                <span class="badge bg-info">{{ PHP_VERSION }}</span>
                            </div>
                            <div class="info-item d-flex justify-content-between align-items-center py-2">
                                <span class="fw-bold">{{ __('Environment') }}</span>
                                <span class="badge bg-{{ app()->environment() === 'production' ? 'success' : 'warning' }}">
                                    {{ ucfirst(app()->environment()) }}
                                </span>
                            </div>
                            <div class="info-item d-flex justify-content-between align-items-center py-2">
                                <span class="fw-bold">{{ __('Debug Mode') }}</span>
                                <span class="badge bg-{{ config('app.debug') ? 'danger' : 'success' }}">
                                    {{ config('app.debug') ? __('Enabled') : __('Disabled') }}
                                </span>
                            </div>
                            <div class="info-item d-flex justify-content-between align-items-center py-2">
                                <span class="fw-bold">{{ __('Current Language') }}</span>
                                <span class="badge bg-secondary">{{ app()->getLocale() }}</span>
                            </div>
                            <div class="info-item d-flex justify-content-between align-items-center py-2">
                                <span class="fw-bold">{{ __('Total Users') }}</span>
                                <span class="badge bg-dark">{{ App\Models\User::count() }}</span>
                            </div>
                            <div class="info-item d-flex justify-content-between align-items-center py-2">
                                <span class="fw-bold">{{ __('Total Languages') }}</span>
                                <span class="badge bg-secondary">{{ App\Models\Language::count() }}</span>
                            </div>
                            <div class="info-item d-flex justify-content-between align-items-center py-2">
                                <span class="fw-bold">{{ __('Total Currencies') }}</span>
                                <span class="badge bg-warning">{{ App\Models\Currency::count() }}</span>
                            </div>
                            <div class="info-item d-flex justify-content-between align-items-center py-2">
                                <span class="fw-bold">{{ __('Server Time') }}</span>
                                <span class="text-muted" id="server-time">{{ now()->format('Y-m-d H:i:s') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Maintenance -->
                <div class="admin-modern-card">
                    <div class="admin-card-header">
                        <h2 class="admin-card-title">
                            <i class="fas fa-cog"></i>
                            {{ __('System Maintenance') }}
                        </h2>
                    </div>
                    <div class="admin-card-body">
                        <div class="admin-actions-grid">
                            <form action="{{ route('admin.cache.clear') }}" method="POST">
                                @csrf
                                <button type="submit" class="admin-btn admin-btn-warning admin-btn-block">
                                    <i class="fas fa-trash-alt"></i>
                                    {{ __('Clear Cache') }}
                                </button>
                            </form>

                            <form action="{{ route('admin.logs.clear') }}" method="POST">
                                @csrf
                                <button type="submit" class="admin-btn admin-btn-secondary admin-btn-block">
                                    <i class="fas fa-file-alt"></i>
                                    {{ __('Clear Logs') }}
                                </button>
                            </form>

                            <form action="{{ route('admin.optimize') }}" method="POST">
                                @csrf
                                <button type="submit" class="admin-btn admin-btn-success admin-btn-block">
                                    <i class="fas fa-bolt"></i>
                                    {{ __('Optimize System') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection