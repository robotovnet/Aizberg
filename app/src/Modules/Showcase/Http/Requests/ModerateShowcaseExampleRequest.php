<?php

declare(strict_types=1);

namespace Modules\Showcase\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ModerateShowcaseExampleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'website_url' => ['sometimes', 'string', 'url', 'max:512'],
            'name' => ['sometimes', 'string', 'max:255'],
            'short_description' => ['sometimes', 'string'],
            'category_id' => ['sometimes', 'integer', Rule::exists('showcase_categories', 'id')],
            'modules' => ['sometimes', 'array', 'min:1'],
            'modules.*' => ['string', 'max:100'],
            'images' => ['nullable', 'array'],
            'images.*' => ['file', 'image', 'max:4096'],
            'remove_image_ids' => ['nullable', 'array'],
            'remove_image_ids.*' => ['integer'],
        ];
    }
}
