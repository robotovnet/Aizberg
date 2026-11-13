<?php

declare(strict_types=1);

namespace Modules\OrderOneClick\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class SetCatalogSectionsRequest
 * @package Modules\OrderOneClick\Http\Requests
 */
class SetCatalogSectionsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'catalog_sections'        => 'string',
        'catalog_sections.*'      => 'string',

    ])]
    public function rules(): array
    {
        return [
            'catalog_sections'   => 'present|array',
            'catalog_sections.*' => 'integer|min:1|exists:catalog_sections,id',
        ];
    }
}
