<?php

declare(strict_types=1);

namespace Modules\Client\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Support\Facades\Lang;

/**
 * Class CreateClientRequest
 * @package Modules\Client\Http\Requests
 */
class LoginRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'phone'     => 'string',
        'email'     => 'string',
        'password'  => 'string',
    ])]
    public function rules(): array
    {
        return [
            'phone'     => 'required_without:email|digits_between:11,20',
            'email'     => 'required_without:phone|email',
            'password'  => 'required|string|min:6|max:20',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'phone.required_without' => Lang::get('client::auth.validation_messages.phone_required'),
            'phone.digits_between'   => Lang::get('client::auth.validation_messages.phone_is_valid'),
            'email.required_without' => Lang::get('client::auth.validation_messages.email_required'),
            'email.email'            => Lang::get('client::auth.validation_messages.email_is_valid'),
            'password.required'      => Lang::get('client::auth.validation_messages.password_required'),
            'password.min'           => Lang::get('client::auth.validation_messages.password_min_length'),
            'password.max'           => Lang::get('client::auth.validation_messages.password_max_length'),
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
            'phone'    => Lang::get('client::auth.validation_attributes.phone'),
            'email'    => Lang::get('client::auth.validation_attributes.email'),
            'password' => Lang::get('client::auth.validation_attributes.password'),
        ];
    }
}
