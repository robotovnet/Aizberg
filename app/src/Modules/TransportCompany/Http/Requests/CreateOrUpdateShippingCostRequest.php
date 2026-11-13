<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Support\Facades\Lang;

/**
 * Class CreateOrUpdateShippingCostRequest
 * @package Modules\TransportCompany\Http\Requests
 */
class CreateOrUpdateShippingCostRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'transport_company_id'        => 'string',
        'shipping_methods'            => 'string',
        'rates'                       => 'string',
        'rates.*.value'               => 'string',
        'rates.*.minimum_order_value' => 'string',
        'rates.*.maximum_order_value' => 'string',
    ])]
    public function rules(): array
    {
        return [
            'transport_company_id'        => 'required|numeric|min:1|exists:transport_companies,id',
            'shipping_methods'            => 'required|array|min:1|in:courier_delivery,postamats,pickup_points',
            'rates'                       => 'required|array|min:1',
            'rates.*.value'               => 'required|numeric|min:0',
            'rates.*.minimum_order_value' => 'required|numeric|min:1',
            'rates.*.maximum_order_value' => 'required|numeric|gt:rates.*.minimum_order_value',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return string[]
     */
    public function messages(): array
    {
        $prefix = 'transportcompany::shipping_cost.validation_messages.';

        $messages = [];

        if ($this->has('rates')) {
            foreach ($this->get('rates') as $key => $val) {
                $messages['rates.' . $key . '.minimum_order_value.min'] = Lang::get($prefix . 'rates_minimum_order_value_min');
                $messages['rates.' . $key . '.maximum_order_value.gt'] = Lang::get($prefix . 'rates_maximum_order_value_gt');
            }
        }

        return $messages;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return string[]
     */
    public function attributes(): array
    {
        $prefix = 'transportcompany::shipping_cost.validation_attributes.';

        $attributes = [];

        $attributes['transport_company_id'] = Lang::get($prefix . 'transport_company_id');
        $attributes['shipping_methods'] = Lang::get($prefix . 'shipping_methods');

        if ($this->has('rates')) {
            foreach ($this->get('rates') as $key => $val) {
                $attributes['rates.' . $key . '.minimum_order_value']  = Lang::get($prefix . 'minimum_order_value');
                $attributes['rates.' . $key . '.maximum_order_value']  = Lang::get($prefix . 'maximum_order_value');
            }
        }

        return $attributes;
    }
}
