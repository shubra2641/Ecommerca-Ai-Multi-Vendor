<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Language;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminBroadcastNotification extends Notification
{
    use Queueable;

    protected array $titleTranslations;

    protected array $messageTranslations;

    protected ?string $url;

    public function __construct(array $titleTranslations = [], array $messageTranslations = [], ?string $url = null)
    {
        $this->titleTranslations = $titleTranslations;
        $this->messageTranslations = $messageTranslations;
        $this->url = $url;
    }

    public function via(): array
    {
        return ['database'];
    }

    public function toArray(): array
    {
        $default = Language::where('is_default', true)->value('code') ?? 'en';

        $titles = $this->ensureDefaultTranslation($this->titleTranslations, $default);
        $messages = $this->ensureDefaultTranslation($this->messageTranslations, $default);

        $titleFallback = $titles[$default] ?? $this->getFirstNonEmpty($this->titleTranslations) ?? '';
        $messageFallback = $messages[$default] ?? $this->getFirstNonEmpty($this->messageTranslations) ?? '';

        return [
            'type' => 'admin_broadcast',
            'title' => $titleFallback,
            'message' => $messageFallback,
            'default_lang' => $default,
            'title_translations' => $titles,
            'message_translations' => $messages,
            'url' => $this->url,
        ];
    }

    private function getFirstNonEmpty(array $translations): ?string
    {
        return collect($translations)->first(fn ($v) => ! empty($v));
    }

    private function ensureDefaultTranslation(array $translations, string $default): array
    {
        $processed = is_array($translations) ? $translations : [];
        $first = $this->getFirstNonEmpty($processed);

        if (! isset($processed[$default]) && $first !== null) {
            $processed[$default] = $first;
        }

        return $processed;
    }
}
