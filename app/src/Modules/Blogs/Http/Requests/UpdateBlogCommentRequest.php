<?php

declare(strict_types=1);

namespace Modules\Blogs\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBlogCommentRequest extends FormRequest
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
            'body' => ['required', 'string', 'max:10000'],
        ];
    }
}
