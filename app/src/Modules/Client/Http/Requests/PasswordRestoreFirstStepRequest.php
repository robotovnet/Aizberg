<?php

declare(strict_types=1);

namespace Modules\Client\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use JetBrains\PhpStorm\ArrayShape;
use Modules\Settings\Repositories\SettingsRepository;

/**
 * Class PasswordRestoreStep1Request
 * @package Modules\Client\Http\Requests
 */
class PasswordRestoreFirstStepRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'phone'   => 'string',
        'email'   => 'string',
        'captcha' => 'string',
    ])]
    public function rules(SettingsRepository $settingsRepository): array
    {
        $moduleName = $this->segment(1);

        $rules = [
            'phone'    => 'required_without:email|digits_between:11,20|exists:' . $moduleName,
            'email'    => 'required_without:phone|email|exists:' . $moduleName,
            'captcha'  => 'required|captcha',
        ];

        if ($this->routeIs('clients.password_restore.by_email.first_step')) {
            $setting = Config::get('client.settings_codes.show_captcha_at_email_recovery');

            if ($settingsRepository->getSettingByCode($setting) === 'no') {
                unset($rules['captcha']);
            }
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'email.required_without' => Lang::get('client::password_restore.validation_messages.email_required'),
            'email.email'            => Lang::get('client::password_restore.validation_messages.email_is_valid'),
            'email.exists'           => Lang::get('client::password_restore.validation_messages.email_exists'),
            'phone.required_without' => Lang::get('client::password_restore.validation_messages.phone_required'),
            'phone.digits_between'   => Lang::get('client::password_restore.validation_messages.phone_digits_between'),
            'phone.exists'           => Lang::get('client::password_restore.validation_messages.phone_exists'),
            'captcha.required'       => Lang::get('client::password_restore.validation_messages.captcha_required'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return string[]
     */
    public function attributes(): array
    {
        return [
            'phone'   => Lang::get('client::password_restore.validation_attributes.phone'),
            'email'   => Lang::get('client::password_restore.validation_attributes.email'),
            'captcha' => Lang::get('client::password_restore.validation_attributes.captcha'),
        ];
    }
}
