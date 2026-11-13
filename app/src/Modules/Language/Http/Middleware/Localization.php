<?php

declare(strict_types=1);

namespace Modules\Language\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Language\Services\LanguageService;

/**
 * Localization Middleware
 * @package Modules\Language\Http\Middleware
 */
class Localization
{
    /** @var LanguageService Service layer for working with languages */
    private LanguageService $language;

    /**
     * Localization Middleware constructor.
     */
    public function __construct(LanguageService $language)
    {
        $this->language = $language;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $this->language->setLocale();

        return $next($request);
    }
}
