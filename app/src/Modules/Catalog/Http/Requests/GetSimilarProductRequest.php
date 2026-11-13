<?php

declare(strict_types=1);

namespace Modules\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class GetSimilarProductRequest
 * @package Modules\Catalog\Http\Requests
 */
class GetSimilarProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'products'       => 'string',
        'products.*'  => 'string',
    ])]
    public function rules(): array
    {
        return [
            'products'    => 'required|array|min:1',
            'products.*'  => 'required|integer|min:1|exists:products,id',
        ];
    }
}
