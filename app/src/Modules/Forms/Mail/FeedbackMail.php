<?php

declare(strict_types=1);

namespace Modules\Forms\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class FeedbackMail
 * @package Modules\Forms\Mail
 */
class FeedbackMail extends Mailable
{
    use Queueable, SerializesModels;

    public const SUBJECT = 'Сообщение с формы обратной связи';

    private array $formData;

    public function __construct(array $formData)
    {
        $this->formData = $formData;
    }

    public function build()
    {
        return $this->view('forms::emails.feedback')
            ->subject(self::SUBJECT)
            ->with([
                'data' => $this->formData,
            ]);
    }
}
