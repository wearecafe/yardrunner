# Resend

Every email YardRunner sends goes through Resend: password resets, email
verification, invites, quotes, statements, rental confirmations, signing
links, reminders and receipts. Yards never see Resend's name: the sending
domain flow is white-labelled (see [email-branding.md](email-branding.md)).

## Who sends what

- Emails to a yard's own users come from `MAIL_FROM_ADDRESS` in
  YardRunner's brand (`BrandedMail::user`).
- Emails to a yard's customers and suppliers carry the yard's brand
  (`BrandedMail::customer`, `YardMail::for`) and come from
  `<slug>@YARDRUNNER_EMAIL_TENANT_FROM_DOMAIN` under the yard's name (verify
  that domain in Resend too; blank sends from `MAIL_FROM_ADDRESS`), or from
  the yard's own domain once it's connected. Replies go to the reply-to
  address set in Settings → Business.
- The demo yard never emails its customers.

## Yards' own sending domains

Registered with Resend through its domains API (same `RESEND_API_KEY`),
with the return path set to `yr`. Resend's DKIM and bounce records are
published on `YARDRUNNER_EMAIL_DNS_ZONE` through Cloudflare (see
[cloudflare-dns.md](cloudflare-dns.md)), and yards add CNAMEs pointing
there, so Resend never appears in their DNS except the fixed DKIM host name
`resend._domainkey` (accepted Oct 27, 2026).

- Code: `App\Email\Domains` (`SendingDomains::connect`), behind the
  `yardrunner.email.domains_driver` setting (`resend` or `fake`).
- The scheduler runs `yardrunner:email:domains` every 10 minutes: waiting
  domains are checked for 72 hours, verified ones hourly.
- Resend's error messages are mapped to YardRunner's own before they reach
  a screen or a log a yard can see.

**Before launch:** connect one real test domain and confirm Resend verifies
DKIM and the bounce MX/SPF records when they're reached through these
CNAMEs. If it doesn't, the fallback is to show the yard Resend's own
records. This is UAT row EM-1 in [email-branding.md](email-branding.md#uat-rows).

## Environment

```
MAIL_MAILER=resend
RESEND_API_KEY=re_...
MAIL_FROM_ADDRESS=...        # on a domain verified in Resend
MAIL_FROM_NAME=YardRunner
YARDRUNNER_EMAIL_TENANT_FROM_DOMAIN=mail.yardrunner.ca  # customer emails: <slug>@this
YARDRUNNER_EMAIL_DOMAINS_DRIVER=resend   # fake locally and in the demo yard
```

## Not built yet

Delivery and bounce webhooks (an email log per order and rental, flagging
bounced addresses). See [gap-list.md](gap-list.md#not-built-yet).
