<?php

declare(strict_types=1);

namespace Modules\Client\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class PasswordRestoreThirdStepRequest
 * @package Modules\Client\Http\Requests
 */
class PasswordRestoreThirdStepRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'password'   => 'string',
    ])]
    public function rules(): array
    {
        return [
            'password' => 'required|string|between:6,20|confirmed',
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
            'password.required'  => Lang::get('client::password_restore.validation_messages.password_required'),
            'password.between'   => Lang::get('client::password_restore.validation_messages.password_between'),
            'password.confirmed' => Lang::get('client::password_restore.validation_messages.password_confirmed'),

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
            'password' => Lang::get('client::password_restore.validation_attributes.password'),
        ];
    }
}
