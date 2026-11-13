<?php

declare(strict_types=1);

namespace Modules\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Support\Facades\Lang;

/**
 * Class CreateOrUpdateSectionRequest
 * @package Modules\Catalog\Http\Requests
 */
class CreateOrUpdateSectionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'names'                      => 'string',
        'names.*.lang'               => 'string',
        'names.*.value'              => 'string',
        'parent_section_id'          => 'string',
        'product_group_id'           => 'string',
        'thumbnail'                  => 'string',
        'is_hidden'                  => 'string',
        'not_for_everyone'           => 'string',
        'sorting_method'             => 'string',
        'order'                      => 'string',
        'seo'                        => 'string',
        'seo.starting_phrases'       => 'string',
        'seo.starting_phrases.*'     => 'string',
        'seo.desc'                   => 'string',
        'is_active_tab_parameters'   => 'string',
        'is_bp_disabled'             => 'string',
        'additional_text'            => 'string',
        'starting_text_product_name' => 'string',
    ])]
    public function rules(): array
    {
        return [
            'names'                      => 'required|array|min:1',
            'names.*.lang'               => 'required|string|size:2|exists:languages,code',
            'names.*.value'              => 'required|string|between:1,100',
            'parent_section_id'          => 'nullable|numeric|min:0',
            'product_group_id'           => 'nullable|numeric|min:1',
            'is_hidden'                  => 'required|in:Y,N',
            'not_for_everyone'           => 'required|in:Y,N',
            'sorting_method'             => 'required|in:by_novelty,by_availability,by_prices,by_alphabet,by_views',
            'order'                      => 'required|integer|min:1',
            'seo'                        => 'nullable',
            'seo.starting_phrases'       => 'nullable|array',
            'seo.starting_phrases.*'     => 'nullable|string',
            'seo.desc'                   => 'nullable|string',
            'is_active_tab_parameters'   => 'required|in:Y,N',
            'is_bp_disabled'             => 'required|in:Y,N',
            'additional_text'            => 'nullable|string',
            'starting_text_product_name' => 'nullable|string',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return string[]
     */
    public function messages(): array
    {
        $prefix = 'catalog::create_update_section.validation_messages.';

        $messages['parent_section_id.min']  = Lang::get($prefix . 'parent_section_id_min');
        $messages['sorting_method.in']  = Lang::get($prefix . 'sorting_method_in');

        return $messages;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return string[]
     */
    public function attributes(): array
    {
        $prefix = 'catalog::create_update_section.validation_attributes.';

        $attributes['parent_section_id'] = Lang::get($prefix . 'parent_section_id');
        $attributes['product_group_id'] = Lang::get($prefix . 'product_group_id');
        $attributes['is_hidden'] = Lang::get($prefix . 'is_hidden');
        $attributes['not_for_everyone'] = Lang::get($prefix . 'not_for_everyone');
        $attributes['sorting_method'] = Lang::get($prefix . 'sorting_method');
        $attributes['order'] = Lang::get($prefix . 'order');
        $attributes['seo.desc'] = Lang::get($prefix . 'seo.desc');
        $attributes['names.0.value']  = Lang::get($prefix . 'names');
        $attributes['seo.starting_phrases'] = Lang::get($prefix . 'seo.starting_phrases');
        $attributes['is_active_tab_parameters'] = Lang::get($prefix . 'is_active_tab_parameters');
        $attributes['is_bp_disabled'] = Lang::get($prefix . 'is_bp_disabled');
        $attributes['additional_text'] = Lang::get($prefix . 'additional_text');
        $attributes['starting_text_product_name'] = Lang::get($prefix . 'starting_text_product_name');

        return $attributes;
    }
}
