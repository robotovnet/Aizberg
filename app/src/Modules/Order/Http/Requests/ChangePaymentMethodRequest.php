<?php

declare(strict_types=1);

namespace Modules\Order\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class ChangePaymentMethodRequest
 * @package Modules\Order\Http\Requests
 */
class ChangePaymentMethodRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'payment_method_id' => 'string',
    ])]
    public function rules(): array
    {
        return [
            'payment_method_id' => 'required|int|min:1|exists:payment_methods,id',
        ];
    }
}
