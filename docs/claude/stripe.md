# Stripe

YardRunner uses Stripe in two separate ways, on the same platform account
(`STRIPE_SECRET`):

| Use                   | Whose money               | Where it runs                                 | Code                      |
| --------------------- | ------------------------- | --------------------------------------------- | ------------------------- |
| Card payments and tax | The yard's sales          | A connected account per yard (Stripe Connect) | `app/Payments`, `app/Tax` |
| Tenant subscriptions  | What yards pay YardRunner | YardRunner's own account (Stripe Billing)     | `app/Billing`             |

Decisions behind this are in
[commerce-data-model.md](commerce-data-model.md#decisions) (Stripe Connect,
tax engine, payment processor) and the billing section of
[modules/billing.md](modules/billing.md).

## Card payments: Stripe Connect, white-labelled

- Every yard takes cards on a connected account YardRunner creates
  (controller properties: no Stripe dashboard, Stripe collects verification
  details and carries negative-balance risk, the yard pays Stripe's fees).
  There is no way to link an outside Stripe account.
- Settings → Payments (`Settings\PaymentsController`, `app/Payments/Connect`)
  creates the account and hosts Stripe's embedded components: onboarding,
  payouts, payments and disputes, business and bank details, tax settings
  and registrations, documents.
- Direct charges: the yard is the merchant of record. Each card payment
  carries YardRunner's application fee
  (`YARDRUNNER_APPLICATION_FEE_PERCENT`,
  `YARDRUNNER_APPLICATION_FEE_FIXED_CENTS`; default 0.5%), set at capture
  for deposit holds. Refunds return the fee in proportion.
- `StripeGateway::requestOptions()` sends requests to the yard's account and
  never falls back to YardRunner's own; `chargeOptions()` also requires
  charges to be on. Card payments are refused until they are.
- `CheckoutLinks` creates Stripe Checkout payment links; the webhook marks
  them paid. Counter card payments are recorded by hand until Stripe
  Terminal is built (see [gap-list.md](gap-list.md)).
- Refunds go back to the card through Stripe (`app/Payments/Refunds`);
  `charge.refunded` keeps them in step.
- Local development and the demo yard simulate setup
  (`FakeConnectAccounts`) and use fake payment links: opening one marks the
  order paid.

## Tax: Stripe Tax

`TaxCalculator` has a Stripe Tax driver (calculation at confirmation,
transaction when the sale completes, reversals when it's undone) and a
flat-rate driver used locally and by the demo yard. Stripe Tax runs on each
yard's connected account: yards add their head office and registrations
under Settings → Payments, Sales tax.

## Tenant subscriptions: Stripe Billing

A base subscription, the Rentals add-on and Time and attendance seats, on
YardRunner's own account and never on a yard's connected account. Amounts
and lookup keys are in `config/yardrunner.php` (`billing`). See
[modules/billing.md](modules/billing.md) for how access follows the subscription.

## Environment

```
YARDRUNNER_TAX_DRIVER=stripe          # flat locally
YARDRUNNER_PAYMENTS_DRIVER=stripe     # fake locally; refuses to run in production
STRIPE_KEY=pk_live_...                # the Payments page loads embedded components with it
STRIPE_SECRET=sk_live_...
STRIPE_CONNECT_WEBHOOK_SECRET=whsec_...
STRIPE_WEBHOOK_SECRET=whsec_...       # optional platform endpoint on the same URL
YARDRUNNER_APPLICATION_FEE_PERCENT=0.5
YARDRUNNER_APPLICATION_FEE_FIXED_CENTS=0

YARDRUNNER_BILLING_DRIVER=stripe      # fake locally: subscribes on the spot
STRIPE_BILLING_WEBHOOK_SECRET=whsec_...
YARDRUNNER_BILLING_AUTOMATIC_TAX=true
```

## Setting up Connect

- Turn on Connect for the platform account, complete the platform profile,
  and set Connect branding (name, icon, colour): Stripe uses it in the
  embedded components and in the few emails it sends yards.
- In Connect settings, set the URLs Stripe's emails send yards to:
  `https://<your-domain>/settings/payments` for onboarding, account
  management, payouts, payments and notifications.
- Add a **Connect** webhook endpoint (events on connected accounts) at
  `https://<your-domain>/webhooks/stripe` for `account.updated`,
  `checkout.session.completed`,
  `checkout.session.async_payment_succeeded`, `charge.refunded` and
  `charge.refund.updated`. Put its
  signing secret in `STRIPE_CONNECT_WEBHOOK_SECRET`.
  (`STRIPE_WEBHOOK_SECRET` is still accepted on the same URL for a platform
  endpoint, if you keep one.)
- Turn on Stripe Tax on the platform account too (it's needed for
  subscription billing below).

## Setting up subscriptions

1. Turn on Stripe Tax with YardRunner's registrations (needed for
   `YARDRUNNER_BILLING_AUTOMATIC_TAX=true`, the default).
2. Create the products, prices and webhook from Cloud's Commands tab:

    ```
    php artisan yardrunner:billing:stripe-setup --webhook=https://<your-domain>/webhooks/stripe/billing
    ```

    Put the printed secret in `STRIPE_BILLING_WEBHOOK_SECRET` and redeploy.
    It's a separate endpoint from the card-payments one, with its own
    secret. (In the dashboard instead: events
    `customer.subscription.created`, `.updated`, `.deleted`, `.paused`
    and `.resumed`.)

3. Set up the customer portal (Settings → Billing → Customer portal):
   allow updating payment methods, billing address and tax IDs, invoice
   history, and cancelling at the end of the period. Turn **off**
   switching plans and changing quantities: YardRunner sets the items.
4. Under Billing → Revenue recovery, keep Smart Retries on and choose
   "mark the subscription as unpaid" after the last retry. Unpaid locks
   the tenant until they update their card; cancelling would end it.
5. Set `YARDRUNNER_BILLING_DRIVER=stripe`.

To change a price, change it in config and run
`yardrunner:billing:stripe-setup` again: it adds a new price and moves the
lookup key, and each subscription moves to it on its next change.

## Webhooks

Every Stripe delivery is logged in `webhook_deliveries` (accepted, ignored,
rejected or failed) for 30 days and shown in the back office under
Integration health. Handlers are safe to run twice.

| Endpoint                   | Controller                       | Secret                                                       |
| -------------------------- | -------------------------------- | ------------------------------------------------------------ |
| `/webhooks/stripe`         | `StripeWebhookController`        | `STRIPE_CONNECT_WEBHOOK_SECRET` (or `STRIPE_WEBHOOK_SECRET`) |
| `/webhooks/stripe/billing` | `StripeBillingWebhookController` | `STRIPE_BILLING_WEBHOOK_SECRET`                              |
