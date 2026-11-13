<?php

declare(strict_types=1);

namespace Modules\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class SectionIconUploadRequest
 * @package Modules\Catalog\Http\Requests
 */
class SectionIconUploadRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'icon' => 'string',
    ])]
    public function rules(): array
    {
        return [
            'icon' => 'required|mimes:jpeg,jpg,png,svg|min:0.01|max:3072',
        ];
    }
}
