<?php

declare(strict_types=1);

namespace Modules\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class OrderRequest
 * @package Modules\Catalog\Http\Requests
 */
class OrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'order'               => 'string',
        'order.*.section_id'  => 'string',
        'order.*.order'       => 'string',

    ])]
    public function rules(): array
    {
        return [
            'order'               => 'required|array|min:1',
            'order.*.section_id'  => 'required|integer|min:1|exists:catalog_sections,id',
            'order.*.order'       => 'required|integer|min:1',
        ];
    }
}
