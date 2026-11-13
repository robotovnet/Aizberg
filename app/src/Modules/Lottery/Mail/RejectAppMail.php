<?php

declare(strict_types=1);

namespace Modules\Lottery\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;

/**
 * Class RejectAppMail
 * @package Modules\Lottery\Mail
 */
class RejectAppMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public string $ticketCode;
    public string $rejectDesc;

    /**
     * Create a new message instance.
     */
    public function __construct(string $ticketCode, string $rejectDesc)
    {
        $this->ticketCode = $ticketCode;
        $this->rejectDesc = $rejectDesc;
    }

    /**
     * Build the message.
     *
     * @return Mailable
     */
    public function build(): Mailable
    {
        return $this->subject(Lang::get('lottery::emails/reject_app.subject'))
            ->view('lottery::emails.reject_app');
    }
}
