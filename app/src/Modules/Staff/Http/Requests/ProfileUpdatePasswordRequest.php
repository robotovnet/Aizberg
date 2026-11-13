<?php

declare(strict_types=1);

namespace Modules\Staff\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Support\Facades\Lang;

/**
 * Class ProfileUpdateNameRequest
 * @package Modules\Client\Http\Requests
 */
class ProfileUpdatePasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'current_password' => 'string',
        'password'         => 'string',
    ])]
    public function rules(): array
    {
        return [
            'current_password' => 'required|string|between:6,20',
            'password'         => 'required|string|between:6,20|confirmed',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return string[]
     */
    public function messages(): array
    {
        $prefix = 'staff::profile_update_password.validation_messages.';

        $messages['current_password.required'] = Lang::get($prefix . 'current_password_required');
        $messages['current_password.between']  = Lang::get($prefix . 'current_password_between');
        $messages['password.required']         = Lang::get($prefix . 'password_required');
        $messages['password.between']          = Lang::get($prefix . 'password_between');
        $messages['password.confirmed']        = Lang::get($prefix . 'password_confirmed');

        return $messages;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return string[]
     */
    public function attributes(): array
    {
        $prefix = 'staff::profile_update_password.validation_attributes.';

        $attributes['current_password'] = Lang::get($prefix . 'current_password');
        $attributes['password']         = Lang::get($prefix . 'password');

        return $attributes;
    }
}
