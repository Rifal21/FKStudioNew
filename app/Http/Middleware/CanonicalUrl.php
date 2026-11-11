<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * One address per page, so Search Console does not report duplicates:
 * www.example.com -> example.com, a trailing slash is dropped, and plain http is sent to https.
 *
 * The host rule is driven by APP_URL, so a wrong or local APP_URL simply never fires it.
 * The http rule only trusts an explicit "X-Forwarded-Proto: http" from the proxy, which cannot loop
 * when the proxy does not send the header.
 */
class CanonicalUrl
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->isMethodSafe() || $request->is('up')) {
            return $next($request);
        }

        $canonicalHost = strtolower((string) parse_url((string) config('app.url'), PHP_URL_HOST));
        $host = strtolower($request->getHost());
        $path = $request->getPathInfo();

        $wrongHost = $canonicalHost !== '' && $host === 'www.'.$canonicalHost;
        $insecure = $request->headers->get('X-Forwarded-Proto') === 'http';
        $trailingSlash = $path !== '/' && str_ends_with($path, '/');

        if (! $wrongHost && ! $insecure && ! $trailingSlash) {
            return $next($request);
        }

        $scheme = $insecure ? 'https' : $request->getScheme();
        if ($wrongHost) {
            $scheme = parse_url((string) config('app.url'), PHP_URL_SCHEME) ?: $scheme;
        }

        $target = $scheme.'://'.($wrongHost ? $canonicalHost : $request->getHttpHost())
            .($trailingSlash ? rtrim($path, '/') : $path)
            .($request->getQueryString() !== null ? '?'.$request->getQueryString() : '');

        return redirect()->to($target, 301);
    }
}
