<h1 align="center">Multi‑Vendor Commerce & PWA Platform</h1>

<p align="center">
<img src="docs/screenshots/cover.png" alt="Cover" width="600" />
</p>

<p align="center">
<img alt="Build" src="https://github.com/laravel/framework/workflows/tests/badge.svg" />
<img alt="License" src="https://img.shields.io/badge/license-MIT-green" />
<img alt="Queries" src="https://img.shields.io/badge/queries_index-≤40-brightgreen" />
<img alt="PWA" src="https://img.shields.io/badge/PWA-enabled-blue" />
</p>

## ✨ Overview
Laravel 12 multi-vendor platform with focus on performance (query limiting), scalability, and modern features (PWA + Push + Background Sync). Contains performance tests that protect against regression, initial setup for extension (Observers / Services) and roadmap for distinctive features (performance dashboard, Webhooks, Pricing Rules).

## 🔗 Core Documentation
| Topic | File |
|-------|------|
| Features List | `docs/FEATURES.md` |
| Extension & Architecture | `docs/DEVELOPERS.md` |
| Security | `docs/SECURITY.md` |
| Accessibility | `docs/ACCESSIBILITY.md` |
| Asset Licenses | `LICENSE-ASSETS.md` |
| Changelog | `docs/CHANGELOG.md` |

## 🚀 Key Features
- Query Baselines (Index ≤40, Category ≤50, Tag ≤55) with automated tests.
- Denormalized Review Aggregates to reduce load.
- PWA Service Worker (Offline, Runtime & API caches, Background Sync).
- Web Push (subscriptions + `push:send` command).
- Wishlist / Compare / Currency Conversion.
- Services & Observers architecture (extensible).
- Tests (performance + commands + Push subscriptions API).

### 🧭 Feature Matrix
| Domain | Included | Notes |
|--------|----------|-------|
| Multi‑Vendor | ✅ | Withdrawals, commissions, vendor products |
| Catalog | ✅ | Products, categories (multilingual), attributes (extensible) |
| Blog | ✅ | Posts & categories multilingual slugs + fallback |
| Translations | ✅ | JSON based + automatic per-locale slug generation |
| Performance Dashboard | ✅ | Snapshot tiles (manual refresh) |
| Activity Center | ✅ | Vue enhancement / degrades to static list |
| PWA | ✅ | Offline, background sync allowlist |
| Web Push | ✅ | VAPID + command dispatch |
| SEO | ✅ | Slug & meta per locale (categories/posts) |
| Notifications | ✅ | Polling + modular JS |
| Theming | 🚧 | Tokens documented (`docs/DESIGN_GUIDE.md`) |
| Webhooks | Roadmap | HMAC signed events |
| Pricing Rules | Roadmap | Engine skeleton documented |

### 🔍 Use Cases
1. Marketplace MVP: Launch multi-vendor catalog + blog + basic payouts fast.
2. Localized Storefront: Serve content in multiple languages with automatic slug handling.
3. Performance Audited Backend: Enforce query baselines via tests.
4. Content + Commerce Hybrid: Blog + catalog share translation & SEO infrastructure.
5. Extensible Platform: Add gateways, pricing rules, webhooks using documented extension points.

### 🖼 Screenshots (Sample)
| Area | File |
|------|------|
| Dashboard | `docs/screenshots/cover.png` |
| Activity Center | (add) `docs/screenshots/activity.png` |
| Vendor Withdrawals | (add) `docs/screenshots/withdrawals.png` |
| Performance Tiles | (add) `docs/screenshots/performance.png` |

Add missing screenshots before packaging (PNG ≤ 300KB each).

## 🛠 Installation & Setup

### Requirements
- PHP 8.3+
- Composer
- Node.js 18+ & npm / pnpm
- SQLite / MySQL / Postgres (default `.env` uses SQLite sample)

### Quick Install
```bash
# Clone & Dependencies
composer install
npm install

# Environment
cp .env.example .env
php artisan key:generate
```

### Environment Configuration
Adjust in `.env`:
```env
APP_NAME="Marketplace"
APP_ENV=local
APP_URL=http://localhost:8000
QUEUE_CONNECTION=database
CACHE_DRIVER=file   # or redis
SESSION_DRIVER=file # or redis
```

### Database Setup
SQLite (default):
```bash
touch database/database.sqlite
```
Or configure MySQL:
```env
DB_CONNECTION=mysql
DB_DATABASE=marketplace
DB_USERNAME=root
DB_PASSWORD=secret
```

### Migration & Seeding
```bash
php artisan migrate --seed
```

### Assets Build & Serve
Development:
```bash
npm run dev
php artisan serve
```
Production:
```bash
npm run build
```

### PWA & Push Setup
Generate VAPID keys:
```bash
php artisan push:keys
```
Add to `.env`:
```env
VAPID_PUBLIC_KEY=...
VAPID_PRIVATE_KEY=...
```

### Queue & Scheduler (Optional)
Queue worker:
```bash
php artisan queue:work --tries=3
```
Scheduler cron:
```bash
* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
```

## 🧪 Tests
Full test run:
```
php artisan test
```
Performance measurement (shows query counter):
```
php artisan test --filter=ProductCatalogQueryCountTest
```

## 📦 Production Build
```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Redis Configuration (Optional)
```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### Build Integrity & Lint
```bash
./vendor/bin/pint
php artisan test
```

### Deployment Checklist
- Run migrations
- Cache config/routes/views
- Queue worker active
- Set APP_ENV=production & APP_DEBUG=false
- Configure HTTPS & security headers (CSP, HSTS)

### Troubleshooting
- Missing JS/CSS: run `npm run build` again
- Queue not processing: ensure `queue:work` running and DB queue table migrated
- 419 errors: check session driver & APP_URL

## 🧩 Roadmap
- Performance Dashboard (snapshots: queries, cache hit %, slow queries)
- Webhook Layer (HMAC signature + retries)
- Pricing Rules Engine (bulk / conditional)
- Dark Mode + Design Tokens (CSS variables)
	- (Documentation ready: merged in `docs/CUSTOMIZATION_AND_THEMING.md`)
- No-JS graceful cart & basic filtering fallback
- Accessibility: skip link, unified focus ring, aria-live for cart

## ♿ Accessibility (Status)
See `docs/ACCESSIBILITY.md` (checklist + remaining tasks). Contrast and focus states will be improved and skip link will be added in the next version.

## 🔐 Security
Summary in `docs/SECURITY.md` includes: CSRF, Sanitization, CSP plan, Rate Limiting (planned). Report vulnerabilities by creating a private ticket or email (put your email here).

## 📁 Asset Licenses
Review `LICENSE-ASSETS.md` and fill any gaps before commercial release (fonts, images, icons). Any asset without clear license should be removed.

## 🤝 Contributing
Pull Requests for planned features are welcome (see Roadmap). Keep tests green and cover new features with at least one test.

## 📄 License
MIT (see default Laravel LICENSE file). Third-party assets according to their mentioned licenses.

---
Notes: This file is adapted to meet commercial presentation requirements (CodeCanyon) by highlighting value, linking to documentation, and stating a clear roadmap.
