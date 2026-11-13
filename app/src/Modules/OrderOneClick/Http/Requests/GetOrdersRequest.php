<?php

declare(strict_types=1);

namespace Modules\OrderOneClick\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class GetOrdersRequest
 * @package Modules\OrderOneClick\Http\Requests
 */
class GetOrdersRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'only_completed' => 'string',
        'page'           => 'string',
        'date_from'      => 'string',
        'date_to'        => 'string',
    ])]
    public function rules(): array
    {
        return [
            'only_completed' => 'nullable|in:Y,N,ALL',
            'page'           => 'nullable|int|between:1,1000',
            'date_from'      => 'nullable|date_format:d/m/Y',
            'date_to'        => 'nullable|date_format:d/m/Y',
        ];
    }
}
