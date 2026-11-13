<?php

declare(strict_types=1);

namespace Modules\Forms\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Modules\Email\Services\MailerService;
use Modules\Forms\Http\Requests\FeedbackRequest;
use Modules\Forms\Mail\FeedbackMail;
use Modules\Settings\Repositories\SettingsRepository;

/**
 * Class FormController
 * @package Modules\Forms\Http\Controllers
 */
class FormController extends Controller
{
    private MailerService $mailerService;
    private SettingsRepository $settingsRepo;

    public function __construct(MailerService $mailerService, SettingsRepository $settingsRepo)
    {
        $this->mailerService = $mailerService;
        $this->settingsRepo = $settingsRepo;
    }

    /**
     * Send the feedback form to the manager's email.
     */
    public function feedback(FeedbackRequest $request): JsonResponse
    {
        // Проверочный код нужен только для валидации, в письмо он не попадает
        $data = $this->processFormData($request->safe()->except('captcha'));

        $html = (new FeedbackMail($data))->render();
        $this->mailerService->send(
            FeedbackMail::SUBJECT,
            $html,
            $this->recipients(Config::get('forms.settings_codes.feedback_email')),
            $data['email']
        );

        return response()->json(['result' => 'success']);
    }

    /**
     * Email addresses from a setting, comma-separated.
     *
     * @return string[]
     */
    private function recipients(string $settingCode): array
    {
        return array_map('trim', explode(',', $this->settingsRepo->getSettingByCode($settingCode)));
    }

    /**
     * Strip HTML tags from user input and keep line breaks as <br>.
     */
    private function processFormData(array $data): array
    {
        array_walk_recursive($data, static function (&$value) {
            if (is_string($value)) {
                $value = str_replace(["\r\n", "\r", "\n"], '<br>', strip_tags($value));
            }
        });

        return $data;
    }
}
