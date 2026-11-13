<?php

declare(strict_types=1);

namespace Modules\Client\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadClientUserpicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'userpic' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,gif,webp',
                'mimetypes:image/jpeg,image/png,image/gif,image/webp',
                'max:10240',
            ],
        ];
    }
}
