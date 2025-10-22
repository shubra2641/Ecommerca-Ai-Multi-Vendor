<?php

namespace App\View\Composers;

use Illuminate\View\View;

class AdminSocialFormComposer
{
    public function compose(View $view): void
    {
        $icons = [
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
        $data = $view->getData();
        $link = $data['link'] ?? null;
        $currentIcon = old('icon', $link->icon ?? 'fas fa-link');
        $view->with('socialIcons', $icons)->with('socialCurrentIcon', $currentIcon);
    }
}
