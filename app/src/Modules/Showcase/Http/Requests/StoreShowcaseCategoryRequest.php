<?php

declare(strict_types=1);

namespace Modules\Showcase\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreShowcaseCategoryRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('showcase_categories', 'slug')],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
