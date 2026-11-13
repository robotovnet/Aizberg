<?php

declare(strict_types=1);

namespace Modules\Lottery\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;

/**
 * Class AcceptAppMail
 * @package Modules\Lottery\Mail
 */
class AcceptAppMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public string $ticketCode;

    /**
     * Create a new message instance.
     */
    public function __construct(string $ticketCode)
    {
        $this->ticketCode = $ticketCode;
    }

    /**
     * Build the message.
     *
     * @return Mailable
     */
    public function build(): Mailable
    {
        return $this->subject(Lang::get('lottery::emails/accept_app.subject'))
            ->view('lottery::emails.accept_app');
    }
}
