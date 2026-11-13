<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class OrderTransportCompanyRequest
 * @package Modules\TransportCompany\Http\Requests
 */
class OrderTransportCompanyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'orders'         => 'string',
        'orders.*.tcId'  => 'string',
        'orders.*.order' => 'string',
    ])]
    public function rules(): array
    {
        return [
            'orders'            => 'required|array',
            'orders.*.tcId'     => 'required|numeric|min:1|exists:transport_companies,id',
            'orders.*.order'    => 'required|numeric|min:1',
        ];
    }
}
