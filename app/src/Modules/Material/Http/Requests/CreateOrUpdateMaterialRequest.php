<?php

declare(strict_types=1);

namespace Modules\Material\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use JetBrains\PhpStorm\ArrayShape;
use Str;

/**
 * Class CreateOrUpdateMaterialRequest
 * @package Modules\Material\Http\Requests
 */
class CreateOrUpdateMaterialRequest extends FormRequest
{
    /**
     * Normalize legacy/plain payload before validation.
     */
    protected function prepareForValidation(): void
    {
        $locale = app()->getLocale() ?: 'ru';
        $data = $this->all();

        if (array_key_exists('name', $data) && is_string($data['name'])) {
            $data['name'] = [[
                'lang' => $locale,
                'value' => $data['name'],
            ]];
        }

        if (array_key_exists('content', $data) && is_string($data['content'])) {
            $data['content'] = [[
                'lang' => $locale,
                'value' => $data['content'],
            ]];
        }

        $this->replace($data);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'shop_id'          => 'string',
        'name'            => 'string',
        'name.*.lang'     => 'string',
        'name.*.value'    => 'string',
        'url_slug'         => 'string',
        'code'             => 'string',
        'section_name'     => 'string',
        'type'             => 'string',
        'is_active'        => 'string',
        'meta_keywords'    => 'string',
        'meta_description' => 'string',
        'content'         => 'string',
        'content.*.lang'  => 'string',
        'content.*.value' => 'string',
    ])]
    public function rules(): array
    {
        $rules = [
            'shop_id'          => 'required|min:1|exists:shops,id',
            'name'             => 'required|array|min:1',
            'name.*.lang'      => 'required_with:name|string|size:2|exists:languages,code',
            'name.*.value'     => 'required_with:name|string|between:1,255',
            'url_slug'         => 'required|string|between:1,160',
            'code'             => 'required|string|between:1,60|unique:materials,code,NULL,id,deleted_at,NULL',
            'section_name'     => 'required|string|between:1,60',
            'type'             => 'required|in:page,block',
            'is_active'        => 'required|in:Y,N',
            'meta_keywords'    => 'nullable|string|min:1',
            'meta_description' => 'nullable|string|min:1',
            'content'           => 'nullable|array|min:1',
            'content.*.lang'   => 'required_with:content|string|size:2|exists:languages,code',
            'content.*.value'   => 'required_with:content|string|min:1',
        ];

        // For update
        $materialId = request()->segment(2);

        if ($materialId !== null) {
            $rules['code'] = Str::replaceFirst('NULL', $materialId, $rules['code']);
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
        $prefix = 'material::create_update_material.validation_messages.';

        $messages['shop_id.min']  = Lang::get($prefix . 'shop_id_min');

        return $messages;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return string[]
     */
    public function attributes(): array
    {
        $prefix = 'material::create_update_material.validation_attributes.';

        $attributes = [];

        $attributes['shop_id'] = Lang::get($prefix . 'shop_id');
        $attributes['name'] = Lang::get($prefix . 'name');
        $attributes['name.*.lang'] = Lang::get($prefix . 'name_lang');
        $attributes['name.*.value'] = Lang::get($prefix . 'name');
        $attributes['url_slug'] = Lang::get($prefix . 'url_slug');
        $attributes['code'] = Lang::get($prefix . 'code');
        $attributes['section_name'] = Lang::get($prefix . 'section_name');
        $attributes['type'] = Lang::get($prefix . 'type');
        $attributes['is_active'] = Lang::get($prefix . 'is_active');
        $attributes['meta_keywords'] = Lang::get($prefix . 'meta_keywords');
        $attributes['meta_description'] = Lang::get($prefix . 'meta_description');
        $attributes['content'] = Lang::get($prefix . 'content');
        $attributes['content.*.lang'] = Lang::get($prefix . 'content_lang');
        $attributes['content.*.value'] = Lang::get($prefix . 'content');
        
        return $attributes;
    }
}
