<?php

declare(strict_types=1);

namespace Modules\Shop\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class CreateOrUpdateShopRequest
 * @package Modules\Shop\Http\Requests
 */
class CreateOrUpdateShopRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'subdomain'             => 'string',
        'city_id'               => 'string',
        'city_name_dative_case' => 'string',
        'phone'                 => 'string',
        'is_active'             => 'string',
        'country_id'            => 'string',
    ])]
    public function rules(): array
    {
        $rules = [
            'subdomain'             => 'nullable|string|between:1,30|unique:shops,subdomain,NULL,id,deleted_at,NULL',
            'country_id'            => 'required|numeric|min:1|exists:countries,id',
            'city_id'               => 'required|numeric|min:1|exists:cities,id|unique:shops,city_id,NULL,id,deleted_at,NULL',
            'city_name_dative_case' => 'required|string|between:2,60',
            'phone'                 => 'required|digits_between:11,20',
            'is_active'             => 'required|in:Y,N',
        ];

        // For update
        $shopId = request()->segment(2);

        if ($shopId !== null) {
            $rules['subdomain'] = Str::replaceFirst('NULL', $shopId, $rules['subdomain']);
            $rules['city_id']   = Str::replaceFirst('NULL', $shopId, $rules['city_id']);
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
        $prefix = 'shop::create_update.validation_messages.';

        $messages['subdomain.between']              = Lang::get($prefix . 'subdomain_between');
        $messages['subdomain.unique']               = Lang::get($prefix . 'subdomain_unique');
        $messages['country_id.min']                 = Lang::get($prefix . 'country_id_min');
        $messages['country_id.required']            = Lang::get($prefix . 'country_id_required');
        $messages['city_id.min']                    = Lang::get($prefix . 'city_id_min');
        $messages['city_id.required']               = Lang::get($prefix . 'city_id_required');
        $messages['city_id.numeric']                = Lang::get($prefix . 'city_id_numeric');
        $messages['city_id.exists']                 = Lang::get($prefix . 'city_id_exists');
        $messages['city_name_dative_case.required'] = Lang::get($prefix . 'city_name_dative_case_required');
        $messages['city_name_dative_case.between']  = Lang::get($prefix . 'city_name_dative_case_between');
        $messages['phone.required']                 = Lang::get($prefix . 'phone_required');
        $messages['phone.digits_between']           = Lang::get($prefix . 'phone_digits_between');
        $messages['is_active.required']             = Lang::get($prefix . 'is_active_required');
        $messages['is_active.in']                   = Lang::get($prefix . 'is_active_in');

        return $messages;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return string[]
     */
    public function attributes(): array
    {
        $prefix = 'shop::create_update.validation_attributes.';

        $attributes['subdomain']             = Lang::get($prefix . 'subdomain');
        $attributes['city_id']               = Lang::get($prefix . 'city_id');
        $attributes['city_name_dative_case'] = Lang::get($prefix . 'city_name_dative_case');
        $attributes['phone']                 = Lang::get($prefix . 'phone');
        $attributes['is_active']             = Lang::get($prefix . 'is_active');
        $attributes['country']               = Lang::get($prefix . 'country');

        return $attributes;
    }
}
