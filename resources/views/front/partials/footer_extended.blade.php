<footer class="footer-new">

  @if($sections['support_bar'])
  <div class="footer-support-bar">
    <div class="support-item"><strong>{{ $supportHeading }}</strong><span>{{ $supportSub }}</span></div>
    <div class="support-channel"><span class="icon">🛈</span><span class="label">{{ $helpCenterLabel }}</span><a href="#">help.{{ config('app.name') }}.com</a></div>
    <div class="support-channel"><span class="icon">✉️</span><span class="label">{{ $emailSupportLabel }}</span><a href="mailto:{{ config('mail.from.address','support@example.com') }}">{{ config('mail.from.address','support@example.com') }}</a></div>
    <div class="support-channel"><span class="icon">📞</span><span class="label">{{ $phoneSupportLabel }}</span><span>{{ config('app.phone','16358') }}</span></div>
  </div>
  @endif

  <div class="footer-apps-social">
    @if($sections['apps'])
    <div class="apps">
      <span class="apps-title">{{ $appsHeading }}</span>
      <div class="app-badges-row">
        @foreach($orderedApps as $platform=>$app)
        <a class="app-badge" href="{{ $app['url'] }}" aria-label="{{ ucfirst($platform) }} Store">
          @if(!empty($app['image']))
          <img src="{{ \App\Helpers\GlobalHelper::storageImageUrl($app['image']) }}" alt="{{ ucfirst($platform) }}" class="app-badge-img">
          @else
          {{ ucfirst($platform) }}
          @endif
        </a>
        @endforeach
      </div>
    </div>
    @endif
    @if($sections['social'])
    <div class="social-connect">
      <span class="social-title">{{ $socialHeading }}</span>
      <div class="social-icons">
        @forelse($socialLinks as $link)
        <a href="{{ $link->url }}" aria-label="{{ $link->label ?? ucfirst($link->platform) }}" target="_blank" rel="noopener" class="soc soc-{{ $link->platform }}">
          @if($link->icon)<i class="{{ $link->icon }}" aria-hidden="true"></i>@else {{ strtoupper(substr($link->label ?? $link->platform,0,2)) }} @endif
        </a>
        @empty
        <span class="no-social text-muted">{{ __('No social links') }}</span>
        @endforelse
      </div>
    </div>
    @endif
  </div>

  <div class="footer-legal footer-legal-row">
    <div class="copyright legal-center">&copy; {{ date('Y') }} {{ config('app.name') }}. {{ $rightsLine }}</div>
    @if($sections['payments'])
    <div class="payments legal-right" aria-label="Payment Methods">
      @foreach(array_slice($paymentList,0,6) as $pm)
      <span class="pm">{{ strtoupper($pm) }}</span>
      @endforeach
    </div>
    @endif
  </div>
</footer>