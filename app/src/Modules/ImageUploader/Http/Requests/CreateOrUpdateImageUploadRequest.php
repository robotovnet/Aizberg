<?php

declare(strict_types=1);

namespace Modules\ImageUploader\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Validator;

class CreateOrUpdateImageUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'title' => ['nullable', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
        ];

        if ($this->isMethod('post')) {
            $rules['image'] = ['nullable', 'file', 'image', 'max:8192'];
            $rules['images'] = ['nullable', 'array'];
            $rules['images.*'] = ['file', 'image', 'max:8192'];
        } else {
            $rules['title'] = ['required', 'string', 'max:255'];
        }

        return $rules;
    }

    public function withValidator(Validator $validator): void
    {
        if (!$this->isMethod('post')) {
            return;
        }

        $validator->after(function (Validator $validator): void {
            $single = $this->file('image');
            $hasSingle = $single instanceof UploadedFile;
            $multi = $this->normalizedImagesFromRequest();
            $multiCount = count($multi);

            if ($hasSingle && $multiCount > 0) {
                $validator->errors()->add('images', 'Нельзя передавать одновременно поля image и images.');

                return;
            }

            if (!$hasSingle && $multiCount === 0) {
                $validator->errors()->add('image', 'Загрузите файл в поле image или images.');

                return;
            }

            $title = trim((string) $this->input('title', ''));

            if ($hasSingle || $multiCount === 1) {
                if ($title === '') {
                    $validator->errors()->add('title', 'Поле title обязательно при загрузке одного файла.');
                }
            }
        });
    }

    /**
     * @return list<UploadedFile>
     */
    private function normalizedImagesFromRequest(): array
    {
        $raw = $this->file('images');
        if ($raw === null) {
            return [];
        }

        if (is_array($raw)) {
            return array_values(array_filter(
                $raw,
                static fn ($file): bool => $file instanceof UploadedFile
            ));
        }

        return $raw instanceof UploadedFile ? [$raw] : [];
    }
}


