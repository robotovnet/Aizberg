<?php

declare(strict_types=1);

namespace Modules\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Support\Facades\Lang;

/**
 * Class CreateOrUpdateFeatureRequest
 * @package Modules\Catalog\Http\Requests
 */
class CreateOrUpdateFeatureRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'names'             => 'string',
        'names.*.lang'      => 'string',
        'names.*.value'     => 'string',
        'units'             => 'string',
        'units.*.lang'      => 'string',
        'units.*.value'     => 'string',
        'is_for_all_groups' => 'string',
        'sort_type'         => 'string',
        'type'              => 'string',
        'value_separator'   => 'string',
    ])]
    public function rules(): array
    {
        return [
            'names'              => 'required|array|min:1',
            'names.*.lang'       => 'required|string|size:2|exists:languages,code',
            'names.*.value'      => 'required|string|between:1,100',
            'units'              => 'nullable|array',
            'units.*.lang'       => 'nullable|string|size:2|exists:languages,code',
            'units.*.value'      => 'nullable|string|between:1,100',
            'is_for_all_groups'  => 'required|in:Y,N',
            'sort_type'          => 'required|in:numerical,string',
            'type'               => 'required|in:only_input,radio,checkbox',
            'value_separator'    => 'nullable|string|max:10',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return string[]
     */
    public function messages(): array
    {
        $prefix = 'catalog::create_update_feature.validation_messages.';

        $messages['type.in'] = Lang::get($prefix . 'type_in');

        return $messages;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return string[]
     */
    public function attributes(): array
    {
        $prefix = 'catalog::create_update_feature.validation_attributes.';

        $attributes['is_for_all_groups'] = Lang::get($prefix . 'is_for_all_groups');
        $attributes['sort_type']         = Lang::get($prefix . 'sort_type');
        $attributes['type']              = Lang::get($prefix . 'type');
        $attributes['names.0.value']     = Lang::get($prefix . 'names');
        $attributes['units.0.value']     = Lang::get($prefix . 'units');

        return $attributes;
    }
}
