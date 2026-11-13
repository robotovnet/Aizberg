<?php

declare(strict_types=1);

namespace Modules\TransportCompany\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class IconUploadRequest
 * @package Modules\TransportCompany\Http\Requests
 */
class IconUploadRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'icon' => 'string',
    ])]
    public function rules(): array
    {
        return [
            'icon' => 'required|image|dimensions:min_width=16,min_height=16|min:0.01|max:3072',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return string[]
     */
    public function messages(): array
    {
        $prefix = 'transportcompany::icon_upload.validation_messages.';

        $messages['icon.required']   = Lang::get($prefix . 'icon_required');
        $messages['icon.image']      = Lang::get($prefix . 'icon_image');
        $messages['icon.dimensions'] = Lang::get($prefix . 'icon_dimensions');
        $messages['icon.min']        = Lang::get($prefix . 'icon_min');
        $messages['icon.max']        = Lang::get($prefix . 'icon_max');


        return $messages;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return string[]
     */
    public function attributes(): array
    {
        $prefix = 'transportcompany::icon_upload.validation_attributes.';

        $attributes['icon'] = Lang::get($prefix . 'icon');

        return $attributes;
    }
}
