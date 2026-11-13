<?php

declare(strict_types=1);

namespace Modules\OrderOneClick\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class SetCatalogSectionsRequest
 * @package Modules\OrderOneClick\Http\Requests
 */
class SetSubscribersRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'subscribers'        => 'string',
        'subscribers.*'      => 'string',

    ])]
    public function rules(): array
    {
        return [
            'subscribers'   => 'required|array',
            'subscribers.*' => 'email:rfc',
        ];
    }
}
