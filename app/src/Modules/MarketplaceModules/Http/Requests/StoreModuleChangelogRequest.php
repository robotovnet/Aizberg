<?php

declare(strict_types=1);

namespace Modules\MarketplaceModules\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreModuleChangelogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'version' => ['required', 'string', 'max:64'],
            'changes' => ['required', 'array', 'min:1'],
            'changes.*' => ['required', 'string', 'max:2000'],
            'release_date' => ['nullable', 'date'],
        ];
    }
}
