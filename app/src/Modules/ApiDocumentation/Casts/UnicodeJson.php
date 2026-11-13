<?php

declare(strict_types=1);

namespace Modules\ApiDocumentation\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use JsonException;

class UnicodeJson implements CastsAttributes
{
    /**
     * @param mixed $value
     * @param array<string, mixed> $attributes
     * @return mixed
     *
     * @throws JsonException
     */
    public function get($model, string $key, $value, array $attributes)
    {
        if ($value === null || is_array($value)) {
            return $value;
        }

        return json_decode((string) $value, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param mixed $value
     * @param array<string, mixed> $attributes
     * @return string|null
     *
     * @throws JsonException
     */
    public function set($model, string $key, $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        return json_encode(
            $value,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
        );
    }
}
