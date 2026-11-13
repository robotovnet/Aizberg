<?php

declare(strict_types=1);

namespace Modules\ClientType\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class CreateOrUpdateCityRequest
 * @package Modules\ClientType\Http\Requests
 */
class CreateOrUpdateClientTypeRequest extends FormRequest
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
        'price_type_id' => 'string',
        'is_default'    => 'string',
    ])]
    public function rules(): array
    {
        return [
            'names'         => 'required',
            'names.*.lang'  => 'required|string|size:2|exists:languages,code',
            'names.*.value' => 'required|string|between:2,60',
            'price_type_id' => 'required|numeric|exists:price_types,id',
            'is_default'    => 'required|in:Y,N',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return string[]
     */
    public function messages(): array
    {
        $prefix = 'clienttype::create_update.validation_messages.';

        $messages['names.required']         = Lang::get($prefix . 'names_required');
        $messages['is_default.required']    = Lang::get($prefix . 'is_default_required');
        $messages['is_default.in']          = Lang::get($prefix . 'is_default_in');
        $messages['price_type_id.required'] = Lang::get($prefix . 'price_type_id_required');
        $messages['price_type_id.numeric']  = Lang::get($prefix . 'price_type_id_numeric');
        $messages['price_type_id.exists']   = Lang::get($prefix . 'price_type_id_exists');

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
        $prefix = 'clienttype::create_update.validation_attributes.';

        $attributes['names']         = Lang::get($prefix . 'names');
        $attributes['price_type_id'] = Lang::get($prefix . 'price_type_id');
        $attributes['is_default']    = Lang::get($prefix . 'is_default');

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
