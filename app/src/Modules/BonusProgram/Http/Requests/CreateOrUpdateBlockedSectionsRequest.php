<?php

declare(strict_types=1);

namespace Modules\BonusProgram\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class CreateOrUpdateBlockedSectionRequest
 * @package Modules\BonusProgram\Http\Requests
 */
class CreateOrUpdateBlockedSectionsRequest extends FormRequest
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
            'sections'    => 'required|array',
            'sections.*'  => 'required|integer|min:1|exists:catalog_sections,id',
        ];
    }
}

