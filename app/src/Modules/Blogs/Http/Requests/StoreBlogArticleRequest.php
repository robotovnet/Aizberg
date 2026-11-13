<?php

declare(strict_types=1);

namespace Modules\Blogs\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBlogArticleRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'section_id' => ['required', 'integer', Rule::exists('blog_sections', 'id')],
            'short_description' => ['required', 'string'],
            'content' => ['required', 'string'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:100'],
            'preview_image' => ['nullable', 'file', 'image', 'max:4096'],
        ];
    }
}
