<?php

declare(strict_types=1);

namespace Modules\Order\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class ChangeTracknumberRequest
 * @package Modules\Order\Http\Requests
 */
class ChangeTracknumberRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'tracknumber' => 'string',
    ])]
    public function rules(): array
    {
        return [
            'tracknumber' => 'required|string|min:2',
        ];
    }
}
