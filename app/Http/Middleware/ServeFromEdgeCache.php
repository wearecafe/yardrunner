<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

/**
 * The marketing pages (routes/marketing.php) are cached at Laravel Cloud's
 * edge. The edge won't cache a response that sets a cookie, and it keys on
 * the URL alone, so:
 *
 * - Inertia visits get a full page load instead (409 with a location),
 *   never a JSON page the edge could hand to a browser loading the page.
 * - Nobody is signed in here, so the page is the same for everyone.
 * - Successful pages go out with no cookies and a public Cache-Control:
 *   5 minutes in the browser, an hour at the edge. Laravel Cloud purges the
 *   edge on each deploy.
 * - Anything else (404s, errors, throttling) is never cached.
 */
class ServeFromEdgeCache
{
    /** Set on the request so shared page props skip anything per visitor. */
    public const ATTRIBUTE = 'edge_cached';

    public const BROWSER_SECONDS = 300;

    public const EDGE_SECONDS = 3600;

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->header('X-Inertia')) {
            return $this->neverCache(Inertia::location($request->fullUrl()));
        }

        // The same page for everyone: nothing about the visitor, even if a
        // guard could find them.
        $request->setUserResolver(fn () => null);
        $request->attributes->set(self::ATTRIBUTE, true);

        $response = $next($request);

        foreach ($response->headers->getCookies() as $cookie) {
            $response->headers->removeCookie($cookie->getName(), $cookie->getPath(), $cookie->getDomain());
        }

        if (! $request->isMethodCacheable() || ! $response->isSuccessful()) {
            return $this->neverCache($response);
        }

        $response->headers->set(
            'Cache-Control',
            sprintf('public, max-age=%d, s-maxage=%d', self::BROWSER_SECONDS, self::EDGE_SECONDS),
        );

        return $response;
    }

    private function neverCache(Response $response): Response
    {
        $response->headers->set('Cache-Control', 'no-store, private');

        return $response;
    }
}
