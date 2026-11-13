<?php

declare(strict_types=1);

namespace Modules\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class PhotoUploadRequest
 * @package Modules\Catalog\Http\Requests
 */
class PhotoUploadRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'photo' => 'string',
    ])]
    public function rules(): array
    {
        return [
            'photo' => 'required|image|dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000|min:0.01|max:7168',
        ];
    }
}
