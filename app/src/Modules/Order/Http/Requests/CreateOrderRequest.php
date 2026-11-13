<?php

declare(strict_types=1);

namespace Modules\Order\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreateOrderRequest
 * @package Modules\Order\Http\Requests
 */
class CreateOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'client_id' => 'required|int|min:1|exists:clients,id',
            'shop_id' => 'required|int|min:1|exists:shops,id',
            'city_id' => 'required|int|min:1|exists:cities,id',
            'payment_method_id' => 'required|int|min:1|exists:payment_methods,id',
            'transport_company_id' => 'required|int|min:1|exists:transport_companies,id',
            'delivery_point_type' => 'required|string|in:postamat,courier_city,pickup',
            'delivery_point_id' => 'required|int|min:0',
            'delivery_cost' => 'required|numeric|min:0',
            'total_products_cost' => 'required|numeric|min:0',
            'order_comment' => 'nullable|string|max:4000',
            'delivery_address' => 'required|string|max:4000',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|regex:/^\d\d{3}\d{3}\d{2}\d{2}$/',
            'client_name' => 'required|string|max:60',
            'client_surname' => 'required|string|max:60',
            'is_mobile_order' => 'required|in:Y,N',
            'referrer' => 'nullable|string|max:255',
            'bank_transfer_details' => 'nullable|string|max:4000',
            'postcode' => 'nullable|string|max:20',
            'products' => 'required|array',
            'products.*.product_id' => 'required|int|min:1|exists:products,id',
            'products.*.quantity' => 'required|int|min:1',
            'products.*.price' => 'required|numeric|min:0',
        ];
    }
}
