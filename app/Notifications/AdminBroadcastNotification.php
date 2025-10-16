<?php

namespace App\Notifications;

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

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        // Determine default language code
        $default = '\App\Models\Language'::where('is_default', true)->value('code') ?? 'en';

        // Find first non-empty title/message to use as fallback
        $firstTitle = '';
        foreach ($this->titleTranslations as $v) {
            if (! empty($v)) {
                $firstTitle = $v;
                break;
            }
        }
        $firstMessage = '';
        foreach ($this->messageTranslations as $v) {
            if (! empty($v)) {
                $firstMessage = $v;
                break;
            }
        }

        // Ensure default language entry exists: if missing, fallback to first provided translation
        $titles = $this->titleTranslations;
        $messages = $this->messageTranslations;
        if (empty($titles) || ! is_array($titles)) {
            $titles = [];
        }
        if (empty($messages) || ! is_array($messages)) {
            $messages = [];
        }
        if (empty($titles[$default]) && $firstTitle !== '') {
            $titles[$default] = $firstTitle;
        }
        if (empty($messages[$default]) && $firstMessage !== '') {
            $messages[$default] = $firstMessage;
        }

        // Top-level fallback values (used by existing views that expect 'title'/'message')
        $titleFallback = $titles[$default] ?? $firstTitle ?? '';
        $messageFallback = $messages[$default] ?? $firstMessage ?? '';

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
}
