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
class ProfileUpdatePhoneRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'phone' => 'string',
        'code'  => 'string',
    ])]
    public function rules(): array
    {
        return [
            'phone' => 'required_with:code|digits_between:11,20|unique:staff',
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
        $prefix = 'staff::profile_update_phone.validation_messages.';

        $messages['phone.required']       = Lang::get($prefix . 'phone_required');
        $messages['phone.digits_between'] = Lang::get($prefix . 'phone_digits_between');
        $messages['phone.unique']         = Lang::get($prefix . 'phone_unique');
        $messages['code.numeric']         = Lang::get($prefix . 'code_numeric');
        $messages['code.digits']          = Lang::get($prefix . 'code_digits');

        return $messages;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return string[]
     */
    public function attributes(): array
    {
        $prefix = 'staff::profile_update_phone.validation_attributes.';

        $attributes['phone'] = Lang::get($prefix . 'phone');
        $attributes['code']  = Lang::get($prefix . 'code');

        return $attributes;
    }
}
