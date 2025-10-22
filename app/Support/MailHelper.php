<?php

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

        // treat array and log as non-real drivers — skip sending
        if (in_array($driver, ['array', 'log'], true)) {
            return false;
        }

        // For smtp require host and at least username/password OR a from address
        if ($driver === 'smtp' || $driver === 'mail') {
            $host = env('MAIL_HOST');
            $username = env('MAIL_USERNAME');
            $password = env('MAIL_PASSWORD');

            return ! empty($host) && (! empty($username) || ! empty($password));
        }

        // For other drivers (ses, postmark, sendmail, resend, mailgun) assume configured
        return true;
    }
}
