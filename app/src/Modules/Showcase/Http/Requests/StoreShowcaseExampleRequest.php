<?php

declare(strict_types=1);

namespace Modules\Showcase\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreShowcaseExampleRequest extends FormRequest
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
            'website_url' => ['required', 'string', 'url', 'max:512'],
            'name' => ['required', 'string', 'max:255'],
            'short_description' => ['required', 'string'],
            'category_id' => ['required', 'integer', Rule::exists('showcase_categories', 'id')],
            'modules' => ['required', 'array', 'min:1'],
            'modules.*' => ['string', 'max:100'],
            'images' => ['nullable', 'array'],
            'images.*' => ['file', 'image', 'max:4096'],
        ];
    }
}
