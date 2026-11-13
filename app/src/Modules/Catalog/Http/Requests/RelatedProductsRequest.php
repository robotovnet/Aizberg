<?php

declare(strict_types=1);

namespace Modules\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class RelatedProductsRequest
 * @package Modules\Catalog\Http\Requests
 */
class RelatedProductsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'products'        => 'string',
        'products.*'      => 'string',

    ])]
    public function rules(): array
    {
        return [
            'products'   => 'required|array',
            'products.*' => 'integer|min:1|exists:products,id',
        ];
    }
}
