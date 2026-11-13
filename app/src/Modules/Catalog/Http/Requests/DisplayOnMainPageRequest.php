<?php

declare(strict_types=1);

namespace Modules\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class DisplayOnMainPageRequest
 * @package Modules\Catalog\Http\Requests
 */
class DisplayOnMainPageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'should_display' => 'string',
    ])]
    public function rules(): array
    {
        return [
            'should_display' => 'required|in:Y,N',
        ];
    }
}
