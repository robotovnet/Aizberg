<?php

declare(strict_types=1);

namespace Modules\Client\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use JetBrains\PhpStorm\ArrayShape;
use Modules\Settings\Repositories\SettingsRepository;
use Nwidart\Modules\Facades\Module;
use Illuminate\Support\Facades\Config;

/**
 * Class CreateClientRequest
 * @package Modules\Client\Http\Requests
 */
class CreateClientRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @param  SettingsRepository  $settingsRepository
     * @return string[]
     */
    #[ArrayShape([
        'first_name'   => 'string',
        'last_name'    => 'string',
        'email'        => 'string',
        'phone'        => 'string',
        'country_code' => 'string',
        'city_id'      => 'string',
        'password'     => 'string',
        'timezone'     => 'string',
        'captcha'      => 'string',
    ])]
    public function rules(SettingsRepository $settingsRepository): array
    {
        $rules = [
            'first_name'    => 'required|string|between:2,60',
            'last_name'     => 'required|string|between:2,60',
            'email'         => 'required|email|max:100|unique:clients',
            'phone'         => 'required|digits_between:11,20|unique:clients',
            'country_code'  => 'required|string|size:2|exists:countries,code',
            'city_id'       => 'required|numeric|min:1|exists:cities,id',
            'password'      => 'required|string|between:6,20|confirmed',
            'timezone'      => 'required|timezone',
        ];

        $moduleName = Module::getModuleName(__NAMESPACE__);
        $setting = Config::get($moduleName . '.settings_codes.show_captcha_at_registration');

        // If the captcha is enabled, then we activate its verification
        if ($settingsRepository->getSettingByCode($setting) != 'no') {
            $rules['captcha'] = 'required|captcha';
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
            'first_name.required'   => Lang::get('client::create_client.validation_messages.first_name_required'),
            'first_name.between'    => Lang::get('client::create_client.validation_messages.first_name_between'),
            'last_name.required'    => Lang::get('client::create_client.validation_messages.last_name_required'),
            'last_name.between'     => Lang::get('client::create_client.validation_messages.last_name_between'),
            'email.required'        => Lang::get('client::create_client.validation_messages.email_required'),
            'email.email'           => Lang::get('client::create_client.validation_messages.email_is_valid'),
            'email.unique'          => Lang::get('client::create_client.validation_messages.email_unique'),
            'email.max'             => Lang::get('client::create_client.validation_messages.email_max'),
            'phone.required'        => Lang::get('client::create_client.validation_messages.phone_required'),
            'phone.digits_between'  => Lang::get('client::create_client.validation_messages.phone_digits_between'),
            'phone.unique'          => Lang::get('client::create_client.validation_messages.phone_unique'),
            'password.required'     => Lang::get('client::create_client.validation_messages.password_required'),
            'password.between'      => Lang::get('client::create_client.validation_messages.password_between'),
            'password.confirmed'    => Lang::get('client::create_client.validation_messages.password_confirmed'),
            'captcha.required'      => Lang::get('client::create_client.validation_messages.captcha_required'),
            'captcha.captcha'       => Lang::get('client::create_client.validation_messages.captcha_invalid'),
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
            'phone'        => Lang::get('client::create_client.validation_attributes.phone'),
            'email'        => Lang::get('client::create_client.validation_attributes.email'),
            'password'     => Lang::get('client::create_client.validation_attributes.password'),
            'first_name'   => Lang::get('client::create_client.validation_attributes.first_name'),
            'last_name'    => Lang::get('client::create_client.validation_attributes.last_name'),
            'timezone'     => Lang::get('client::create_client.validation_attributes.timezone'),
            'city_id'      => Lang::get('client::create_client.validation_attributes.city_id'),
            'country_code' => Lang::get('client::create_client.validation_attributes.country_code'),
            'captcha'      => Lang::get('client::create_client.validation_attributes.captcha'),
        ];
    }
}
