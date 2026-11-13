<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class CreateOrUpdatePaymentMethodRequest
 * @package Modules\TransportCompany\Http\Requests
 */
class CreateOrUpdatePaymentMethodRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'names'                       => 'string',
        'names.*.lang'                => 'string',
        'names.*.value'               => 'string',
        'additional_comments'         => 'string',
        'additional_comments.*.lang'  => 'string',
        'additional_comments.*.value' => 'string',
        'code'                        => 'string',
        'minimum_order_value'         => 'string',
        'maximum_order_value'         => 'string',
        'is_active'                   => 'string',
    ])]
    public function rules(): array
    {
        $rules = [
            'names'                       => 'required|array|min:1',
            'names.*.lang'                => 'required|string|size:2|exists:languages,code',
            'names.*.value'               => 'required|string|between:1,100',
            'additional_comments'         => 'required|array|min:1',
            'additional_comments.*.lang'  => 'required|string|size:2|exists:languages,code',
            'additional_comments.*.value' => 'nullable|string',
            'code'                        => 'required|string|between:1,30|unique:payment_methods,code,NULL,id,deleted_at,NULL',
            'minimum_order_value'         => 'required|numeric|min:0|max:999999999',
            'maximum_order_value'         => 'required|numeric|gt:minimum_order_value|max:999999999',
            'is_active'                   => 'required|in:Y,N',
            'order'                       => 'required|numeric|min:1',
        ];

        // For update
        $paymentMethodId = request()->segment(3);

        if ($paymentMethodId !== null) {
            $rules['code'] = Str::replaceFirst('NULL', $paymentMethodId, $rules['code']);
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return string[]
     */
    public function attributes(): array
    {
        $prefix = 'transportcompany::create_update_payment_method.validation_attributes.';

        $attributes['code'] = Lang::get($prefix . 'code');
        $attributes['minimum_order_value'] = Lang::get($prefix . 'minimum_order_value');
        $attributes['maximum_order_value'] = Lang::get($prefix . 'maximum_order_value');
        $attributes['is_active'] = Lang::get($prefix . 'is_active');
        $attributes['names.0.value'] = Lang::get($prefix . 'name');

        return $attributes;
    }
}
