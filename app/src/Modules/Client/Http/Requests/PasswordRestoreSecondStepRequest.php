<?php

declare(strict_types=1);

namespace Modules\Client\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class PasswordRestoreSecondStepRequest
 * @package Modules\Client\Http\Requests
 */
class PasswordRestoreSecondStepRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'code'   => 'string',
    ])]
    public function rules(): array
    {
        return [
            'code' => 'required|numeric|digits:6',
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
            'code.required' => Lang::get('client::password_restore.validation_messages.code_required'),
            'code.numeric'  => Lang::get('client::password_restore.validation_messages.code_numeric'),
            'code.digits'   => Lang::get('client::password_restore.validation_messages.code_digits'),
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
            'code' => Lang::get('client::password_restore.validation_attributes.code'),
        ];
    }
}
