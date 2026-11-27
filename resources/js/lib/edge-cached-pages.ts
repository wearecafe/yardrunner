import { router } from '@inertiajs/react';

/**
 * The marketing pages outside the app: the home page and the help center.
 * They're served from Laravel Cloud's edge cache, which keys on the URL alone
 * and can't tell a page load from an Inertia visit, so the browser always
 * loads them as whole pages. Keep in step with routes/marketing.php.
 */
export function isEdgeCachedPath(pathname: string): boolean {
    return (
        pathname === '/' ||
        pathname === '/help' ||
        pathname.startsWith('/help/')
    );
}

/**
 * Turn Inertia visits to those pages into full page loads, and skip
 * prefetching them. Runs once from app.tsx.
 */
export function loadEdgeCachedPagesInFull(): void {
    router.on('before', (event) => {
        const { url, prefetch, method } = event.detail.visit;

        if (
            method !== 'get' ||
            url.origin !== window.location.origin ||
            !isEdgeCachedPath(url.pathname)
        ) {
            return;
        }

        if (!prefetch) {
            window.location.assign(url.href);
        }

        return false;
    });
}
