<?php

declare(strict_types=1);

namespace Modules\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class CreateOrUpdateProductRequest
 * @package Modules\Catalog\Http\Requests
 */
class CreateOrUpdateProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'names'                          => 'string',
        'names.*.lang'                   => 'string',
        'names.*.value'                  => 'string',
        'overview_texts'                 => 'string',
        'overview_texts.*.lang'          => 'string',
        'overview_texts.*.value'         => 'string',
        'vendor_code'                    => 'string',
        'brand_id'                       => 'string',
        'product_group_id'               => 'string',
        'catalog_section_id'             => 'string',
        'catalog_section_id.*'           => 'string',
        'is_hidden'                      => 'string',
        'not_for_everyone'               => 'string',
        'ads_rotator'                    => 'string',
        'stock_status'                   => 'string',
        'uuid'                           => 'string',
        'related_products'               => 'string',
        'related_products.*'             => 'string',
        'similar_products'               => 'string',
        'similar_products.*'             => 'string',
        'ad_generator'                   => 'string',
        'ad_generator.alt_names'         => 'string',
        'ad_generator.pn'                => 'string',
        'ad_yandex_market'               => 'string',
        'ad_yandex_market.name'          => 'string',
        'ad_yandex_market.overview_text' => 'string',
        'ad_yandex_market.picture_url'   => 'string',
        'ad_yandex_market.is_active'     => 'string',
        'prices'                         => 'string',
        'prices.*.price_type_id'         => 'string',
        'prices.*.value'                 => 'string',
        'features'                       => 'string',
        'features.*.feature_id'          => 'string',
        'features.*.values'              => 'string',
        'features.*.values.*.lang'       => 'string',
        'features.*.values.*.value'      => 'string',
        'features.*.value'               => 'string',
        'materials_urls'                          => 'string',
        'materials_urls.drivers'                  => 'string',
        'materials_urls.drivers.*'                => 'string',
        'materials_urls.drivers.*.lang'          => 'string',
        'materials_urls.drivers.*.value'         => 'string',
        'materials_urls.manuals'                 => 'string',
        'materials_urls.manuals.*'               => 'string',
        'materials_urls.manuals.*.lang'          => 'string',
        'materials_urls.manuals.*.value'         => 'string',
        'copy_data_to_similar_products'                  => 'string',
        'copy_data_to_similar_products.desc'             => 'string',
        'copy_data_to_similar_products.related_products' => 'string',
        'copy_data_to_similar_products.brand'            => 'string',
        'copy_data_to_similar_products.features'         => 'string',
        'copy_data_to_similar_products.pn'               => 'string',
        'copy_data_to_similar_products.alt_names'        => 'string',
        'created_at'                                      => 'string',
    ])]
    public function rules(): array
    {
        $rules = [
            'names'                          => 'required|array|min:1',
            'names.*.lang'                   => 'required|string|size:2|exists:languages,code',
            'names.*.value'                  => 'required|string|between:1,100',
            'overview_texts'                 => 'required|array',
            'overview_texts.*.lang'          => 'required|string|size:2|exists:languages,code',
            'overview_texts.*.value'         => 'required|string|between:1,15000',
            'vendor_code'                    => 'required|string|between:1,255|unique:products,vendor_code,NULL,id,deleted_at,NULL',
            'brand_id'                       => 'required|integer|min:1|exists:brands,id',
            'product_group_id'               => 'required|integer|min:1|exists:product_groups,id',
            'catalog_section_id'             => 'required|array|min:1',
            'catalog_section_id.*'           => 'required|integer|min:1|exists:catalog_sections,id',
            'is_hidden'                      => 'required|in:Y,N',
            'not_for_everyone'               => 'required|in:Y,N',
            'ads_rotator'                    => 'required|in:Y,N',
            'stock_status'                   => 'required|in:in_stock,pre_order,out_of_stock',
            'uuid'                           => 'nullable|uuid', // unique:products,uuid,NULL,id,deleted_at,NULL
            'related_products'               => 'nullable|array',
            'related_products.*'             => 'integer|min:1|exists:products,id',
            'similar_products'               => 'nullable|array',
            'similar_products.*'             => 'integer|min:1|exists:products,id',
            'ad_generator'                   => 'nullable',
            'ad_generator.alt_names'         => 'nullable|string',
            'ad_generator.pn'                => 'nullable|string',
            'ad_yandex_market'               => 'nullable',
            'ad_yandex_market.name'          => 'nullable|string|min:1|max:100', // required_with:ad_yandex_market|
            'ad_yandex_market.overview_text' => 'nullable|string', // required_with:ad_yandex_market|
            'ad_yandex_market.picture_url'   => 'nullable|url',
            'ad_yandex_market.is_active'     => 'required_with:ad_yandex_market|in:Y,N',
            'prices'                         => 'required|array|min:1',
            'prices.*.price_type_id'         => 'required_with:prices|integer|min:1|exists:price_types,id',
            'prices.*.value'                 => 'required_with:prices|integer|min:0',
            'features'                       => 'nullable|array',
            'features.*.feature_id'          => 'required_with:features|integer|min:1|exists:features,id',
            'features.*.values'              => 'required_without:features.*.value|array|min:1',
            'features.*.values.*.lang'       => 'required_with:features.*.values|string|size:2|exists:languages,code',
            'features.*.values.*.value'      => 'required_with:features.*.values|string|min:1|max:255',
            'features.*.value'               => 'required_without:features.*.values|string|min:1|max:255',
            'materials_urls'                          => 'nullable',
            'materials_urls.drivers'                  => 'nullable|array',
            'materials_urls.drivers.*.lang'          => 'required_with:materials_urls.drivers|string|size:2|exists:languages,code',
            'materials_urls.drivers.*.value'         => 'required_with:materials_urls.drivers|string|min:1',
            'materials_urls.manuals'                  => 'nullable|array',
            'materials_urls.manuals.*.lang'          => 'required_with:materials_urls.manuals|string|size:2|exists:languages,code',
            'materials_urls.manuals.*.value'         => 'required_with:materials_urls.manuals|string|min:1',
            'copy_data_to_similar_products'                  => 'nullable',
            'copy_data_to_similar_products.desc'             => 'required_with:copy_data_to_similar_products|in:Y,N',
            'copy_data_to_similar_products.related_products' => 'required_with:copy_data_to_similar_products|in:Y,N',
            'copy_data_to_similar_products.brand'            => 'required_with:copy_data_to_similar_products|in:Y,N',
            'copy_data_to_similar_products.features'         => 'required_with:copy_data_to_similar_products|in:Y,N',
            'copy_data_to_similar_products.pn'               => 'required_with:copy_data_to_similar_products|in:Y,N',
            'copy_data_to_similar_products.alt_names'        => 'required_with:copy_data_to_similar_products|in:Y,N',
        ];

        // For update
        $pid = request()->segment(3);

        if ($pid !== null && is_numeric($pid)) {
            $rules['vendor_code'] = Str::replaceFirst('NULL', $pid, $rules['vendor_code']);
            $rules['created_at'] = 'required|date';
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
        $prefix = 'catalog::create_update_product.validation_messages.';

        $messages['brand_id.min']           = Lang::get($prefix . 'brand_id_min');
        $messages['brand_id.required']           = Lang::get($prefix . 'brand_id_min');

        $messages['product_group_id.min']   = Lang::get($prefix . 'product_group_id_min');
        $messages['product_group_id.required']   = Lang::get($prefix . 'product_group_id_min');

        $messages['catalog_section_id.min'] = Lang::get($prefix . 'catalog_section_id_min');
        $messages['catalog_section_id.required']   = Lang::get($prefix . 'catalog_section_id_min');

        return $messages;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return string[]
     */
    public function attributes(): array
    {
        $prefix = 'catalog::create_update_product.validation_attributes.';

        $attributes['names']                  = Lang::get($prefix . 'names');
        $attributes['overview_texts']         = Lang::get($prefix . 'overview_texts');
        $attributes['overview_texts.0.value'] = Lang::get($prefix . 'overview_texts');
        $attributes['catalog_section_id']     = Lang::get($prefix . 'catalog_section_id');
        $attributes['prices']                 = Lang::get($prefix . 'prices');
        $attributes['vendor_code']            = Lang::get($prefix . 'vendor_code');
        $attributes['brand_id']               = Lang::get($prefix . 'brand_id');
        $attributes['product_group_id']       = Lang::get($prefix . 'product_group_id');
        $attributes['is_hidden']              = Lang::get($prefix . 'is_hidden');
        $attributes['not_for_everyone']       = Lang::get($prefix . 'not_for_everyone');
        $attributes['stock_status']           = Lang::get($prefix . 'stock_status');
        $attributes['catalog_section_id.0']   = Lang::get($prefix . 'catalog_section_id');
        $attributes['names.0.value']          = Lang::get($prefix . 'names');
        $attributes['ad_yandex_market']       = Lang::get($prefix . 'ad_yandex_market');
        $attributes['ad_yandex_market.name']  = Lang::get($prefix . 'ad_yandex_market_name');
        $attributes['ad_yandex_market.overview_text'] = Lang::get($prefix . 'ad_yandex_market_overview_text');

        for ($i = 0; $i < 6; $i++) {
            $attributes['prices.' . $i . '.value'] = Lang::get($prefix . 'prices');
        }

        return $attributes;
    }
}
