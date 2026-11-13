<?php

declare(strict_types=1);

namespace Modules\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class PhotoOrderRequest
 * @package Modules\Catalog\Http\Requests
 */
class PhotoOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'photos'             => 'string',
        'photos.*.is_main'   => 'string',
        'photos.*.pos'       => 'string',
        'photos.*.photo_id'  => 'string',
    ])]
    public function rules(): array
    {
        return [
            'photos'             => 'required|array|min:1',
            'photos.*.is_main'   => 'required|string|in:Y,N',
            'photos.*.pos'       => 'required|integer|min:1|max:999999',
            'photos.*.photo_id'  => 'required|integer|min:1|exists:product_photos,id',
        ];
    }
}

