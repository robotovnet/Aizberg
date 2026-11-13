<?php

declare(strict_types=1);

namespace Modules\PriceType\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Support\Str;

/**
 * Class CreateOrUpdatePriceTypeRequest
 * @package Modules\Staff\Http\Requests
 */
class CreateOrUpdatePriceTypeRequest extends FormRequest
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
    ])]
    public function rules(): array
    {
        $rules = [
            'names'         => 'required',
            'names.*.lang'  => 'required|string|size:2|exists:languages,code',
            'names.*.value' => 'required|string|between:2,60',
            'code'          => 'required|string|max:20|unique:price_types,code,NULL,id,deleted_at,NULL',
        ];

        // For update
        $priceTypeId = request()->segment(3);

        if ($priceTypeId !== null) {
            $rules['code'] = Str::replaceFirst('NULL', $priceTypeId, $rules['code']);
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
        $prefix = 'pricetype::create_update.validation_messages.';

        $messages['names.required'] = Lang::get($prefix . 'names_required');
        $messages['code.required'] = Lang::get($prefix . 'code_required');
        $messages['code.max']      = Lang::get($prefix . 'code_max');
        $messages['code.unique']   = Lang::get($prefix . 'code_unique');

        if ($this->has('names')) {
            foreach ($this->get('names') as $key => $val) {
                $messages['names.' . $key . '.lang.required'] = Lang::get($prefix . 'names_lang_required');
                $messages['names.' . $key . '.lang.size']     = Lang::get($prefix . 'names_lang_size');
                $messages['names.' . $key . '.lang.exists']   = Lang::get($prefix . 'names_lang_exists');

                $messages['names.' . $key . '.value.required'] = Lang::get($prefix . 'names_value_required');
                $messages['names.' . $key . '.value.between']  = Lang::get($prefix . 'names_value_between');
            }
        } else {
            $messages['required.names.*.lang']  = Lang::get($prefix . 'names_lang_required');
            $messages['size.names.*.lang']      = Lang::get($prefix . 'names_lang_size');
            $messages['exists.names.*.lang']    = Lang::get($prefix . 'names_lang_exists');
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
        $prefix = 'pricetype::create_update.validation_attributes.';

        $attributes['names'] = Lang::get($prefix . 'names');
        $attributes['code']  = Lang::get($prefix . 'code');

        if ($this->has('names')) {
            foreach ($this->get('names') as $key => $val) {
                $attributes['names.' . $key . '.lang']  = Lang::get($prefix . 'names_lang');
                $attributes['names.' . $key . '.value'] = Lang::get($prefix . 'names');
            }
        }

        $attributes['names.0.value'] = Lang::get($prefix . 'names');

        return $attributes;
    }
}
