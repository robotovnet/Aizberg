<?php

declare(strict_types=1);

namespace Modules\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class AdPhotoUploadRequest
 * @package Modules\Catalog\Http\Requests
 */
class AdPhotoUploadRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'adphoto' => 'string',
    ])]
    public function rules(): array
    {
        return [
            'adphoto' => 'required|image|dimensions:min_width=100,min_height=100|min:0.01|max:5120',
        ];
    }
}
