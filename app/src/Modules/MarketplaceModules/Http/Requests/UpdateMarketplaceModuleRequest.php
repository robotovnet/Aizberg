<?php

declare(strict_types=1);

namespace Modules\MarketplaceModules\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\MarketplaceModules\Enums\MarketplaceLicenseType;
use Modules\MarketplaceModules\Enums\MarketplacePricingType;
use Modules\MarketplaceModules\Services\MarketplaceModuleScreenshotService;

class UpdateMarketplaceModuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $isPaid = $this->input('pricing_type') === MarketplacePricingType::PAID;
        $currency = $this->input('currency');

        $this->merge([
            'license_url' => $this->input('license_url') ?: null,
            'changelog_url' => $this->input('changelog_url') ?: null,
            'price' => $isPaid ? ($this->input('price') ?: null) : null,
            'currency' => $isPaid && is_string($currency) ? strtoupper(trim($currency)) : ($isPaid ? $currency : null),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $id = (int)$this->route('id');

        return [
            'display_name' => ['required', 'string', 'max:255'],
            'install_slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9_-]+$/',
                Rule::unique('marketplace_modules', 'install_slug')->ignore($id),
            ],
            'website_url' => ['required', 'string', 'max:1024', 'url'],
            'source_code_url' => ['required', 'string', 'max:1024', $this->githubRule()],
            'license_type' => ['required', 'string', Rule::in(MarketplaceLicenseType::all())],
            'license_url' => ['nullable', 'string', 'max:1024', $this->githubRule()],
            'changelog_url' => ['nullable', 'string', 'max:1024', $this->githubRule()],
            'pricing_type' => ['required', 'string', Rule::in(MarketplacePricingType::all())],
            'price' => [
                'required_if:pricing_type,' . MarketplacePricingType::PAID,
                'nullable',
                'numeric',
                'between:0.01,9999999999.99',
            ],
            'currency' => [
                'required_if:pricing_type,' . MarketplacePricingType::PAID,
                'nullable',
                'string',
                'size:3',
                'regex:/^[A-Z]{3}$/',
            ],
            'author_display_name' => ['required', 'string', 'max:255'],
            'short_description' => ['required', 'string'],
            'category' => ['required', 'string', 'max:30'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:100'],
            'version' => ['required', 'string', 'max:64'],
            'version_release_date' => ['required', 'date'],
            'detailed_description' => ['nullable', 'string'],
            'icon' => ['nullable', 'file', 'image', 'mimes:jpeg,png,gif,webp,svg', 'max:4096'],
            'screenshots' => [
                'nullable',
                'array',
                'max:' . MarketplaceModuleScreenshotService::MAX_FILES,
            ],
            'screenshots.*' => [
                'file',
                'mimes:jpg,jpeg,png,webp',
                'mimetypes:image/jpeg,image/png,image/webp',
                'max:10240',
            ],
        ];
    }

    private function githubRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            if ($value === null || $value === '') {
                return;
            }
            if (!is_string($value) || !preg_match('#^https?://(www\.)?github\.com/#i', $value)) {
                $fail(__('The :attribute must be a valid URL on github.com.'));
            }
        };
    }
}
