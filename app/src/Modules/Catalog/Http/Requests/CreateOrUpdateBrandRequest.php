<?php

declare(strict_types=1);

namespace Modules\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Support\Facades\Lang;

/**
 * Class CreateOrUpdateBrandRequest
 * @package Modules\Catalog\Http\Requests
 */
class CreateOrUpdateBrandRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'names'            => 'string',
        'names.*.lang'     => 'string',
        'names.*.value'    => 'string',
        'country_code'     => 'string',
        'official_website' => 'string',
        'desc'             => 'string',
        'is_hidden'        => 'string',
    ])]
    public function rules(): array
    {
        return [
            'names'            => 'required|array|min:1',
            'names.*.lang'     => 'required|string|size:2|exists:languages,code',
            'names.*.value'    => 'required|string|between:1,60',
            'country_code'     => 'required|string|size:2',
            'official_website' => 'nullable|url',
            'desc'             => 'nullable|string|min:1',
            'is_hidden'        => 'required|in:Y,N',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return string[]
     */
    public function messages(): array
    {
        $prefix = 'catalog::create_update_brand.validation_messages.';

        $messages['country_code.size'] = Lang::get($prefix . 'country_code_size');

        return $messages;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return string[]
     */
    public function attributes(): array
    {
        $prefix = 'catalog::create_update_brand.validation_attributes.';

        $attributes['names.0.value']     = Lang::get($prefix . 'name');
        $attributes['country_code']     = Lang::get($prefix . 'country_code');
        $attributes['official_website'] = Lang::get($prefix . 'official_website');
        $attributes['desc']             = Lang::get($prefix . 'desc');
        $attributes['is_hidden']        = Lang::get($prefix . 'is_hidden');

        return $attributes;
    }
}
