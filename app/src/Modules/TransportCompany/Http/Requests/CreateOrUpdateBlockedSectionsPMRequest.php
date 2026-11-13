<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class CreateOrUpdateBlockedSectionsPMRequest
 * @package Modules\TransportCompany\Http\Requests
 */
class CreateOrUpdateBlockedSectionsPMRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'sections'    => 'string',
        'sections.*'  => 'string',
    ])]
    public function rules(): array
    {
        return [
            'sections'    => 'nullable|array',
            'sections.*'  => 'nullable|integer|min:1|exists:catalog_sections,id',
        ];
    }
}
