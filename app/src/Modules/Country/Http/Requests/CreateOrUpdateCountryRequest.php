<?php

declare(strict_types=1);

namespace Modules\Country\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class CreateOrUpdateCountryRequest
 * @package Modules\Country\Http\Requests
 */
class CreateOrUpdateCountryRequest extends FormRequest
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
        'language'      => 'string',
        'is_default'    => 'string',
        'code'          => 'string',
    ])]
    public function rules(): array
    {
        $rules = [
            'names'         => 'required',
            'names.*.lang'  => 'required|string|size:2|exists:languages,code',
            'names.*.value' => 'required|string|between:2,60',
            'language'      => 'required|string|size:2|exists:languages,code',
            'is_default'    => 'required|in:Y,N',
            'code'          => 'required|string|size:2|unique:countries,code,NULL,id,deleted_at,NULL',
        ];

        // For update
        $countryTypeId = request()->segment(2);

        if ($countryTypeId !== null) {
            $rules['code'] = Str::replaceFirst('NULL', $countryTypeId, $rules['code']);
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
        $prefix = 'country::create_update.validation_messages.';

        $messages['names.required']      = Lang::get($prefix . 'names_required');
        $messages['is_default.required'] = Lang::get($prefix . 'is_default_required');
        $messages['is_default.in']       = Lang::get($prefix . 'is_default_in');
        $messages['language.required']   = Lang::get($prefix . 'language_required');
        $messages['language.size']       = Lang::get($prefix . 'language_size');
        $messages['language.exists']     = Lang::get($prefix . 'language_exists');
        $messages['code.required']       = Lang::get($prefix . 'code_required');
        $messages['code.size']           = Lang::get($prefix . 'code_size');

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
        $prefix = 'country::create_update.validation_attributes.';

        $attributes['names']      = Lang::get($prefix . 'names');
        $attributes['language']   = Lang::get($prefix . 'language');
        $attributes['code']       = Lang::get($prefix . 'code');
        $attributes['is_default'] = Lang::get($prefix . 'is_default');

        if ($this->has('names')) {
            foreach ($this->get('names') as $key => $val) {
                $attributes['names.' . $key . '.lang']  = Lang::get($prefix . 'names_lang');
                $attributes['names.' . $key . '.value'] = Lang::get($prefix . 'names');
            }
        }

        $attributes['names.0.value']  = Lang::get($prefix . 'names');
        return $attributes;
    }
}
