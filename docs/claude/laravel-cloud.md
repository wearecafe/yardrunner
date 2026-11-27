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
3. Attach a MySQL database. Production runs on MySQL; the `mysql` CI job
   runs the raw SQL and every demo-yard page against MySQL 8.4 because the
   main suite uses SQLite.
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
