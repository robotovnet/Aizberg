<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Support\Facades\Lang;

/**
 * Class CreateOrUpdatePickupPointRequest
 * @package Modules\TransportCompany\Http\Requests
 */
class CreateOrUpdatePickupPointRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'name'                  => 'string',
        'country_id'            => 'string',
        'city_id'               => 'string',
        'adding_method'         => 'string',
        'address'               => 'string',
        'address_comment'       => 'string',
        'payment_by_card'       => 'string',
        'payment_by_cash'       => 'string',
        'postal_code'           => 'string',
        'phone'                 => 'string',
        'lat'                   => 'string',
        'lng'                   => 'string',
        'metro_station'         => 'string',
        'work_time'             => 'string',
        'break_work_time'       => 'string',
        'warehouses'            => 'string',
        'transport_companies'   => 'string',
        'warehouses.*'          => 'string',
        'transport_companies.*' => 'string',
    ])]
    public function rules(): array
    {
        return [
            'name'                  => 'required|string|min:1|max:100',
            'country_id'            => 'required|numeric|min:1|exists:countries,id',
            'city_id'               => 'required|numeric|min:1|exists:cities,id',
            'adding_method'         => 'required|in:preset,manual',
            'address'               => 'required|string|min:1',
            'address_comment'       => 'nullable|string',
            'payment_by_card'       => 'required|in:Y,N',
            'payment_by_cash'       => 'required|in:Y,N',
            'postal_code'           => 'nullable|digits_between:6,20',
            'phone'                 => 'nullable|digits_between:11,20',
            'lat'                   => 'required|numeric|min:-90|max:90',
            'lng'                   => 'required|numeric|min:-180|max:180',
            'metro_station'         => 'nullable|string|min:1|max:100',
            'work_time'             => 'required|json',
            'break_work_time'       => 'required|json',
            'warehouses'            => 'nullable|array',
            'transport_companies'   => 'nullable|array',
            'warehouses.*'          => 'required|numeric|min:1|exists:warehouses,id',
            'transport_companies.*' => 'required|numeric|min:1|exists:transport_companies,id',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return string[]
     */
    public function messages(): array
    {
        $prefix = 'transportcompany::create_update_pickup_point.validation_messages.';

        $messages['country_id.min']  = Lang::get($prefix . 'country_id_min');
        $messages['city_id.min']     = Lang::get($prefix . 'city_id_min');

        return $messages;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return string[]
     */
    public function attributes(): array
    {
        $prefix = 'transportcompany::create_update_pickup_point.validation_attributes.';

        $attributes['name'] = Lang::get($prefix . 'name');
        $attributes['country_id'] = Lang::get($prefix . 'country_id');
        $attributes['city_id'] = Lang::get($prefix . 'city_id');
        $attributes['adding_method'] = Lang::get($prefix . 'adding_method');
        $attributes['address'] = Lang::get($prefix . 'address');
        $attributes['address_comment'] = Lang::get($prefix . 'address_comment');
        $attributes['payment_by_card'] = Lang::get($prefix . 'payment_by_card');
        $attributes['payment_by_cash'] = Lang::get($prefix . 'payment_by_cash');
        $attributes['postal_code'] = Lang::get($prefix . 'postal_code');
        $attributes['phone'] = Lang::get($prefix . 'phone');
        $attributes['lat'] = Lang::get($prefix . 'lat');
        $attributes['lng'] = Lang::get($prefix . 'lng');
        $attributes['metro_station'] = Lang::get($prefix . 'metro_station');
        $attributes['work_time'] = Lang::get($prefix . 'work_time');
        $attributes['break_work_time'] = Lang::get($prefix . 'break_work_time');
        $attributes['transport_companies'] = Lang::get($prefix . 'transport_companies');
        $attributes['warehouses'] = Lang::get($prefix . 'warehouses');

        return $attributes;
    }
}
