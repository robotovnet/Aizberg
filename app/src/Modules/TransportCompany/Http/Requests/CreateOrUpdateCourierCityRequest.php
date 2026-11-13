<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class CreateOrUpdateCourierCityRequest
 * @package Modules\TransportCompany\Http\Requests
 */
class CreateOrUpdateCourierCityRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'country_id'            => 'string',
        'city_id'               => 'string',
        'adding_method'         => 'string',
        'warehouses'            => 'string',
        'transport_companies'   => 'string',
        'warehouses.*'          => 'string',
        'transport_companies.*' => 'string',
    ])]
    public function rules(): array
    {
        return [
            'country_id'            => 'required|numeric|min:1|exists:countries,id',
            'city_id'               => 'required|numeric|min:1|exists:cities,id',
            'adding_method'         => 'required|in:preset,manual',
            'warehouses'            => 'nullable|array',
            'transport_companies'   => 'required|array',
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
        $prefix = 'transportcompany::courier_city.validation_messages.';

        $messages['country_id.required']          = Lang::get($prefix . 'country_id_required');
        $messages['city_id.required']             = Lang::get($prefix . 'city_id_required');
        $messages['adding_method.required']       = Lang::get($prefix . 'adding_method_required');
        $messages['warehouses.required']          = Lang::get($prefix . 'warehouses_required');
        $messages['transport_companies.required'] = Lang::get($prefix . 'transport_companies_required');
        $messages['country_id.numeric']           = Lang::get($prefix . 'country_id_numeric');
        $messages['country_id.min']               = Lang::get($prefix . 'country_id_min');
        $messages['country_id.exists']            = Lang::get($prefix . 'country_id_exists');
        $messages['city_id.numeric']              = Lang::get($prefix . 'city_id_numeric');
        $messages['city_id.min']                  = Lang::get($prefix . 'city_id_min');
        $messages['city_id.exists']               = Lang::get($prefix . 'city_id_exists');
        $messages['adding_method.in']             = Lang::get($prefix . 'adding_method_in');
        $messages['warehouses.array']             = Lang::get($prefix . 'warehouses_array');
        $messages['transport_companies.array']    = Lang::get($prefix . 'transport_companies_array');

        if ($this->has('warehouses')) {
            foreach ($this->get('warehouses') as $key => $val) {
                $messages['warehouses.' . $key . '.required'] = Lang::get($prefix . 'warehouses_required');
                $messages['warehouses.' . $key . '.numeric'] = Lang::get($prefix . 'warehouses_numeric');
                $messages['warehouses.' . $key . '.min'] = Lang::get($prefix . 'warehouses_min');
                $messages['warehouses.' . $key . '.exists'] = Lang::get($prefix . 'warehouses_exists');
            }
        } else {
            $messages['required.warehouses.*'] = Lang::get($prefix . 'warehouses_required');
            $messages['numeric.warehouses.*']  = Lang::get($prefix . 'warehouses_numeric');
            $messages['min.warehouses.*']      = Lang::get($prefix . 'warehouses_min');
            $messages['exists.warehouses.*']   = Lang::get($prefix . 'warehouses_exists');
        }

        if ($this->has('transport_companies')) {
            foreach ($this->get('transport_companies') as $key => $val) {
                $messages['transport_companies.' . $key . '.required'] =
                    Lang::get($prefix . 'transport_companies_required');
                $messages['transport_companies.' . $key . '.numeric'] =
                    Lang::get($prefix . 'transport_companies_numeric');
                $messages['transport_companies.' . $key . '.min'] =
                    Lang::get($prefix . 'transport_companies_min');
                $messages['transport_companies.' . $key . '.exists'] =
                    Lang::get($prefix . 'transport_companies_exists');
            }
        } else {
            $messages['required.transport_companies.*'] =
                Lang::get($prefix . 'transport_companies_required');
            $messages['numeric.transport_companies.*'] =
                Lang::get($prefix . 'transport_companies_numeric');
            $messages['min.transport_companies.*'] =
                Lang::get($prefix . 'transport_companies_min');
            $messages['exists.transport_companies.*'] =
                Lang::get($prefix . 'transport_companies_exists');
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
        $prefix = 'transportcompany::courier_city.validation_attributes.';

        $attributes['country_id']          = Lang::get($prefix . 'country_id');
        $attributes['city_id']             = Lang::get($prefix . 'city_id');
        $attributes['adding_method']       = Lang::get($prefix . 'adding_method');
        $attributes['warehouses']          = Lang::get($prefix . 'warehouses');
        $attributes['transport_companies'] = Lang::get($prefix . 'transport_companies');

        if ($this->has('warehouses')) {
            foreach ($this->get('warehouses') as $key => $val) {
                $attributes['warehouses.' . $key]  = Lang::get($prefix . 'warehouses');
            }
        }

        if ($this->has('transport_companies')) {
            foreach ($this->get('transport_companies') as $key => $val) {
                $attributes['transport_companies.' . $key]  = Lang::get($prefix . 'transport_companies');
            }
        }

        return $attributes;
    }
}
