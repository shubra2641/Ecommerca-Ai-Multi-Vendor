<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use App\Services\AI\SimpleAIService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomepageSectionController extends Controller
{
    private const DEFAULT_SECTIONS = [
        // Main large sections
        [
            'key' => 'flash_sale',
            'title' => 'Flash Sale',
            'subtitle' => 'Limited time deals',
            'item_limit' => 8,
            'sort_order' => 10,
            'cta_url' => '/products?filter=on-sale',
            'cta_label' => 'View All Deals',
        ],
        [
            'key' => 'categories',
            'title' => 'Shop by Category',
            'subtitle' => 'Browse our main categories',
            'item_limit' => 12,
            'sort_order' => 20,
            'cta_url' => '/products',
            'cta_label' => 'View All Products',
        ],
        [
            'key' => 'latest_products',
            'title' => 'Latest Products',
            'subtitle' => 'Fresh arrivals',
            'item_limit' => 8,
            'sort_order' => 30,
            'cta_url' => '/products?sort=newest',
            'cta_label' => 'View All New Arrivals',
        ],
        [
            'key' => 'blog_posts',
            'title' => 'Latest News',
            'subtitle' => 'From our blog',
            'item_limit' => 3,
            'sort_order' => 40,
            'cta_url' => '/blog',
            'cta_label' => 'View All Posts',
        ],
        // Footer showcase mini sections (cta disabled by default)
        [
            'key' => 'showcase_latest',
            'title' => 'Latest Products',
            'subtitle' => '',
            'item_limit' => 4,
            'sort_order' => 900,
        ],
        [
            'key' => 'showcase_best_selling',
            'title' => 'Best Selling',
            'subtitle' => '',
            'item_limit' => 4,
            'sort_order' => 901,
        ],
        [
            'key' => 'showcase_discount',
            'title' => 'Discount',
            'subtitle' => '',
            'item_limit' => 4,
            'sort_order' => 902,
        ],
        [
            'key' => 'showcase_most_rated',
            'title' => 'Most Rated',
            'subtitle' => '',
            'item_limit' => 4,
            'sort_order' => 903,
        ],
        [
            'key' => 'showcase_brands',
            'title' => 'Brands',
            'subtitle' => '',
            'item_limit' => 8,
            'sort_order' => 904,
        ],
    ];

    public function index(): View
    {
        $this->ensureDefaults();
        $sections = HomepageSection::orderBy('sort_order')->get();
        $activeLanguages = Cache::remember('active_languages_full', 3600, function () {
            try {
                return \DB::table('languages')->where('is_active', 1)->orderBy('is_default', 'desc')->get();
            } catch (\Throwable $e) {
                return collect([
                    (object) [
                        'code' => config('app.locale', 'en'),
                        'is_default' => 1,
                        'name' => strtoupper(config('app.locale', 'en')),
                    ],
                ]);
            }
        });

        return view('admin.homepage.sections.index', compact('sections', 'activeLanguages'));
    }

    public function updateBulk(Request $request): RedirectResponse
    {
        $data = $this->validateBulkUpdateData($request);

        foreach ($data['sections'] as $secData) {
            $this->updateSection($secData);
        }

        Cache::forget('homepage_sections_conf');

        return back()->with('success', __('Homepage sections updated.'));
    }

    public function aiSuggest(Request $request, SimpleAIService $aiService): RedirectResponse
    {
        $request->validate([
            'sections' => 'required|array',
        ]);

        // Get all active languages
        $languages = \App\Models\Language::where('is_active', 1)->get();
        $sections = $request->input('sections');
        $errors = [];

        // Process each section
        foreach ($sections as $index => $section) {
            $sectionKey = $section['key'] ?? "section_{$index}";

            // Get title from any available language or use key as fallback
            $sectionTitle = $sectionKey;
            if (isset($section['title']) && is_array($section['title'])) {
                $sectionTitle = collect($section['title'])->filter()->first() ?: $sectionKey;
            }

            foreach ($languages as $language) {
                try {
                    $result = $aiService->generate($sectionTitle, 'homepage_section', $language->code);

                    if (isset($result['error'])) {
                        $errors[] = "Section {$sectionKey} - Language {$language->name}: " . $result['error'];
                        continue;
                    }

                    // Add content for this language
                    if (isset($result['title'])) {
                        $sections[$index]['title'][$language->code] = $result['title'];
                    }
                    if (isset($result['subtitle'])) {
                        $sections[$index]['subtitle'][$language->code] = $result['subtitle'];
                    }
                    if (isset($result['cta_label'])) {
                        $sections[$index]['cta_label'][$language->code] = $result['cta_label'];
                    }
                } catch (\Exception $e) {
                    $errors[] = "Section {$sectionKey} - Language {$language->name}: " . $e->getMessage();
                }
            }
        }

        // Prepare form data
        $formData = ['sections' => $sections];

        // Prepare success message
        $successMessage = __('AI generated successfully for all sections and languages');
        if (!empty($errors)) {
            $errorCount = count($errors);
            $successMessage .= " " . __('Some items failed') . " ({$errorCount} " . __('errors') . ")";
        }

        return back()->with('success', $successMessage)->withInput($formData);
    }

    private function validateBulkUpdateData(Request $request): array
    {
        return $request->validate([
            'sections' => ['required', 'array'],
            'sections.*.id' => ['required', 'integer', 'exists:homepage_sections,id'],
            'sections.*.enabled' => ['nullable', 'boolean'],
            'sections.*.sort_order' => ['nullable', 'integer', 'between:0,65535'],
            'sections.*.item_limit' => ['nullable', 'integer', 'between:1,100'],
            'sections.*.title' => ['nullable', 'array'],
            'sections.*.subtitle' => ['nullable', 'array'],
            'sections.*.cta_enabled' => ['nullable', 'boolean'],
            'sections.*.cta_url' => ['nullable', 'string', 'max:255'],
            'sections.*.cta_label' => ['nullable', 'array'],
        ]);
    }

    private function updateSection(array $secData): void
    {
        $section = HomepageSection::find($secData['id']);
        if (! $section) {
            return;
        }

        $this->updateSectionBasicFields($section, $secData);
        $this->updateSectionI18nFields($section, $secData);

        $section->save();
    }

    private function updateSectionBasicFields(HomepageSection $section, array $secData): void
    {
        $section->enabled = (bool) ($secData['enabled'] ?? false);

        if (isset($secData['sort_order'])) {
            $section->sort_order = (int) $secData['sort_order'];
        }

        if (isset($secData['item_limit'])) {
            $section->item_limit = (int) $secData['item_limit'];
        }

        $section->cta_enabled = (bool) ($secData['cta_enabled'] ?? false);

        if (array_key_exists('cta_url', $secData)) {
            $section->cta_url = $secData['cta_url'] ?: null;
        }
    }

    private function updateSectionI18nFields(HomepageSection $section, array $secData): void
    {
        if (array_key_exists('title', $secData)) {
            $section->title_i18n = $this->mergeI18nData($section->title_i18n, $secData['title']);
        }

        if (array_key_exists('subtitle', $secData)) {
            $section->subtitle_i18n = $this->mergeI18nData($section->subtitle_i18n, $secData['subtitle']);
        }

        if (array_key_exists('cta_label', $secData)) {
            $section->cta_label_i18n = $this->mergeI18nData($section->cta_label_i18n, $secData['cta_label']);
        }
    }

    private function mergeI18nData(?array $existing, ?array $incoming): array
    {
        $existing = $existing ? $existing : [];

        foreach (($incoming ? $incoming : []) as $lang => $val) {
            if ($val === null) {
                continue;
            }

            if ($val === '') {
                unset($existing[$lang]);
                continue;
            }

            $existing[$lang] = $val;
        }

        return $existing;
    }

    private function ensureDefaults(): void
    {
        // Rename legacy 'brands' full section (if exists) to new showcase_brands key to reuse data
        $legacy = HomepageSection::where('key', 'brands')->first();
        if ($legacy && ! HomepageSection::where('key', 'showcase_brands')->exists()) {
            $legacy->key = 'showcase_brands';
            $legacy->sort_order = 904; // position at end among showcase
            $legacy->save();
        }
        foreach (self::DEFAULT_SECTIONS as $row) {
            HomepageSection::firstOrCreate(['key' => $row['key']], [
                'title_i18n' => [config('app.locale', 'en') => $row['title']],
                'subtitle_i18n' => [config('app.locale', 'en') => $row['subtitle']],
                'item_limit' => $row['item_limit'],
                'sort_order' => $row['sort_order'],
                'cta_enabled' => isset($row['cta_url']) ? true : false,
                'cta_url' => $row['cta_url'] ?? null,
                'cta_label_i18n' => isset($row['cta_label']) ? [config('app.locale', 'en') => $row['cta_label']] : [],
            ]);
        }
    }
}
