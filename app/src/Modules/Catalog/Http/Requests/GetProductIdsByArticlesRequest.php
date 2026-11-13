<?php

declare(strict_types=1);

namespace Modules\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class GetProductIdsByArticlesRequest
 * @package Modules\Catalog\Http\Requests
 */
class GetProductIdsByArticlesRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'articles' => 'required|array|min:1|max:1000',
            'articles.*' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'articles.required' => 'Поле articles обязательно для заполнения.',
            'articles.array' => 'Поле articles должно быть массивом.',
            'articles.min' => 'Массив articles должен содержать хотя бы один элемент.',
            'articles.max' => 'Массив articles не может содержать более 1000 элементов.',
        ];
    }
}
