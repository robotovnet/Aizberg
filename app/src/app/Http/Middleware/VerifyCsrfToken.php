<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use App\Helpers\Helper;

/**
 * Class VerifyCsrfToken
 * @package App\Http\Middleware
 */
class VerifyCsrfToken extends Middleware
{
    /** @var array The URIs that should be excluded from CSRF verification. */
    protected $except = [
        'api/showcase/*',
        'api/blogs/*',
        'api/marketplace-modules',
        'api/marketplace-modules/*',
        'api/api-documentations',
        'api/api-documentations/*',
        'api/client/userpic',
    ];

    /**
     * Allow requests without validation for non-production environments.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     *
     * @throws TokenMismatchException
     */
    public function handle($request, Closure $next): mixed
    {
        if (Helper::isNonProductEnv()) {
            return $next($request);
        }

        return parent::handle($request, $next);
    }
}
