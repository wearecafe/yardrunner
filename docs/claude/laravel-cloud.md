# Laravel Cloud

YardRunner is hosted on Laravel Cloud with a MySQL database. This file covers
the app itself: deploying, the environment, the scheduler and queue, and the
edge cache. Each outside service has its own file:

| Service                          | File                                   |
| -------------------------------- | -------------------------------------- |
| Stripe (payments, tax, billing)  | [stripe.md](stripe.md)                 |
| QuickBooks Online                | [quickbooks.md](quickbooks.md)         |
| Resend (email, sending domains)  | [resend.md](resend.md)                 |
| Cloudflare DNS (sending domains) | [cloudflare-dns.md](cloudflare-dns.md) |
| Twilio (text messages)           | [twilio.md](twilio.md)                 |
| Web push (staff alerts)          | [web-push.md](web-push.md)             |
| File storage (S3 bucket)         | [file-storage.md](file-storage.md)     |
| Bunny Fonts                      | [bunny-fonts.md](bunny-fonts.md)       |

## First deploy

1. Commit `composer.lock` and `package-lock.json` (run `composer update`
   once locally if `composer.lock` doesn't exist yet).
2. In Laravel Cloud, create an application from the `wearecafe/yardrunner`
   repository, branch `main`.
3. Attach a MySQL database. Production runs on MySQL, and so does the whole
   test suite in CI.
4. Build commands:

    ```
    composer install --no-dev --optimize-autoloader
    npm ci
    npm run build
    ```

    The build runs `php artisan wayfinder:generate` through the Vite
    plugin, so PHP dependencies must be installed before `npm run build`.

5. Deploy commands:

    ```
    php artisan migrate --force
    ```

6. Turn on the scheduler. With `YARDRUNNER_DEMO=true` it rebuilds the demo
   yard at 3:00 a.m. Toronto time every night, and only the demo yard.

7. Turn on a queue worker for the app (QuickBooks sync, alerts and
   subscription updates run on the queue). `QUEUE_CONNECTION=database` is
   fine to start.

## Environment

Laravel Cloud sets `APP_KEY`, `APP_URL` and database variables. Add the
variables below, plus the ones in each service's file. Environments set up
before the rename may still use `YARDLINE_*` names; they keep working, but
rename them to `YARDRUNNER_*` (a `YARDRUNNER_*` variable wins if both are
set), then the fallback in `config/yardrunner.php` can go.

```
APP_NAME=YardRunner
APP_ENV=production
APP_DEBUG=false
YARDRUNNER_DEMO=true
```

The fake payments driver refuses to run in production. A staging
environment can use Stripe test keys with the same drivers.

## Edge cache

The home page and the help center (`routes/marketing.php`) are served from
Laravel Cloud's edge cache, which runs on Cloudflare:

- They run in the `marketing` middleware group: no session, no cookies, no
  CSRF token, no signed-in user. Cloudflare won't cache a response that sets
  a cookie.
- `ServeFromEdgeCache` sends `Cache-Control: public, max-age=300,
  s-maxage=3600` on successful pages and `no-store` on anything else.
- The edge keys on the URL alone and ignores `Vary: X-Inertia`, so these
  pages are always loaded whole: Inertia visits to them get a 409 with a
  location, and the browser turns them into full page loads before they're
  sent (`resources/js/lib/edge-cached-pages.ts`).
- **Turn on "purge edge cache on deploy"** in the environment's network
  settings, so a deploy never leaves the edge serving pages that point at
  old built assets. Without it, pages can be up to an hour old after a
  deploy.

To add a page to the marketing site, put its route in `routes/marketing.php`
and its path in `isEdgeCachedPath()`; it must render the same for everyone.

## Demo yard

Run once from Cloud's Commands tab to create it:

```
php artisan yardrunner:demo:reset --force
```

It's then reachable at `/demo`, linked from the home and login pages.
Demo accounts have random passwords, can only be entered through `/demo`,
and can't change account or business settings. The contractor portal for the demo
yard is at `/portal/escarpment-stone` with a one-click "try it" button. The demo yard always uses
flat-rate tax and fake card payments, so it never creates Stripe Tax
transactions or Checkout sessions on your live account.
