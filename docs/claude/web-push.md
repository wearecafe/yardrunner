# Web push

Staff alerts on phones and tablets are web push notifications sent with
`minishlink/web-push` and a VAPID key pair; no third-party push service is
involved beyond each browser's own push endpoint. What alerts exist and who
gets them is in [features.md](features.md#installable-app-and-alerts).

## Keys

Run `php artisan yardrunner:push:keys` once, put the output in the
environment and keep it: a new pair stops alerts on every device until each
one opens YardRunner again. Without keys, alerts are written to the log and
Settings → Alerts says they aren't set up.

```
VAPID_PUBLIC_KEY=...         # php artisan yardrunner:push:keys
VAPID_PRIVATE_KEY=...
VAPID_SUBJECT=mailto:support@yardrunner.ca
```

Alerts are queued notifications (`WebPushChannel`), so they need the queue
worker running. Alerts are off in the demo yard. On iPhone and iPad, web
push only works from the installed app.
