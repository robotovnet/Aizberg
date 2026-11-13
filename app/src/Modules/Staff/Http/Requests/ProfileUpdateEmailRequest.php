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
class ProfileUpdateEmailRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'email' => 'string',
        'code'  => 'string',
    ])]
    public function rules(): array
    {
        return [
            'email' => 'required_with:code|email|max:100|unique:staff',
            'code'  => 'nullable|numeric|digits:6',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return string[]
     */
    public function messages(): array
    {
        $prefix = 'staff::profile_update_email.validation_messages.';

        $messages['email.required'] = Lang::get($prefix . 'email_required');
        $messages['email.email']    = Lang::get($prefix . 'email_email');
        $messages['email.max']      = Lang::get($prefix . 'email_max');
        $messages['email.unique']   = Lang::get($prefix . 'email_unique');
        $messages['code.numeric']   = Lang::get($prefix . 'code_numeric');
        $messages['code.digits']    = Lang::get($prefix . 'code_digits');

        return $messages;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return string[]
     */
    public function attributes(): array
    {
        $prefix = 'staff::profile_update_email.validation_attributes.';

        $attributes['email'] = Lang::get($prefix . 'email');
        $attributes['code']  = Lang::get($prefix . 'code');

        return $attributes;
    }
}
