<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class CreateOrUpdateProductMarkupRequest
 * @package Modules\TransportCompany\Http\Requests
 */
class CreateOrUpdateProductMarkupRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'payment_method_id'           => 'string',
        'transport_company_id'        => 'string',
        'is_support_courier_delivery' => 'string',
        'is_support_postamats'        => 'string',
        'is_support_pickup_points'    => 'string',
        'markup'                      => 'string',
        'markup_type'                 => 'string',
        'is_active'                   => 'string',
    ])]
    public function rules(): array
    {
        return [
            'payment_method_id'           => 'required|numeric|min:1|exists:payment_methods,id',
            'transport_company_id'        => 'nullable|numeric|min:1|exists:transport_companies,id',
            'is_support_courier_delivery' => 'required|in:Y,N',
            'is_support_postamats'        => 'required|in:Y,N',
            'is_support_pickup_points'    => 'required|in:Y,N',
            'markup'                      => 'nullable|numeric|min:1|max:50000',
            'markup_type'                 => 'required|in:percentage,fixed_value',
            'is_active'                   => 'required|in:Y,N',
        ];
    }
}
