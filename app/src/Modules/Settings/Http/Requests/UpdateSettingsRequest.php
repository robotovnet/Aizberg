<?php

declare(strict_types=1);

namespace Modules\Settings\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class UpdateSettingsRequest
 * @package Modules\Settings\Http\Requests
 */
class UpdateSettingsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'value' => 'string',
    ])]
    public function rules(): array
    {
        return [
            'value' => 'required|string|between:1,255',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return string[]
     */
    public function messages(): array
    {
        $prefix = 'settings::update.validation_messages.';

        $messages['value.required'] = Lang::get($prefix . 'value_required');
        $messages['value.between']  = Lang::get($prefix . 'value_between');

        return $messages;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return string[]
     */
    public function attributes(): array
    {
        $prefix = 'settings::update.validation_attributes.';

        $attributes['value'] = Lang::get($prefix . 'value');

        return $attributes;
    }
}
