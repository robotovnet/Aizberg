<?php

declare(strict_types=1);

namespace Modules\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class CreateOrUpdateManyFilterRequest
 * @package Modules\Catalog\Http\Requests
 */
class UpdateManyFilterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        '*.id'        => 'string',
        '*.product_group_id' => 'string',
        '*.feature_id'       => 'string',
        '*.order'            => 'string',
        '*.type'             => 'string',
    ])]
    public function rules(): array
    {
        return [
            '*.id'        => 'required|integer|min:1|exists:catalog_filters,id',
            '*.product_group_id' => 'required|integer|min:1|exists:product_groups,id',
            '*.feature_id'       => 'required|integer|min:1|exists:features,id',
            '*.order'            => 'required|integer|min:1',
            '*.type'             => 'required|in:radio,checkbox,range,input_with_checkbox',
        ];
    }
}
