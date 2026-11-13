<?php

declare(strict_types=1);

namespace Modules\Ad\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Support\Facades\Lang;

/**
 * Class CreateOrUpdateYmlRequest
 * @package Modules\Ad\Http\Requests
 */
class CreateOrUpdateYmlRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'shop_name'     => 'string',
        'company_name'  => 'string',
        'shop_domain'   => 'string',
        'shop_id'       => 'string',
        'warehouses'    => 'string',
        'warehouses.*'  => 'string',
    ])]
    public function rules(): array
    {
        return [
            'shop_name'     => 'required|string|between:1,60',
            'company_name'  => 'required|string|between:1,60',
            'shop_domain'   => 'required|url',
            'shop_id'       => 'required|integer|min:1|exists:shops,id',
            'warehouses'    => 'nullable|array',
            'warehouses.*'  => 'required|integer|min:1|exists:warehouses,id',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return string[]
     */
    public function messages(): array
    {
        $prefix = 'ad::create_update_yml.validation_messages.';

        $messages['shop_id.min']  = Lang::get($prefix . 'shop_id_min');

        return $messages;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return string[]
     */
    public function attributes(): array
    {
        $prefix = 'ad::create_update_yml.validation_attributes.';

        $attributes['shop_name']    = Lang::get($prefix . 'shop_name');
        $attributes['company_name'] = Lang::get($prefix . 'company_name');
        $attributes['shop_domain']  = Lang::get($prefix . 'shop_domain');
        $attributes['shop_id']      = Lang::get($prefix . 'shop_id');
        $attributes['warehouses']   = Lang::get($prefix . 'warehouses');
        //$attributes['warehouses.*'] = Lang::get($prefix . 'name');

        return $attributes;
    }
}

