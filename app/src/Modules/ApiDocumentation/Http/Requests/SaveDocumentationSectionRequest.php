<?php

declare(strict_types=1);

namespace Modules\ApiDocumentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\ApiDocumentation\Entities\ApiDocumentation;
use Modules\ApiDocumentation\Entities\ApiDocumentationSection;
use Modules\ApiDocumentation\Http\Requests\Concerns\ValidatesTranslationMaps;

class SaveDocumentationSectionRequest extends FormRequest
{
    use ValidatesTranslationMaps;

    private const BLOCK_TYPES = [
        'text',
        'code',
        'important',
        'section_link',
        'variable',
        'table',
        'card',
    ];

    private const CARD_VARIANTS = [
        'accent',
        'default',
    ];

    private const HTTP_METHODS = [
        'GET',
        'POST',
        'PUT',
        'PATCH',
        'DELETE',
        'HEAD',
        'OPTIONS',
        'TRACE',
    ];

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $endpoint = $this->input('endpoint');
        if (is_array($endpoint) && isset($endpoint['method']) && is_string($endpoint['method'])) {
            $endpoint['method'] = strtoupper($endpoint['method']);
            $this->merge(['endpoint' => $endpoint]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $creating = $this->isMethod('post');
        $presence = $creating ? 'required' : 'sometimes';
        $documentationId = (int) $this->route('documentationId');
        $sectionId = (int) $this->route('sectionId');

        return [
            'parent_id' => [$creating ? 'nullable' : 'sometimes', 'nullable', 'integer', 'min:1'],
            'position' => $creating
                ? ['integer', 'min:0']
                : ['sometimes', 'integer', 'min:0'],
            'slug' => [
                $presence,
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9_-]+$/',
                Rule::unique('api_documentation_sections', 'slug')
                    ->where(static fn ($query) => $query->where('api_documentation_id', $documentationId))
                    ->ignore($sectionId),
            ],
            'title' => [$presence, 'array', 'min:1'],
            'title.*' => ['string', 'max:500'],
            'description_blocks' => [$creating ? 'nullable' : 'sometimes', 'array'],
            'description_blocks.*' => ['array'],
            'description_blocks.*.type' => ['required', 'string', Rule::in(self::BLOCK_TYPES)],
            'description_blocks.*.content' => ['nullable', 'array'],
            'description_blocks.*.language' => ['nullable', 'string', 'max:50'],
            'description_blocks.*.section_id' => ['nullable', 'integer', 'min:1'],
            'description_blocks.*.label' => ['nullable', 'array'],
            'description_blocks.*.name' => ['nullable', 'string', 'max:255'],
            'description_blocks.*.description' => ['nullable', 'array'],
            'description_blocks.*.columns' => ['nullable', 'array'],
            'description_blocks.*.rows' => ['nullable', 'array'],
            'description_blocks.*.variant' => ['nullable', 'string', Rule::in(self::CARD_VARIANTS)],
            'description_blocks.*.title' => ['nullable', 'array'],
            'endpoint' => [$creating ? 'nullable' : 'sometimes', 'nullable', 'array'],
            'endpoint.method' => ['required_with:endpoint', 'string', Rule::in(self::HTTP_METHODS)],
            'endpoint.url' => ['required_with:endpoint', 'string', 'max:2048'],
            'request_body' => [$creating ? 'nullable' : 'sometimes', 'nullable', 'array'],
            'request_body.content_type' => ['required_with:request_body', 'string', 'max:255'],
            'request_body.schema' => ['nullable', 'array'],
            'request_headers' => [$creating ? 'nullable' : 'sometimes', 'array'],
            'request_headers.*' => ['array'],
            'request_headers.*.name' => ['required', 'string', 'max:255'],
            'request_headers.*.required' => ['required', 'boolean'],
            'request_headers.*.description' => ['required', 'array', 'min:1'],
            'request_headers.*.type' => ['required', 'string', 'max:100'],
            'request_headers.*.example' => ['nullable'],
            'request_parameters' => [$creating ? 'nullable' : 'sometimes', 'array'],
            'request_parameters.*' => ['array'],
            'request_parameters.*.name' => ['required', 'string', 'max:255'],
            'request_parameters.*.required' => ['required', 'boolean'],
            'request_parameters.*.description' => ['required', 'array', 'min:1'],
            'request_parameters.*.type' => ['required', 'string', 'max:100'],
            'request_parameters.*.location' => [
                'nullable',
                'string',
                Rule::in(['query', 'path', 'header', 'cookie', 'body']),
            ],
            'request_parameters.*.example' => ['nullable'],
            'request_example' => [$creating ? 'nullable' : 'sometimes', 'nullable', 'array'],
            'responses' => [$creating ? 'nullable' : 'sometimes', 'array'],
            'responses.*' => ['array'],
            'responses.*.status_code' => ['required', 'regex:/^(default|[1-5][0-9]{2})$/'],
            'responses.*.name' => ['required', 'array', 'min:1'],
            'responses.*.description' => ['required', 'array', 'min:1'],
            'responses.*.type' => ['required', 'string', 'max:100'],
            'responses.*.content_type' => ['nullable', 'string', 'max:255'],
            'responses.*.schema' => ['nullable', 'array'],
            'responses.*.example' => ['nullable', 'array'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($this->has('title')) {
                $this->validateTranslationMap($validator, 'title', $this->input('title'));
            }

            $this->validateParameterTranslations($validator, 'request_headers');
            $this->validateParameterTranslations($validator, 'request_parameters');
            $this->validateResponseTranslations($validator);
            $this->validateBlocks($validator);

            $documentation = ApiDocumentation::query()->find((int) $this->route('documentationId'));
            if ($documentation !== null) {
                $section = $this->isMethod('post')
                    ? null
                    : ApiDocumentationSection::query()->find((int) $this->route('sectionId'));
                $title = $this->input('title', $section?->title ?? []);

                if (is_array($title) && !array_key_exists($documentation->default_locale, $title)) {
                    $validator->errors()->add(
                        'title',
                        'Заголовок должен содержать перевод для языка документации по умолчанию.'
                    );
                }
            }
        });
    }

    private function validateParameterTranslations($validator, string $field): void
    {
        $items = $this->input($field, []);
        if (!is_array($items)) {
            return;
        }

        foreach ($items as $index => $item) {
            if (is_array($item) && array_key_exists('description', $item)) {
                $this->validateTranslationMap(
                    $validator,
                    $field . '.' . $index . '.description',
                    $item['description']
                );
            }
        }
    }

    private function validateResponseTranslations($validator): void
    {
        $responses = $this->input('responses', []);
        if (!is_array($responses)) {
            return;
        }

        foreach ($responses as $index => $response) {
            if (!is_array($response)) {
                continue;
            }
            foreach (['name', 'description'] as $field) {
                if (array_key_exists($field, $response)) {
                    $this->validateTranslationMap(
                        $validator,
                        'responses.' . $index . '.' . $field,
                        $response[$field]
                    );
                }
            }
        }
    }

    private function validateBlocks($validator): void
    {
        $blocks = $this->input('description_blocks', []);
        if (!is_array($blocks)) {
            return;
        }

        foreach ($blocks as $index => $block) {
            if (!is_array($block) || !isset($block['type'])) {
                continue;
            }

            $path = 'description_blocks.' . $index;
            switch ($block['type']) {
                case 'text':
                case 'code':
                case 'important':
                    $this->validateTranslationMap($validator, $path . '.content', $block['content'] ?? null);
                    break;

                case 'section_link':
                    $this->validateSectionLink($validator, $path, $block);
                    break;

                case 'variable':
                    if (!isset($block['name']) || !is_string($block['name']) || trim($block['name']) === '') {
                        $validator->errors()->add($path . '.name', 'Для переменной требуется имя.');
                    }
                    if (array_key_exists('description', $block)) {
                        $this->validateTranslationMap(
                            $validator,
                            $path . '.description',
                            $block['description'],
                            false
                        );
                    }
                    break;

                case 'table':
                    $this->validateTable($validator, $path, $block);
                    break;

                case 'card':
                    if (!isset($block['variant'])) {
                        $validator->errors()->add($path . '.variant', 'Для карточки требуется вариант оформления.');
                    }
                    $this->validateTranslationMap($validator, $path . '.title', $block['title'] ?? null);
                    $this->validateTranslationMap($validator, $path . '.content', $block['content'] ?? null);
                    break;
            }
        }
    }

    /**
     * @param array<string, mixed> $block
     */
    private function validateSectionLink($validator, string $path, array $block): void
    {
        $sectionId = $block['section_id'] ?? null;
        if (!is_int($sectionId) && !(is_string($sectionId) && ctype_digit($sectionId))) {
            $validator->errors()->add($path . '.section_id', 'Для ссылки требуется идентификатор раздела.');
        } else {
            $exists = ApiDocumentationSection::query()
                ->where('api_documentation_id', (int) $this->route('documentationId'))
                ->whereKey((int) $sectionId)
                ->exists();
            if (!$exists) {
                $validator->errors()->add($path . '.section_id', 'Связанный раздел не найден в документации.');
            }
        }

        $this->validateTranslationMap($validator, $path . '.label', $block['label'] ?? null);
    }

    /**
     * @param array<string, mixed> $block
     */
    private function validateTable($validator, string $path, array $block): void
    {
        $columns = $block['columns'] ?? null;
        if (!is_array($columns) || $columns === []) {
            $validator->errors()->add($path . '.columns', 'Для таблицы требуется хотя бы один столбец.');
            return;
        }

        $keys = [];
        foreach ($columns as $columnIndex => $column) {
            if (!is_array($column) || !isset($column['key']) || !is_string($column['key']) || $column['key'] === '') {
                $validator->errors()->add($path . '.columns.' . $columnIndex . '.key', 'Для столбца требуется ключ.');
                continue;
            }
            if (in_array($column['key'], $keys, true)) {
                $validator->errors()->add(
                    $path . '.columns.' . $columnIndex . '.key',
                    'Ключи столбцов должны быть уникальными.'
                );
            }
            $keys[] = $column['key'];
            $this->validateTranslationMap(
                $validator,
                $path . '.columns.' . $columnIndex . '.title',
                $column['title'] ?? null
            );
        }

        $rows = $block['rows'] ?? [];
        if (!is_array($rows)) {
            $validator->errors()->add($path . '.rows', 'Строки таблицы должны быть массивом.');
            return;
        }

        foreach ($rows as $rowIndex => $row) {
            $cells = is_array($row) ? ($row['cells'] ?? null) : null;
            if (!is_array($cells)) {
                $validator->errors()->add(
                    $path . '.rows.' . $rowIndex . '.cells',
                    'Для строки требуется словарь ячеек.'
                );
                continue;
            }
            foreach ($cells as $key => $cell) {
                if (!in_array($key, $keys, true)) {
                    $validator->errors()->add(
                        $path . '.rows.' . $rowIndex . '.cells.' . $key,
                        'Ячейка ссылается на неизвестный столбец.'
                    );
                }
                $this->validateTranslationMap(
                    $validator,
                    $path . '.rows.' . $rowIndex . '.cells.' . $key,
                    $cell
                );
            }
        }
    }
}
