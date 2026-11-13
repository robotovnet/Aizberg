<?php

declare(strict_types=1);

namespace Modules\ApiDocumentation\Http\Requests\Concerns;

use Illuminate\Contracts\Validation\Validator;

trait ValidatesTranslationMaps
{
    /**
     * @param mixed $value
     */
    protected function validateTranslationMap(
        Validator $validator,
        string $path,
        $value,
        bool $required = true
    ): void {
        if ($value === null && !$required) {
            return;
        }

        if (!is_array($value) || ($required && $value === [])) {
            $validator->errors()->add($path, 'Поле должно быть непустым словарём переводов.');
            return;
        }

        foreach ($value as $locale => $translation) {
            if (!is_string($locale) || !preg_match('/^[A-Za-z]{2,3}(?:[-_][A-Za-z0-9]{2,8})*$/', $locale)) {
                $validator->errors()->add($path . '.' . $locale, 'Некорректный код языка.');
            }

            if (!is_string($translation)) {
                $validator->errors()->add($path . '.' . $locale, 'Перевод должен быть строкой.');
            } elseif ($required && trim($translation) === '') {
                $validator->errors()->add($path . '.' . $locale, 'Перевод не должен быть пустым.');
            }
        }
    }
}
