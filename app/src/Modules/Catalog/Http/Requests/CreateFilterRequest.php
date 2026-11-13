<?php

declare(strict_types=1);

namespace Modules\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class CreateFilterRequest
 * @package Modules\Catalog\Http\Requests
 */
class CreateFilterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        '*.product_group_id' => 'string',
        '*.feature_id'       => 'string',
        '*.order'            => 'string',
        '*.type'             => 'string',
    ])]
    public function rules(): array
    {
        return [
            '*.product_group_id' => 'required|integer|min:1|exists:product_groups,id',
            '*.feature_id'       => 'required|integer|min:1|exists:features,id',
            '*.order'            => 'required|integer|min:1',
            '*.type'             => 'required|in:radio,checkbox,range,input_with_checkbox',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return string[]
     */
    public function attributes(): array
    {
        $prefix = 'catalog::create_filters.validation_attributes.';

        for ($i = 0; $i < 20; $i++) {
            $attributes[$i . '.type'] = Lang::get($prefix . 'type');
        }

        return $attributes;
    }
}
