<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

/**
 * Class AllowOnlyAjaxRequests
 * @package App\Http\Middleware
 */
class AllowOnlyAjaxRequests
{
    /**
     * Handle an incoming request and check is XHR request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (!$request->ajax()) {
            return Response::make('', 405);
        }

        return $next($request);
    }
}
