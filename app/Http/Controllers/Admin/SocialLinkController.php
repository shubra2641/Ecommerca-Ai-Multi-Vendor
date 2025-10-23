<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialLink;
use App\Services\HtmlSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SocialLinkController extends Controller
{
    /**
     * Allowed icon classes (whitelist)
     */
    private array $allowedIcons = [
        'fab fa-facebook-f',
        'fab fa-x-twitter',
        'fab fa-twitter',
        'fab fa-instagram',
        'fab fa-linkedin-in',
        'fab fa-youtube',
        'fab fa-tiktok',
        'fab fa-github',
        'fab fa-gitlab',
        'fab fa-discord',
        'fab fa-telegram',
        'fab fa-whatsapp',
        'fab fa-snapchat-ghost',
        'fab fa-pinterest',
        'fab fa-reddit',
        'fab fa-dribbble',
        'fab fa-behance',
        'fab fa-medium',
        'fab fa-stack-overflow',
        'fas fa-globe',
        'fas fa-link',
    ];

    public function index()
    {
        $links = SocialLink::orderBy('order')->get();

        return view('admin.social.index', compact('links'));
    }

    public function create()
    {
        $link = new SocialLink;

        return view('admin.social.form', compact('link'));
    }

    public function store(Request $request, HtmlSanitizer $sanitizer): RedirectResponse
    {
        $data = $this->validateData($request);
        // sanitize label and url
        if (isset($data['label']) && is_string($data['label'])) {
            $data['label'] = $sanitizer->clean($data['label']);
        }
        if (isset($data['url']) && is_string($data['url'])) {
            $data['url'] = $sanitizer->clean($data['url']);
        }
        $data['order'] = $data['order'] ?? SocialLink::max('order') + 1;
        SocialLink::create($data);

        return redirect()->route('admin.social.index')->with('success', __('Social link created.'));
    }

    public function edit(SocialLink $social)
    {
        $link = $social;

        return view('admin.social.form', compact('link'));
    }

    public function update(Request $request, SocialLink $social, HtmlSanitizer $sanitizer): RedirectResponse
    {
        $data = $this->validateData($request, $social->id);
        if (isset($data['label']) && is_string($data['label'])) {
            $data['label'] = $sanitizer->clean($data['label']);
        }
        if (isset($data['url']) && is_string($data['url'])) {
            $data['url'] = $sanitizer->clean($data['url']);
        }
        $social->update($data);

        return redirect()->route('admin.social.index')->with('success', __('Social link updated.'));
    }

    public function destroy(SocialLink $social): RedirectResponse
    {
        $social->delete();

        return redirect()->route('admin.social.index')->with('success', __('Social link deleted.'));
    }

    public function reorder(Request $request): RedirectResponse
    {
        $request->validate([
            'orders' => ['required', 'array'],
        ]);
        foreach ($request->orders as $id => $order) {
            SocialLink::where('id', $id)->update(['order' => (int) $order]);
        }

        return back()->with('success', __('Order updated.'));
    }

    private function validateData(Request $request, ?int $id = null): array
    {
        $validated = $request->validate([
            'platform' => ['required', 'string', 'max:50'],
            'label' => ['nullable', 'string', 'max:100'],
            'url' => ['required', 'url', 'max:255'],
            'icon' => ['required', 'string', 'in:'.implode(',', $this->allowedIcons)],
            'order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
        $validated['is_active'] = $request->has('is_active');
        // No fallback needed; icon required & validated. If somehow empty, default.
        if (empty($validated['icon'])) {
            $validated['icon'] = 'fas fa-link';
        }

        return $validated;
    }
}
