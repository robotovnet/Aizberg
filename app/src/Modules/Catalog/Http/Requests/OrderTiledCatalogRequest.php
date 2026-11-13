<?php

declare(strict_types=1);

namespace Modules\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class OrderTiledCatalogRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'orders'              => 'string',
        'orders.*.section_id' => 'string',
        'orders.*.order'      => 'string',
    ])]
    public function rules(): array
    {
        return [
            'orders'               => 'required|array',
            'orders.*.section_id'  => 'required|numeric|min:1|exists:catalog_sections,id',
            'orders.*.order'       => 'required|numeric|min:1',
        ];
    }
}
