<?php

declare(strict_types=1);

namespace Modules\Currency\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Support\Str;

/**
 * Class CreateOrUpdatePriceTypeRequest
 * @package Modules\Currency\Http\Requests
 */
class CreateOrUpdateCurrencyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'names'         => 'string',
        'names.*.lang'  => 'string',
        'names.*.value' => 'string',
        'code'          => 'string',
        'symbol'        => 'string',
        'is_default'    => 'string',
        'extra_charge'  => 'string',
        'country_code'  => 'string',
    ])]
    public function rules(): array
    {
        $rules = [
            'names'         => 'required',
            'names.*.lang'  => 'required|string|size:2|exists:languages,code',
            'names.*.value' => 'required|string|between:2,60',
            'code'          => 'required|string|size:3',
            'symbol'        => 'required|string|between:1,5',
            'is_default'    => 'required|in:Y,N',
            'extra_charge'  => 'required|numeric|between:0,100',
            'country_code'  => 'required|string|exists:countries,code|' .
                              'unique:currencies,country_code,NULL,id,deleted_at,NULL',
        ];

        // For update
        $currencyId = request()->segment(2);

        if ($currencyId !== null) {
            $rules['country_code'] = Str::replaceFirst('NULL', $currencyId, $rules['country_code']);
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
        $prefix = 'currency::create_update.validation_messages.';

        $messages['names.required']        = Lang::get($prefix . 'names_required');
        $messages['is_default.required']   = Lang::get($prefix . 'is_default_required');
        $messages['is_default.in']         = Lang::get($prefix . 'is_default_in');
        $messages['code.required']         = Lang::get($prefix . 'code_required');
        $messages['code.size']             = Lang::get($prefix . 'code_size');
        $messages['symbol.required']       = Lang::get($prefix . 'symbol_required');
        $messages['symbol.between']        = Lang::get($prefix . 'symbol_between');
        $messages['extra_charge.required'] = Lang::get($prefix . 'extra_charge_required');
        $messages['extra_charge.numeric']  = Lang::get($prefix . 'extra_charge_numeric');
        $messages['extra_charge.between']  = Lang::get($prefix . 'extra_charge_between');
        $messages['country_code.required'] = Lang::get($prefix . 'country_code_required');
        $messages['country_code.exists']   = Lang::get($prefix . 'country_code_exists');
        $messages['country_code.unique']   = Lang::get($prefix . 'country_code_unique');

        if ($this->has('names')) {
            foreach ($this->get('names') as $key => $val) {
                $messages['names.' . $key . '.lang.required'] = Lang::get($prefix . 'names_lang_required');
                $messages['names.' . $key . '.lang.size']     = Lang::get($prefix . 'names_lang_size');
                $messages['names.' . $key . '.lang.exists']   = Lang::get($prefix . 'names_lang_exists');

                $messages['names.' . $key . '.value.required'] = Lang::get($prefix . 'names_value_required');
                $messages['names.' . $key . '.value.between']  = Lang::get($prefix . 'names_value_between');
            }
        } else {
            $messages['required.names.*.lang'] = Lang::get($prefix . 'names_lang_required');
            $messages['size.names.*.lang']     = Lang::get($prefix . 'names_lang_size');
            $messages['exists.names.*.lang']   = Lang::get($prefix . 'names_lang_exists');

            $messages['required.names.*.value'] = Lang::get($prefix . 'names_value_required');
            $messages['between.names.*.value']  = Lang::get($prefix . 'names_value_between');
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
        $prefix = 'currency::create_update.validation_attributes.';

        $attributes['names']        = Lang::get($prefix . 'names');
        $attributes['code']         = Lang::get($prefix . 'code');
        $attributes['symbol']       = Lang::get($prefix . 'symbol');
        $attributes['extra_charge'] = Lang::get($prefix . 'extra_charge');
        $attributes['country_code'] = Lang::get($prefix . 'country_code');
        $attributes['is_default']   = Lang::get($prefix . 'is_default');
        $attributes['names.0.value'] = Lang::get($prefix . 'names');

        if ($this->has('names')) {
            foreach ($this->get('names') as $key => $val) {
                $attributes['names.' . $key . '.lang']  = Lang::get($prefix . 'names_lang');
                $attributes['names.' . $key . '.value'] = Lang::get($prefix . 'names');
            }
        }

        return $attributes;
    }
}
