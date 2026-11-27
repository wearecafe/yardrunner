# Billing

What tenants pay YardRunner: a base subscription ($749 CAD a month),
Rentals ($199 a month) and Time and attendance ($5 per person tracked a
month), plus tax. Amounts and Stripe lookup keys are in
`config/yardrunner.php` (`billing`); the marketing home page's pricing table
reads them too. Billed through Stripe Billing on YardRunner's own Stripe
account, never on a yard's connected account. Code is in `app/Billing`.

| What                                                                            | Where                      | Code                                   |
| ------------------------------------------------------------------------------- | -------------------------- | -------------------------------------- |
| 14-day trial on sign-up (today is day 1); every add-on can be switched on free  | Settings → Billing         | `Trial`, `AddOns`, `BillingController` |
| Add payment details in Stripe Checkout; the trial carries over                  | Settings → Billing         | `StripeBillingGateway::checkoutUrl`    |
| Card, address, invoices and cancelling in Stripe's customer portal              | Settings → Billing         | `StripeBillingGateway::portalUrl`      |
| Add-on or tracked-people changes update the subscription, prorated              | Billing, Time, Employees   | `Billing::changed`, `SyncSubscription` |
| Subscription state copied from Stripe's webhooks                                | `/webhooks/stripe/billing` | `StripeBillingWebhookController`       |
| Locked when the trial ends unpaid, unpaid after retries, cancelled or suspended | Everywhere                 | `BillingState`, `EnsureBilling`        |
| Rentals screens and the booking page only with the add-on on                    | Rentals                    | `EnsureRentals`                        |

- **Access** is worked out on every request from the trial date and the
  last subscription status Stripe sent (`BillingState`), so a trial ends
  at midnight in the yard's time zone with nothing scheduled. Past due
  keeps working while Stripe retries; `unpaid`, `canceled` or a suspension
  by staff locks the tenant. Owners and admins land on Settings → Billing;
  everyone else on a paused page. Profile, security, sign-out and help
  still work, and the contractor portal and booking page close.
- **Time and attendance seats** are users set up as employees with
  "Clocks in" on (`tracks_time`), whose employment hasn't ended and whose
  user is active (`Plan::seatsFor`). Users who aren't employees aren't
  billed. Adding, archiving or changing one, or deactivating their user,
  queues `SyncSubscription`, which
  waits 30 seconds so a burst of changes becomes one update.
- **Checkout's trial** must end at least 48 hours out, so a tenant
  subscribing in the last two days of their trial gets the difference
  free; the trial end Stripe sends back becomes `trial_ends_at`.
- **Prices** are found in Stripe by lookup key. `php artisan
  yardrunner:billing:stripe-setup` creates the products and prices (and,
  with `--webhook=<url>`, the webhook). Change an amount in config and
  run it again: it adds a new price and moves the lookup key, and each
  subscription moves to it on its next change.
- `php artisan yardrunner:billing:refresh` runs nightly to update the
  status shown in the back office (Trial → Trial ended).
- Locally `YARDRUNNER_BILLING_DRIVER=fake` subscribes on the spot with no
  Stripe. The demo yard is never billed.
