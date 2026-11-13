<?php

declare(strict_types=1);

namespace Modules\Client\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;

/**
 * Class PasswordRestore
 * @package App\Mail
 */
class PasswordRestore extends Mailable
{
    use Queueable;
    use SerializesModels;

    /** @var int Stores the recovery code. */
    public int $recoveryCode;

    /**
     * Create a new message instance.
     */
    public function __construct(int $recoveryCode)
    {
        $this->recoveryCode = $recoveryCode;
    }

    /**
     * Build the message.
     *
     * @return Mailable
     */
    public function build(): Mailable
    {
        return $this->subject(Lang::get('client::emails/password_restore.subject'))
            ->view('client::emails.password-restore');
    }
}
