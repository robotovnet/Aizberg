<?php

declare(strict_types=1);

namespace Modules\Blogs\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Blogs\Entities\BlogSection;

class StoreBlogSectionRequest extends FormRequest
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
            'slug' => ['nullable', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', Rule::exists('blog_sections', 'id')],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $parentId = $this->input('parent_id');
            if ($parentId === null) {
                return;
            }

            $parent = BlogSection::query()->find((int) $parentId);
            if ($parent === null) {
                return;
            }

            if ($parent->parent_id !== null) {
                $validator->errors()->add('parent_id', 'Подраздел можно создать только в корневом разделе (максимум 2 уровня).');
            }
        });
    }
}
