<?php

declare(strict_types=1);

namespace Modules\Staff\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class StaffUploadUserpicRequest
 * @package Modules\Staff\Http\Requests
 */
class StaffUploadUserpicRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    #[ArrayShape([
        'userpic' => 'string',
    ])]
    public function rules(): array
    {
        return [
            'userpic' => 'required|image|dimensions:min_width=100,min_height=100|min:1|max:3072',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return string[]
     */
    public function messages(): array
    {
        $prefix = 'staff::staff_upload_userpic.validation_messages.';

        $messages['userpic.required']   = Lang::get($prefix . 'userpic_required');
        $messages['userpic.image']      = Lang::get($prefix . 'userpic_image');
        $messages['userpic.dimensions'] = Lang::get($prefix . 'userpic_dimensions');
        $messages['userpic.min']        = Lang::get($prefix . 'userpic_min');
        $messages['userpic.max']        = Lang::get($prefix . 'userpic_max');

        return $messages;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return string[]
     */
    public function attributes(): array
    {
        $prefix = 'staff::staff_upload_userpic.validation_attributes.';

        $attributes['userpic'] = Lang::get($prefix . 'userpic');

        return $attributes;
    }
}
