<?php

declare(strict_types=1);

namespace Modules\Staff\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;

/**
 * Class PasswordRestore
 * @package Modules\Staff\Mail
 */
class ConfirmationCode extends Mailable
{
    use Queueable;
    use SerializesModels;

    /** @var int Stores the confirmation code. */
    public int $code;

    /**
     * Create a new message instance.
     */
    public function __construct(int $code)
    {
        $this->code = $code;
    }

    /**
     * Build the message.
     *
     * @return Mailable
     */
    public function build(): Mailable
    {
        return $this->subject(Lang::get('staff::emails/confirmation_code.subject'))
            ->view('staff::emails.confirmation-code');
    }
}
