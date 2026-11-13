<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class CreateOrUpdateWarehouseRequest
 * @package Modules\TransportCompany\Http\Requests
 */
class CreateOrUpdateWarehouseRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'names'                  => 'string',
        'names.*.lang'           => 'string',
        'names.*.value'          => 'string',
        'label_1c'               => 'string',
        'code'                   => 'string',
        'priority'               => 'string',
        'is_active'              => 'string',
        'for_all_courier_cities' => 'string',
        'for_all_postamats'      => 'string',
        'for_all_pickup_points'  => 'string',
    ])]
    public function rules(): array
    {
        $rules = [
            'names'                  => 'required|array|min:1',
            'names.*.lang'           => 'required|string|size:2|exists:languages,code',
            'names.*.value'          => 'required|string|between:2,60',
            'label_1c'               => 'required|string|between:1,30|unique:warehouses,label_1c,NULL,id,deleted_at,NULL',
            'code'                   => 'required|string|between:1,30|unique:warehouses,code,NULL,id,deleted_at,NULL',
            'priority'               => 'required|numeric|min:1|max:999999999',
            'is_active'              => 'required|in:Y,N',
            'transport_companies'                          => 'present|array',
            'transport_companies.*.transport_company_id'   => 'required|numeric|min:1|exists:transport_companies,id',
            'transport_companies.*.for_all_courier_cities' => 'required|in:Y,N',
            'transport_companies.*.for_all_postamats'      => 'required|in:Y,N',
            'transport_companies.*.for_all_pickup_points'  => 'required|in:Y,N',
        ];

        // For update
        $warehouseId = request()->segment(3);

        if ($warehouseId !== null) {
            $rules['label_1c'] = Str::replaceFirst('NULL', $warehouseId, $rules['label_1c']);
            $rules['code']     = Str::replaceFirst('NULL', $warehouseId, $rules['code']);
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return string[]
     */
    public function messages(): array
    {
        $prefix = 'transportcompany::create_update_warehouse.validation_messages.';

        $messages['transport_companies.required'] = Lang::get($prefix . 'transport_companies_required');

        return $messages;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return string[]
     */
    public function attributes(): array
    {
        $prefix = 'transportcompany::create_update_warehouse.validation_attributes.';

        $attributes['label_1c'] = Lang::get($prefix . 'label_1c');
        $attributes['code'] = Lang::get($prefix . 'code');
        $attributes['priority'] = Lang::get($prefix . 'priority');
        $attributes['is_active'] = Lang::get($prefix . 'is_active');
        $attributes['transport_companies'] = Lang::get($prefix . 'transport_companies');

        $attributes['names.0.value'] = Lang::get($prefix . 'name');

        return $attributes;
    }
}
