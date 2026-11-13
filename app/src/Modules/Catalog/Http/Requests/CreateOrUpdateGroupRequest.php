<?php

declare(strict_types=1);

namespace Modules\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;

/**
 * Class CreateOrUpdateGroupRequest
 * @package Modules\Catalog\Http\Requests
 */
class CreateOrUpdateGroupRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'plural_names'                      => 'required|array|min:1',
            'plural_names.*.lang'               => 'required|string|size:2|exists:languages,code',
            'plural_names.*.value'              => 'required|string|between:1,100',
            'singular_names'                    => 'required|array|min:1',
            'singular_names.*.lang'             => 'required|string|size:2|exists:languages,code',
            'singular_names.*.value'            => 'required|string|between:1,100',
            'overview_texts'                    => 'nullable|array',
            'overview_texts.*.lang'             => 'required|string|size:2|exists:languages,code',
            'overview_texts.*.value'            => 'nullable|string|between:1,50000',
            'where_sn_descs'                    => 'nullable|array',
            'where_sn_descs.*.lang'             => 'required|string|size:2|exists:languages,code',
            'where_sn_descs.*.value'            => 'nullable|string|between:1,50000',
            'replacement_only'                  => 'required|in:Y,N',
            'hidden_for_service'                => 'required|in:Y,N',
            'features'                          => 'nullable|array',
            'features.*.feature_id'             => 'required|numeric|exists:features,id',
            'features.*.values'                 => 'nullable|array',
            'features.*.values.*.lang'          => 'nullable|string|size:2|exists:languages,code',
            'features.*.values.*.value'         => 'nullable|string|between:0,100',
            'features.*.overview_texts'         => 'nullable|array',
            'features.*.overview_texts.*.lang'  => 'required|string|size:2|exists:languages,code',
            'features.*.overview_texts.*.value' => 'nullable|string',
            'features.*.is_required'            => 'required|in:Y,N',
            'features.*.is_main'                => 'required|in:Y,N',
            'features.*.order'                  => 'required|integer|min:1',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return string[]
     */
    public function messages(): array
    {
        $prefix = 'catalog::create_update_group.validation_messages.';

        return [];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return string[]
     */
    public function attributes(): array
    {
        $prefix = 'catalog::create_update_group.validation_attributes.';

        $attributes['plural_names.0.value']     = Lang::get($prefix . 'plural_names');
        $attributes['singular_names.0.value']   = Lang::get($prefix . 'singular_names');
        $attributes['overview_texts.0.value']   = Lang::get($prefix . 'overview_texts');
        $attributes['where_sn_descs.0.value']   = Lang::get($prefix . 'where_sn_descs');

        //$attributes['features.*.feature_id']  = Lang::get($prefix . 'feature_id');
        //$attributes['features.*.values.0.value']  = Lang::get($prefix . 'feature_values');
        //$attributes['features.*.overview_texts.0.value']  = Lang::get($prefix . 'feature_overview_texts');

        for ($key = 0; $key < 30; $key++) {
            $attributes['features.' . $key . '.feature_id']  = Lang::get($prefix . 'feature_id');
            $attributes['features.' . $key . '.values.0.value']  = Lang::get($prefix . 'feature_values');
            $attributes['features.' . $key . '.overview_texts.0.value']  = Lang::get($prefix . 'feature_overview_texts');
        }

        return $attributes;
    }
}
