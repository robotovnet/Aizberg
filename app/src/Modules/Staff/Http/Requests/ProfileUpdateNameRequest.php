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
class ProfileUpdateNameRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'first_name' => 'string',
        'last_name'  => 'string',
        'whatsapp'   => 'string',
        'telegram'   => 'string',
        'max'        => 'string',
    ])]
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|between:2,60',
            'last_name'  => 'required|string|between:2,60',
            'whatsapp'   => 'nullable|string|max:50',
            'telegram'   => 'nullable|string|max:50',
            'max'        => 'nullable|string|max:50',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return string[]
     */
    public function messages(): array
    {
        $prefix = 'staff::profile_update_name.validation_messages.';

        $messages['first_name.required'] = Lang::get($prefix . 'first_name_required');
        $messages['first_name.between']  = Lang::get($prefix . 'first_name_between');
        $messages['last_name.required']  = Lang::get($prefix . 'last_name_required');
        $messages['last_name.between']   = Lang::get($prefix . 'last_name_between');
        $messages['whatsapp.max']        = Lang::get($prefix . 'whatsapp_max');
        $messages['telegram.max']        = Lang::get($prefix . 'telegram_max');
        $messages['max.max']             = Lang::get($prefix . 'max_max');

        return $messages;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return string[]
     */
    public function attributes(): array
    {
        $prefix = 'staff::profile_update_name.validation_attributes.';

        $attributes['first_name'] = Lang::get($prefix . 'first_name');
        $attributes['last_name']  = Lang::get($prefix . 'last_name');
        $attributes['whatsapp']   = Lang::get($prefix . 'whatsapp');
        $attributes['telegram']   = Lang::get($prefix . 'telegram');
        $attributes['max']        = Lang::get($prefix . 'max');

        return $attributes;
    }
}
