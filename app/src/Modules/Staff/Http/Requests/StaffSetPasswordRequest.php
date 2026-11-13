<?php

declare(strict_types=1);

namespace Modules\Staff\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Support\Facades\Lang;

/**
 * Class StaffSetPasswordRequest
 * @package Modules\Staff\Http\Requests
 */
class StaffSetPasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'password' => 'string',
    ])]
    public function rules(): array
    {
        return [
            'password' => 'required|string|between:6,20',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return string[]
     */
    public function messages(): array
    {
        $prefix = 'staff::staff_set_password.validation_messages.';

        $messages['password.required'] = Lang::get($prefix . 'password_required');
        $messages['password.between']  = Lang::get($prefix . 'password_between');

        return $messages;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return string[]
     */
    public function attributes(): array
    {
        $prefix = 'staff::staff_set_password.validation_attributes.';

        $attributes['password'] = Lang::get($prefix . 'password');

        return $attributes;
    }
}
