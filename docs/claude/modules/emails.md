# Emails and branding

Every email goes through one layout, `resources/views/mail/branded.blade.php`
(and `branded-text`), drawn in one of two brands. The recipient decides
which:

| Brand      | Who gets it                                                                                                                 | Start from                                                                    | From                                                                                           |
| ---------- | --------------------------------------------------------------------------------------------------------------------------- | ----------------------------------------------------------------------------- | ---------------------------------------------------------------------------------------------- |
| YardRunner | The yard's own users: invites, password resets, verification, time corrections, QuickBooks failures, sending-domain notices | `BrandedMail::user($business, $why)`                                          | `MAIL_FROM_ADDRESS`, as YardRunner                                                             |
| The yard   | Its customers and suppliers: quotes, statements, rentals, purchase orders, portal sign-in links                             | `BrandedMail::customer($tenant, $group, $yard)` (`YardMail::for` is the same) | The yard's mailbox for that group, or `<slug>@tenant_from_domain` until its domain is verified |

`BrandedMessage` is Laravel's `MailMessage` plus `title()`, `facts()` (a key
facts box; empty values are left out) and `notice()` (a banner). Customer
emails show the yard's address and tax numbers and a reply line; the
"Sent with YardRunner" line goes under them unless staff turn it off.

**Branding** (Settings → Branding, `Settings\BrandingController`, owners
and admins). Logo (PNG or JPG, served publicly at `/brand/{slug}/logo`
because email apps don't sign in), primary colour, header style (light or
brand), tax numbers and footer note, with a live preview and a test send.
Stored in `tenants.settings['branding']` (`App\Email\BrandSettings`).

**Colour system** (`App\Email\ColourSystem`, mirrored in
`resources/js/lib/colour-system.ts`; change both together). One primary
colour makes an 11-step OKLCH ramp; roles are picked by contrast, so body
text, button text, links and the key facts box always reach 4.5:1 whatever
the primary. Editing single colours is off by default: staff switch it on
per tenant in the back office. With it off, earlier changes are kept but
ignored. With it on, a change that fails a required check can't be saved.

**Sending domain** (Settings → Sending domain,
`Settings\SendingDomainController`, `App\Email\Domains`). A white-label
flow: nothing a yard sees names the email service.

1. The yard enters its domain and main mailbox. Free mail domains and
   yardrunner.ca are refused.
2. `SendingDomains::connect` registers it with the service (return path
   `yr`), publishes the service's real records on YardRunner's zone
   (`dkim-{tenant}.dns.yardrunner.ca`, `bounce-{tenant}…`) and gives the
   yard CNAMEs pointing there, plus an optional DMARC record.
3. `yardrunner:email:domains` runs every 10 minutes: domains still waiting
   are checked for 72 hours (then "expired" until **Check now**), verified
   ones hourly. Verified means every required CNAME resolves and the
   service passes the domain.
4. Owners and admins turn on sending and name mailboxes per `MailGroup`
   (main, accounts, orders, delivery, rentals; blank uses main).
5. A verified domain that stops passing is marked "needs attention",
   customer emails fall back to YardRunner's address on their own, and
   owners and admins are emailed (`SendingDomainMessage`). Sending resumes
   when the records are back.

Drivers: `yardrunner.email.domains_driver` (`resend` or `fake`) and
`yardrunner.email.dns_driver` (`cloudflare` or `local`). The demo yard and
local development use the fakes. Tests answer DNS with
`DnsLookup::fake()`.
