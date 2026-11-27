# Back office

`/admin`, for YardRunner staff. Staff are users with no tenant and
`is_platform_staff` set; add or remove them with
`php artisan yardrunner:staff:add you@example.com --name="Your Name"`
(`--remove` takes access away). There's no screen for it on purpose.
Staff must turn on two-factor authentication before `/admin` opens
(`YARDRUNNER_ADMIN_REQUIRE_2FA=false` switches that off locally). Tenant
users get a 404 there, and staff are sent back to `/admin` from the yard
screens (`KeepStaffInBackOffice`).

| Screen             | What's on it                                                                                                                 | Code                                               |
| ------------------ | ---------------------------------------------------------------------------------------------------------------------------- | -------------------------------------------------- |
| Overview           | Tenants, trials, MRR, sales and orders across tenants, busiest tenants, what needs attention, growth, module adoption        | `Admin\OverviewController`, `BackOffice\Attention` |
| Tenants            | Every tenant with status, plan, connections, activity and health; set up a new tenant (emails the owner)                     | `Admin\TenantController`                           |
| Tenant page        | 30-day activity, integrations, yards, users (send a password link), audit log; subscription, trial end, suspend, staff notes | `Admin\TenantController`                           |
| Activity reports   | Volume per week, module usage by tenant, engagement and churn risk, retention by signup month                                | `Admin\ReportController`                           |
| Integration health | Webhooks, QuickBooks connections, open issues, failed jobs (retry or clear)                                                  | `Admin\IntegrationHealthController`                |

Figures come from `App\BackOffice\TenantMetrics`, which reads tables
directly and groups by `tenant_id` (staff have no tenant, so scoped models
would return nothing). Health and churn risk are in `TenantHealth`.

- **Plan, price and status** follow the tenant's trial, add-ons and
  Stripe subscription (see [Billing](billing.md)). Staff can move the trial
  end (also on Stripe while the subscription is trialing), suspend or
  restore access, and open the customer and subscription in Stripe. MRR
  is the sum of active and past-due tenants' monthly prices.
- **Add-ons** (Rentals, Time and attendance) are switched on by the
  tenant under Settings → Billing, not by staff.
- **Activity**: `TrackActivity` notes each user's last visit, the
  tenant's, and one `user_activity_days` row per user per day (weekly
  users, retention), writing at most every five minutes per user.
- **Webhooks**: every Stripe delivery is logged in
  `webhook_deliveries` (accepted, ignored, rejected or failed), kept 30
  days.
- Staff changes to a tenant are written to that tenant's audit log, but
  the tenant's own audit page leaves them out.
- New sign-ups and tenants staff set up start a 14-day trial.
- **Email branding**: staff switch on the full colour system for a
  tenant that asks for it (off by default), and can turn off the "Sent
  with YardRunner" line on its customer emails (on by default). Both live
  in `tenants.settings['branding']` and each change is audited.
