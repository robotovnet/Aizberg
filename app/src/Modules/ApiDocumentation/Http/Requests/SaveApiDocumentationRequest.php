<?php

declare(strict_types=1);

namespace Modules\ApiDocumentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\ApiDocumentation\Entities\ApiDocumentation;
use Modules\ApiDocumentation\Http\Requests\Concerns\ValidatesTranslationMaps;

class SaveApiDocumentationRequest extends FormRequest
{
    use ValidatesTranslationMaps;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->isMethod('post')) {
            $this->merge([
                'default_locale' => $this->input('default_locale') ?: (string) app()->getLocale(),
                'is_published' => $this->has('is_published') ? $this->boolean('is_published') : true,
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $creating = $this->isMethod('post');
        $presence = $creating ? 'required' : 'sometimes';
        $id = (int) $this->route('id');

        return [
            'slug' => [
                $presence,
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9_-]+$/',
                Rule::notIn(['manage', 'search']),
                Rule::unique('api_documentations', 'slug')->ignore($id),
            ],
            'name' => [$presence, 'array', 'min:1'],
            'name.*' => ['string', 'max:255'],
            'short_description' => [$presence, 'array', 'min:1'],
            'short_description.*' => ['string', 'max:5000'],
            'tags' => [$creating ? 'nullable' : 'sometimes', 'array', 'max:50'],
            'tags.*' => ['string', 'max:100', 'distinct'],
            'default_locale' => [$presence, 'string', 'max:16', 'regex:/^[A-Za-z]{2,3}(?:[-_][A-Za-z0-9]{2,8})*$/'],
            'is_published' => [$presence, 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($this->has('name')) {
                $this->validateTranslationMap($validator, 'name', $this->input('name'));
            }
            if ($this->has('short_description')) {
                $this->validateTranslationMap($validator, 'short_description', $this->input('short_description'));
            }

            $existing = null;
            if (!$this->isMethod('post')) {
                $existing = ApiDocumentation::query()->find((int) $this->route('id'));
            }

            $name = $this->input('name', $existing?->name ?? []);
            $defaultLocale = (string) $this->input('default_locale', $existing?->default_locale ?? '');
            if (is_array($name) && $defaultLocale !== '' && !array_key_exists($defaultLocale, $name)) {
                $validator->errors()->add(
                    'default_locale',
                    'Для языка по умолчанию должен быть задан перевод поля name.'
                );
            }
        });
    }
}
