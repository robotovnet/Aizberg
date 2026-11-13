<?php

declare(strict_types=1);

namespace Modules\Qa\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;

/**
 * Class PasswordRestore
 * @package Modules\Qa\Mail
 */
class AnswerToQuestion extends Mailable
{
    use Queueable;
    use SerializesModels;

    public string $productUrl;
    public string $productName;
    public string $question;
    public string $answer;

    /**
     * Create a new message instance.
     */
    public function __construct(string $productUrl, string $productName, string $question, string $answer)
    {
        $this->productUrl = $productUrl;
        $this->productName = $productName;
        $this->question = $question;
        $this->answer = $answer;
    }

    /**
     * Build the message.
     *
     * @return Mailable
     */
    public function build(): Mailable
    {
        return $this->subject(Lang::get('qa::emails/answer_to_question.subject'))
            ->view('qa::emails.answer_to_question');
    }
}
