<?php

declare(strict_types=1);

namespace Modules\RealProjects\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;

/**
 * Class CreateOrUpdateRealProjectRequest
 * @package Modules\RealProjects\Http\Requests
 */
class CreateOrUpdateRealProjectRequest extends FormRequest
{
    /** @var string[] Allowed banner extensions. */
    public const BANNER_MIMES = ['svg', 'png', 'jpg', 'jpeg', 'webp'];

    /**
     * Get the validation rules that apply to the request.
     *
     * Banner is required only when creating a project (no "id" route parameter);
     * when updating, omitted banner keeps the current one.
     */
    public function rules(): array
    {
        $isCreate = $this->route('id') === null;

        return [
            'name'   => ['required', 'string', 'max:255'],
            'url'    => ['required', 'string', 'max:2048', 'url', 'regex:/^https?:\/\//i'],
            'sort'   => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'banner' => [
                $isCreate ? 'required' : 'nullable',
                'file',
                'mimes:' . implode(',', self::BANNER_MIMES),
                'max:' . (int)Config::get('real_projects.banner_max_size', 4096),
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return string[]
     */
    public function messages(): array
    {
        $prefix = 'real_projects::create_update.validation_messages.';

        return [
            'name.required'   => Lang::get($prefix . 'required'),
            'name.max'        => Lang::get($prefix . 'max_string'),
            'url.required'    => Lang::get($prefix . 'required'),
            'url.max'         => Lang::get($prefix . 'max_string'),
            'url.url'         => Lang::get($prefix . 'url'),
            'url.regex'       => Lang::get($prefix . 'url'),
            'sort.integer'    => Lang::get($prefix . 'integer'),
            'sort.min'        => Lang::get($prefix . 'min_number'),
            'sort.max'        => Lang::get($prefix . 'max_number'),
            'banner.required' => Lang::get($prefix . 'banner_required'),
            'banner.file'     => Lang::get($prefix . 'banner_mimes'),
            'banner.mimes'    => Lang::get($prefix . 'banner_mimes'),
            'banner.max'      => Lang::get($prefix . 'banner_max'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return string[]
     */
    public function attributes(): array
    {
        $prefix = 'real_projects::create_update.validation_attributes.';

        return [
            'name'   => Lang::get($prefix . 'name'),
            'url'    => Lang::get($prefix . 'url'),
            'sort'   => Lang::get($prefix . 'sort'),
            'banner' => Lang::get($prefix . 'banner'),
        ];
    }
}
