<?php

declare(strict_types=1);

namespace Modules\City\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class CreateOrUpdateCityRequest
 * @package Modules\City\Http\Requests
 */
class CreateOrUpdateCityRequest extends FormRequest
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
        'timezone'      => 'string',
        'is_default'    => 'string',
        'country_code'  => 'string',
    ])]
    public function rules(): array
    {
        return [
            'names'         => 'required',
            'names.*.lang'  => 'required|string|size:2|exists:languages,code',
            'names.*.value' => 'required|string|between:2,60',
            'timezone'      => 'required|timezone',
            'is_default'    => 'required|in:Y,N',
            'country_code'  => 'required|string|exists:countries,code',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return string[]
     */
    public function messages(): array
    {
        $prefix = 'city::create_update.validation_messages.';

        $messages['names.required']        = Lang::get($prefix . 'names_required');
        $messages['timezone.required']     = Lang::get($prefix . 'timezone_required');
        $messages['timezone.timezone']     = Lang::get($prefix . 'timezone_timezone');
        $messages['is_default.required']   = Lang::get($prefix . 'is_default_required');
        $messages['is_default.in']         = Lang::get($prefix . 'is_default_in');
        $messages['country_code.required'] = Lang::get($prefix . 'country_code_required');
        $messages['country_code.exists']   = Lang::get($prefix . 'country_code_exists');

        if ($this->has('names')) {
            foreach ($this->get('names') as $key => $val) {
                $messages['names.' . $key . '.lang.required'] = Lang::get($prefix . 'names_lang_required');
                $messages['names.' . $key . '.lang.size']     = Lang::get($prefix . 'names_lang_size');
                $messages['names.' . $key . '.lang.exists']   = Lang::get($prefix . 'names_lang_exists');

                $messages['names.' . $key . '.value.required'] = Lang::get($prefix . 'names_value_required');
                $messages['names.' . $key . '.value.between']  = Lang::get($prefix . 'names_value_between');
            }
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
        $prefix = 'city::create_update.validation_attributes.';

        $attributes['names']        = Lang::get($prefix . 'names');
        $attributes['timezone']     = Lang::get($prefix . 'timezone');
        $attributes['is_default']   = Lang::get($prefix . 'is_default');
        $attributes['country_code'] = Lang::get($prefix . 'country_code');

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
