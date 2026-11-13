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
class SeoDataSectionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'seo'                    => 'string',
        'seo.starting_phrases'   => 'string',
        'seo.starting_phrases.*' => 'string',
        'seo.desc'               => 'string',

    ])]
    public function rules(): array
    {
        return [
            'seo'                    => 'nullable',
            'seo.starting_phrases'   => 'nullable|array',
            'seo.starting_phrases.*' => 'nullable|string',
            'seo.desc'               => 'nullable|string',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return string[]
     */
    public function attributes(): array
    {
        $prefix = 'catalog::create_update_section.validation_attributes.';

        $attributes['seo.desc']             = Lang::get($prefix . 'seo.desc');
        $attributes['seo.starting_phrases'] = Lang::get($prefix . 'seo.starting_phrases');

        return $attributes;
    }
}
