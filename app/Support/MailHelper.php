<?php

declare(strict_types=1);

namespace App\Support;

class MailHelper
{
    /**
     * Return true if mail sending appears configured enough to attempt sending.
     * This is defensive: it checks configured mailer and key env vars for common drivers.
     */
    public static function mailIsAvailable(): bool
    {
        $driver = config('mail.default') ?: env('MAIL_MAILER');
        if (! $driver) {
            return false;
        }

        if (in_array($driver, ['array', 'log'], true)) {
            return false;
        }

        if (in_array($driver, ['smtp', 'mail'], true)) {
            return self::isSmtpConfigured();
        }

        return true;
    }

    private static function isSmtpConfigured(): bool
    {
        $host = env('MAIL_HOST');
        $username = env('MAIL_USERNAME');
        $password = env('MAIL_PASSWORD');

        return ! empty($host) && (! empty($username) || ! empty($password));
    }
}
