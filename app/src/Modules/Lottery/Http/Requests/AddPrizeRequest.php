<?php

declare(strict_types=1);

namespace Modules\Lottery\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
class AddPrizeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'product_vendor_code' => 'required|string|exists:products,vendor_code',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return string[]
     */
    public function messages(): array
    {
        $prefix = 'lottery::add_prize.validation_messages.';

        $messages['product_vendor_code.required']  = Lang::get($prefix . 'product_vendor_code_required');
        $messages['product_vendor_code.exists']    = Lang::get($prefix . 'product_vendor_code_exists');

        return $messages;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return string[]
     */
    public function attributes(): array
    {
        $prefix = 'lottery::add_prize.validation_attributes.';

        $attributes['product_vendor_code'] = Lang::get($prefix . 'product_vendor_code');
        
        return $attributes;
    }
}
