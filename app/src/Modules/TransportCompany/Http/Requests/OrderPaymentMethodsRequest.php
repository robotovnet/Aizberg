<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class OrderPaymentMethodsRequest
 * @package Modules\TransportCompany\Http\Requests
 */
class OrderPaymentMethodsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'orders'         => 'string',
        'orders.*.pmId'  => 'string',
        'orders.*.order' => 'string',
    ])]
    public function rules(): array
    {
        return [
            'orders'            => 'required|array',
            'orders.*.pmId'     => 'required|numeric|min:1|exists:payment_methods,id',
            'orders.*.order'    => 'required|numeric|min:1',
        ];
    }
}
