<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VendorExportReady extends Mailable
{
    use Queueable;
    use SerializesModels;

    private $url;

    private $filename;

    public function __construct(string $url, string $filename)
    {
        $this->url = $url;
        $this->filename = $filename;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function build()
    {
        return $this->subject('Your vendor orders export is ready')
            ->view('emails.vendor.export_ready')
            ->with(['url' => $this->url, 'filename' => $this->filename]);
    }
}
