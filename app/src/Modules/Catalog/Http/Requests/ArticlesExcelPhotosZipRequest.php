<?php

declare(strict_types=1);

namespace Modules\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Загрузка Excel со списком артикулов (столбец A) для выгрузки фотографий в ZIP.
 */
class ArticlesExcelPhotosZipRequest extends FormRequest
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
            'excel' => 'required|file|mimes:xlsx,xls|max:51200',
        ];
    }
}
