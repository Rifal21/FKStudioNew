<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** The shop is hidden until switched on in the dashboard; admins can still preview it. */
class EnsureShopEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! SiteSetting::current()->shop_enabled && ! $request->user()?->isSuperAdmin()) {
            abort(404);
        }

        return $next($request);
    }
}
