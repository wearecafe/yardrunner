# Marketing pages: cookieless and edge-cached

Status: built, merged in PR #59 (Nov 27, 2026).

## Scope

The home page (`/`) and the help center (`/help…`) set no cookies and are served from Laravel Cloud's edge cache. They run in the `marketing` middleware group (`routes/marketing.php`): no session, no CSRF token, no signed-in user. `ServeFromEdgeCache` sends `Cache-Control: public, max-age=300, s-maxage=3600` on successful pages and `no-store` on anything else, and turns stray Inertia visits into uncached 409 full-page loads. The browser loads these pages whole (`resources/js/lib/edge-cached-pages.ts`). How it's run on Laravel Cloud: [laravel-cloud.md](laravel-cloud.md#edge-cache).

YardRunner keeps these pages in Inertia rather than the Laravel stack's Blade views in `routes/site.php` (an exception Jon confirmed).

## Decisions

- `decisions/2026-11-27-marketing-pages-cookieless.md`
- `decisions/2026-11-27-marketing-pages-same-for-everyone.md`
- `decisions/2026-11-27-marketing-pages-full-loads.md`
- `decisions/2026-11-27-marketing-pages-cache-times.md`
- `decisions/2026-11-27-marketing-pages-stay-inertia.md`

## Access changes

None. Signed-in people see the same pages as everyone else; "Log in" takes them into the app.

## Security notes

- Guest routes, all GET: `/`, `/help…`, `/help/suggest` (throttled 120/min). Listed in [security.md](security.md#guest-routes).
- Nothing per visitor reaches the cached HTML: the user resolver is cleared, and the sidebar prop doesn't read its cookie on these pages.
- 404s, errors and throttled responses are never cached.
- Tests: `tests/Feature/MarketingPagesTest.php`.

## UAT rows

Results are Jon's to fill in. IDs: `demo-and-uat.md` → UAT row IDs.

| #            | What to check                                                                                                                                                                                                                 | Who | Result |
| ------------ | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | --- | ------ |
| MK-1 (was 9) | After turning on "purge edge cache on deploy" and deploying, the home page and a help article answer with no `Set-Cookie` and `cf-cache-status: HIT` on a second load, and signed-in people still reach the app from "Log in" | Jon |        |
