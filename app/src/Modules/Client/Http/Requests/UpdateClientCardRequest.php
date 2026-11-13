<?php

declare(strict_types=1);

namespace Modules\Client\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class UpdateClientCardRequest
 * @package Modules\Client\Http\Requests
 */
class UpdateClientCardRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'first_name'      => 'string',
        'last_name'       => 'string',
        'email'           => 'string',
        'phone'           => 'string',
        'country_code'    => 'string',
        'city_id'         => 'string',
        'staff_id'        => 'string',
        'client_type_id'  => 'string',
        'permanent_type'  => 'string',
    ])]
    public function rules(): array
    {
        $clientId = request()->segment(3);

        return [
            'first_name'     => 'nullable|string|between:1,60',
            'last_name'      => 'nullable|string|between:1,60',
            'email'          => 'required|email|max:100', // |unique:clients,email,' . $clientId . ',id,deleted_at,NULL
            'phone'          => 'required|digits_between:11,20', // |unique:clients,phone,' . $clientId . ',id,deleted_at,NULL
            'country_code'   => 'nullable|string|size:2|exists:countries,code',
            'city_id'        => 'required|numeric|min:1|exists:cities,id',
            'staff_id'       => 'nullable|numeric|min:1|exists:staff,id',
            'client_type_id' => 'required|numeric|min:1|exists:client_types,id',
            'permanent_type' => 'required|string|in:Y,N',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return string[]
     */
    public function attributes(): array
    {
        $prefix = 'client::create_update_client_card.validation_attributes.';

        $attributes['first_name'] = Lang::get($prefix . 'first_name');
        $attributes['last_name'] = Lang::get($prefix . 'last_name');
        $attributes['email'] = Lang::get($prefix . 'email');
        $attributes['phone'] = Lang::get($prefix . 'phone');
        $attributes['country_code'] = Lang::get($prefix . 'country_code');
        $attributes['city_id'] = Lang::get($prefix . 'city_id');
        $attributes['staff_id'] = Lang::get($prefix . 'staff_id');
        $attributes['client_type_id'] = Lang::get($prefix . 'client_type_id');
        $attributes['permanent_type'] = Lang::get($prefix . 'permanent_type');

        return $attributes;
    }
}
