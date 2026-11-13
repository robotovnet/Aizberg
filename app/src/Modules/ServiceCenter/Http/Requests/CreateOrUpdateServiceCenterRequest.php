<?php

declare(strict_types=1);

namespace Modules\ServiceCenter\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Support\Facades\Lang;

/**
 * Class CreateOrUpdateServiceCenterRequest
 * @package Modules\ServiceCenter\Http\Requests
 */
class CreateOrUpdateServiceCenterRequest extends FormRequest
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
        'address'               => 'string',
        'address_comment'       => 'string',
        'postal_code'           => 'string',
        'site'                  => 'string',
        'phone'                 => 'string',
        'lat'                   => 'string',
        'lng'                   => 'string',
        'metro_station'         => 'string',
        'work_time'             => 'string',
        'break_work_time'       => 'string',
    ])]
    public function rules(): array
    {
        return [
            'name'                  => 'required|string|min:1|max:100',
            'country_id'            => 'required|numeric|min:1|exists:countries,id',
            'city_id'               => 'required|numeric|min:1|exists:cities,id',
            'address'               => 'required|string|min:1',
            'address_comment'       => 'nullable|string',
            'postal_code'           => 'nullable|digits_between:6,20',
            'site'                  => 'nullable|url',
            'phone'                 => 'required|digits_between:11,20',
            'lat'                   => 'required|numeric|min:-90|max:90',
            'lng'                   => 'required|numeric|min:-180|max:180',
            'metro_station'         => 'nullable|string|min:1|max:100',
            'work_time'             => 'required|json',
            'break_work_time'       => 'required|json',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return string[]
     */
    public function messages(): array
    {
        $prefix = 'servicecenter::create_update_service_center.validation_messages.';

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
        $prefix = 'servicecenter::create_update_service_center.validation_attributes.';

        $attributes['name'] = Lang::get($prefix . 'name');
        $attributes['country_id'] = Lang::get($prefix . 'country_id');
        $attributes['city_id'] = Lang::get($prefix . 'city_id');
        $attributes['adding_method'] = Lang::get($prefix . 'adding_method');
        $attributes['address'] = Lang::get($prefix . 'address');
        $attributes['address_comment'] = Lang::get($prefix . 'address_comment');
        $attributes['postal_code'] = Lang::get($prefix . 'postal_code');
        $attributes['site'] = Lang::get($prefix . 'site');
        $attributes['phone'] = Lang::get($prefix . 'phone');
        $attributes['lat'] = Lang::get($prefix . 'lat');
        $attributes['lng'] = Lang::get($prefix . 'lng');
        $attributes['metro_station'] = Lang::get($prefix . 'metro_station');
        $attributes['work_time'] = Lang::get($prefix . 'work_time');
        $attributes['break_work_time'] = Lang::get($prefix . 'break_work_time');

        return $attributes;
    }
}
