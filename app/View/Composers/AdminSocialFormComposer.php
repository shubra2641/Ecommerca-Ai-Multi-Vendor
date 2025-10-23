<?php

declare(strict_types=1);

namespace App\View\Composers;

use Illuminate\View\View;

final class AdminSocialFormComposer
{
    public function compose(View $view): void
    {
        $data = $view->getData();
        $link = $data['link'] ?? null;
        $currentIcon = old('icon', $link->icon ?? 'fas fa-link');

        $view->with('socialIcons', $this->getSocialIcons())
            ->with('socialCurrentIcon', $currentIcon);
    }

    private function getSocialIcons(): array
    {
        return [
            'fab fa-facebook-f' => 'Facebook',
            'fab fa-x-twitter' => 'X / Twitter',
            'fab fa-twitter' => 'Twitter (legacy)',
            'fab fa-instagram' => 'Instagram',
            'fab fa-linkedin-in' => 'LinkedIn',
            'fab fa-youtube' => 'YouTube',
            'fab fa-tiktok' => 'TikTok',
            'fab fa-github' => 'GitHub',
            'fab fa-gitlab' => 'GitLab',
            'fab fa-discord' => 'Discord',
            'fab fa-telegram' => 'Telegram',
            'fab fa-whatsapp' => 'WhatsApp',
            'fab fa-snapchat-ghost' => 'Snapchat',
            'fab fa-pinterest' => 'Pinterest',
            'fab fa-reddit' => 'Reddit',
            'fab fa-dribbble' => 'Dribbble',
            'fab fa-behance' => 'Behance',
            'fab fa-medium' => 'Medium',
            'fab fa-stack-overflow' => 'Stack Overflow',
            'fas fa-globe' => 'Website',
            'fas fa-link' => 'Generic Link',
        ];
    }
}
