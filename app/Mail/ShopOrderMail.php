<?php

namespace App\Mail;

use App\Models\ShopOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ShopOrderMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  list<string>  $lines
     * @param  array{label: string, url: string}|null  $action
     */
    public function __construct(
        public ShopOrder $order,
        public string $subjectLine,
        public array $lines,
        public ?array $action = null,
        public ?string $customerName = null,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->subjectLine);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.shop.order');
    }
}
