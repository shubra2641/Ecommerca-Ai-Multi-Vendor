<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Notifications\WebPushService;
use Illuminate\Console\Command;

class SendWebPushCommand extends Command
{
    protected $signature = 'push:send {title : Notification title} {body : Notification body} {--data= : JSON payload extras}';

    protected $description = 'Broadcast a Web Push notification to all stored subscriptions';

    public function handle(WebPushService $service): int
    {
        $title = $this->argument('title');
        $body = $this->argument('body');
        $extras = [];
        $json = $this->option('data');
        if (! $json) {
            foreach ((array) ($_SERVER['argv'] ?? []) as $arg) {
                if (strpos($arg, '--data=') === 0) {
                    $json = substr($arg, 7);
                    break;
                }
            }
        }
        if ($json) {
            try {
                $decoded = json_decode($json, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $extras = $decoded;
                } else {
                    // Fallback: very small regex-based parser for simple {"k":"v",...}
                    if (preg_match_all('/"([^"\\]+)"\s*:\s*"([^"\\]*)"/', $json, $m)) {
                        foreach ($m[1] as $i => $k) {
                            $extras[$k] = $m[2][$i];
                        }
                        if (! $extras) {
                            $this->warn('Invalid JSON for --data ignored.');
                        }
                    } else {
                        $this->warn('Invalid JSON for --data ignored.');
                    }
                }
            } catch (\Throwable $e) {
                $this->warn('Invalid JSON for --data ignored.');
            }
        }
        $payload = array_merge(['title' => $title, 'body' => $body], $extras);
        $count = $service->sendToAll($payload);
        $this->info("Pushed to {$count} subscription(s).");

        return self::SUCCESS;
    }
}
