<?php

declare(strict_types=1);

namespace Modules\MatrixType\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;

/**
 * Class CreateOrUpdateMatrixTypeRequest
 * @package Modules\MatrixType\Http\Requests
 */
class CreateOrUpdateMatrixTypeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'margin_up_to_BP' => 'required|numeric|min:0',
            'EXW_price' => 'required|numeric|min:0',
            'weight_in_kg' => 'required|numeric|min:0',
            'MRP_markup' => 'required|numeric|min:0',
            'RRP_markup' => 'required|numeric|min:0',
            'margin_up_to_P2' => 'required|numeric|min:0',
            'margin_up_to_P3' => 'required|numeric|min:0',
            'margin_up_to_P4' => 'required|numeric|min:0',
            'mark_up_contract' => 'required|numeric|min:0',
        ];

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return string[]
     */
    public function messages(): array
    {
        $prefix = 'matrix_type::create_update.validation_messages.';

        $messages['name.required'] = Lang::get($prefix . 'name_required');
        $messages['margin_up_to_BP.required'] = Lang::get($prefix . 'margin_up_to_BP_required');
        $messages['EXW_price.required']  = Lang::get($prefix . 'EXW_price_required');
        $messages['weight_in_kg.required']   = Lang::get($prefix . 'weight_in_kg_required');
        $messages['MRP_markup.required']   = Lang::get($prefix . 'MRP_markup_required');
        $messages['RRP_markup.required']   = Lang::get($prefix . 'RRP_markup_required');
        $messages['margin_up_to_P2.required']   = Lang::get($prefix . 'margin_up_to_P2_required');
        $messages['margin_up_to_P3.required']   = Lang::get($prefix . 'margin_up_to_P3_required');
        $messages['margin_up_to_P4.required']   = Lang::get($prefix . 'margin_up_to_P4_required');
        $messages['mark_up_contract.required']   = Lang::get($prefix . 'mark_up_contract_required');

        return $messages;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return string[]
     */
    public function attributes(): array
    {
        $prefix = 'matrix_type::create_update.validation_attributes.';

        $attributes = [];

        $attributes['name'] = Lang::get($prefix . 'name');
        $attributes['margin_up_to_BP'] = Lang::get($prefix . 'margin_up_to_BP');
        $attributes['EXW_price'] = Lang::get($prefix . 'EXW_price');
        $attributes['weight_in_kg'] = Lang::get($prefix . 'weight_in_kg');
        $attributes['MRP_markup'] = Lang::get($prefix . 'MRP_markup');
        $attributes['RRP_markup'] = Lang::get($prefix . 'RRP_markup');
        $attributes['margin_up_to_P2'] = Lang::get($prefix . 'margin_up_to_P2');
        $attributes['margin_up_to_P3'] = Lang::get($prefix . 'margin_up_to_P3');
        $attributes['margin_up_to_P4'] = Lang::get($prefix . 'margin_up_to_P4');
        $attributes['mark_up_contract'] = Lang::get($prefix . 'mark_up_contract');

        return $attributes;
    }
}
