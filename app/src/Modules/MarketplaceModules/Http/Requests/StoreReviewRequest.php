<?php

declare(strict_types=1);

namespace Modules\MarketplaceModules\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
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
            'review_text' => ['required', 'string', 'max:50000'],
        ];
    }
}
