# YardRunner Email Branding

Oct 27, 2026 · @Jon Moore

> Moved here from the claude.ai doc "YardRunner Email Branding" on Nov 27, 2026. This is the full spec with the email catalogue and layout rules. The summary the code follows is [email-branding.md](email-branding.md); where they differ, the summary and the code win, and what isn't built yet is listed in [gap-list.md](gap-list.md#not-built-yet).

## Overview

Every email YardRunner sends uses one of two brands, decided by who receives it. Emails to the tenant's own users carry the YardRunner brand. Emails to the tenant's customers (contractors, retail and rental customers) carry the tenant's brand, so the yard looks like it sent them.

| Brand      | Who receives it                                                    | Looks like it's from              | Sent from (default)                                                                  |
| ---------- | ------------------------------------------------------------------ | --------------------------------- | ------------------------------------------------------------------------------------ |
| YardRunner | Tenant users (Owner, Admin, Sales, Yard crew and every other role) | YardRunner                        | YardRunner <notifications@mail.yardrunner.ca>                                        |
| Tenant     | The tenant's customers: org contacts, retail and rental customers  | The yard (e.g. Stoneworks Supply) | Stoneworks Supply <stoneworks@mail.yardrunner.ca>, or the yard's own verified domain |

YardRunner billing emails to the tenant (trial, subscription, invoices from YardRunner) are YardRunner-branded too, since YardRunner is the sender. Tenant-branded emails carry a small "Sent with YardRunner" line in the footer; YardRunner staff can turn it off per tenant from the back office.

Custom sending domains are set up entirely inside YardRunner under Settings → Email. The email provider is never named in the app, the DNS records or the help docs.

## Which brand each email uses

The recipient decides the brand, not the email type. A customer who is also a tenant user (rare, e.g. an owner buying on a test account) gets the brand of the context the email comes from.

### Tenant-branded: emails to the tenant's customers

| Email                                  | Trigger                                              | Attachment or link                             |
| -------------------------------------- | ---------------------------------------------------- | ---------------------------------------------- |
| Quote                                  | Quote sent or revised                                | Quote PDF, accept link                         |
| Quote expiring                         | 3 days before expiry (tenant can change or turn off) | Quote link                                     |
| Order confirmation                     | Order confirmed on any channel                       | Order link in the contractor portal            |
| Receipt                                | Counter or online payment taken                      | Receipt PDF                                    |
| Invoice                                | Goods invoice issued                                 | Invoice PDF, pay link                          |
| Rental invoice                         | Rental invoice issued (separate from goods)          | Invoice PDF, pay link                          |
| Statement                              | Monthly statement for orgs on account                | Statement PDF                                  |
| Payment reminder                       | Invoice overdue (schedule set by Accounts)           | Pay link                                       |
| Credit note and refund                 | Return, pallet return or refund processed            | Credit note PDF                                |
| Delivery scheduled                     | Dispatch assigns a delivery window                   | Tracking link                                  |
| Out for delivery / ETA                 | Truck leaves the yard                                | Tracking link                                  |
| Delivered                              | Proof of delivery taken                              | Proof of delivery PDF with photo and signature |
| COD payment received                   | Driver collects payment on delivery                  | Receipt PDF                                    |
| Rental booking confirmation            | Reservation made                                     | Booking link                                   |
| Rental agreement to sign               | Agreement sent for e-signature                       | Signing link                                   |
| Signed agreement copy                  | Agreement signed                                     | Agreement PDF                                  |
| Pickup and return reminders            | Day before pickup or return                          | Booking link                                   |
| Rental overdue                         | Unit not returned on time                            | Extension link                                 |
| Damage and late charges                | Return inspection adds charges                       | Inspection photos, invoice                     |
| Contractor portal invite               | Org contact invited to the portal                    | Set-up link                                    |
| Portal password reset and sign-in code | Contact requests it                                  | One-time link or code                          |
| Portal order needs approval            | Buyer places an order above their limit              | Approve link (to the org's approver)           |

### YardRunner-branded: emails to tenant users

| Email                                                   | Recipient                            |
| ------------------------------------------------------- | ------------------------------------ |
| Welcome, invite to tenant, password reset, sign-in code | The user                             |
| Role or yard assignment changed                         | The user                             |
| Low stock and cost-change alerts                        | Purchasing & inventory, Owner, Admin |
| Order awaiting quality check                            | Users who can sign off that stage    |
| Shortage, substitution or damage flagged                | Sales and Admin at that yard         |
| COD stop unpaid                                         | Dispatcher and Accounts              |
| QuickBooks Online sync errors                           | Accounts, Admin                      |
| Credit limit override or refund approval requested      | Holders of the permission            |
| Timesheets ready to approve                             | Holders of Approve timesheets        |
| Unit due for service or locked out                      | Service tech                         |
| Sending domain verified, failing or disconnected        | Owner, Admin                         |
| Trial ending, subscription receipts, payment failed     | Owner (billing contact)              |

Web push stays the main channel for live alerts in the PWA. Each user can turn alert emails off per type; account and security emails can't be turned off.

## YardRunner brand

YardRunner emails use the app's light concrete and safety orange palette, so they look like the app the user just left.

| Token         | Value   | Used for                                     |
| ------------- | ------- | -------------------------------------------- |
| Concrete      | #EEECE8 | Email background around the card             |
| Surface       | #FFFFFF | Card background                              |
| Ink           | #1F1F1C | Headings and body text                       |
| Muted         | #6B6862 | Secondary text, footer                       |
| Line          | #D9D6D0 | Dividers, table borders                      |
| Safety orange | #F26A1B | Top bar, buttons (with Ink text), highlights |
| Alert red     | #B42318 | Failures and overdue items                   |

- **Type:** system font stack (-apple-system, Segoe UI, Roboto, Helvetica, Arial). Heading 22px bold, body 15px, line height 1.5. No web fonts, since Outlook and Gmail drop most of them.
- **Logo:** YardRunner wordmark as a 2x PNG, 160px wide, hosted on yardrunner.ca. Never SVG, which Gmail and Outlook don't show.
- **Header:** a 4px safety orange bar, then the wordmark on the left. The tenant's name sits on the right when the email is about one tenant (e.g. "Stoneworks Supply · Burlington yard").
- **Buttons:** safety orange with Ink text, 6px radius, 44px tall. Orange with white text fails contrast, so it isn't used.
- **Footer:** why they got it ("You're getting this because you're Sales at Stoneworks Supply"), a link to notification settings for alert emails, YardRunner's mailing address and yardrunner.ca.

## Tenant branding settings

Owner and Admin set the tenant's brand once under Settings → Branding, and it applies to customer emails, PDFs (quotes, invoices, receipts, statements, agreements), the contractor portal and the rental booking page. A tenant that sets nothing still gets clean, neutral customer emails with its business name.

| Setting                            | Where                        | Values                                                                                     | Default                        |
| ---------------------------------- | ---------------------------- | ------------------------------------------------------------------------------------------ | ------------------------------ |
| Business name shown                | Tenant, overridable per yard | Text, 60 characters                                                                        | Tenant name                    |
| Logo                               | Tenant, overridable per yard | PNG, JPG or SVG upload, up to 2 MB; stored as a 2x PNG for email                           | None (business name in bold)   |
| Logo on dark background            | Tenant                       | Optional second logo, used in the PDF and portal headers if the tenant picks a dark header | None                           |
| Brand colour                       | Tenant                       | Primary colour (hex, or picked from the logo); generates the colour system below           | Neutral charcoal #2B2B28       |
| Button text colour                 | Tenant                       | Part of the colour system (On primary)                                                     | Automatic                      |
| Header style                       | Tenant                       | Light (logo on white, brand colour bar) / Brand (logo on brand colour)                     | Light                          |
| Contact block                      | Per yard                     | Phone, email, address, hours                                                               | Yard's address and phone       |
| Reply-to address                   | Tenant, overridable per yard | Email address                                                                              | Yard's email, else the Owner's |
| Email footer note                  | Tenant                       | Text, 300 characters (e.g. HST number, terms, a slogan)                                    | Blank                          |
| Social links                       | Tenant                       | Website, Facebook, Instagram, LinkedIn                                                     | None                           |
| "Sent with YardRunner" footer line | Tenant                       | On/off, set per tenant by YardRunner staff in the back office                              | On                             |

- **Per-yard overrides:** a multi-yard tenant can trade under different names (e.g. a stone yard and a landscape yard). Emails about an order use the brand of the order's yard; statements and portal emails use the tenant brand.
- **Contrast guard:** the colour system never lets a customer email ship text or buttons that fail contrast (see Colour system).
- **Colour from logo:** on upload, YardRunner suggests up to three colours taken from the logo.
- **Logo processing:** uploads are trimmed of empty margins, scaled to fit 200 × 60px (shown at half size for sharp screens), and flattened onto white if they have transparency that would vanish in dark mode.
- **No custom HTML or CSS.** Tenants change the brand, not the layout. This keeps every email readable in every client and keeps the support load down. Tenants can edit the opening paragraph of each customer email (see Layout).

### Colour system

The colour system is off by default. YardRunner staff switch it on per tenant in the back office tenant settings, only for tenants that ask for it. While it's off, the tenant picks a primary colour only; YardRunner still generates the tokens below behind the scenes so emails stay readable, but the tenant can't see or edit them. While it's on, the tenant enters one primary colour and YardRunner generates a full colour system from it. Every generated colour can then be changed by hand, and hand-changed colours survive later regeneration.

**Generated tokens**

| Token                                     | Generated as                                                | Used for                                         |
| ----------------------------------------- | ----------------------------------------------------------- | ------------------------------------------------ |
| Primary                                   | The colour entered, unchanged                               | Header bar, button fill, Brand header background |
| Primary hover                             | One step darker on the ramp                                 | Button hover in the portal and booking page      |
| Primary strong                            | Lightest ramp step with 4.5:1 contrast on white             | Links, amounts due, accents in text              |
| Primary subtle                            | Lightest ramp step                                          | Key facts box background, selected rows          |
| Primary border                            | Second-lightest ramp step                                   | Key facts box border, input focus                |
| On primary                                | White or Ink, whichever contrasts more with Primary         | Button text, text on a Brand header              |
| Ink, Muted, Line, Surface, Background     | Greys tinted very slightly with the primary's hue           | Text, dividers, cards, page background           |
| Secondary and Secondary subtle (optional) | Generated the same way if the tenant enters a second colour | Badges, highlights in the portal                 |
| Success, Warning, Danger                  | Fixed hues, lightness tuned to pass contrast                | Status banners (overdue, declined, delivered)    |

**How it's generated**

1. Convert the primary to OKLCH (perceptual lightness, chroma, hue).
2. Build an 11-step ramp (50 to 950) at fixed lightness targets, keeping the hue and easing chroma at the light and dark ends so every step stays in sRGB.
3. Snap the ramp so the entered colour sits exactly on its nearest step; it becomes Primary unchanged.
4. Assign roles by contrast, not by fixed step: Primary strong is the lightest step reaching 4.5:1 on white, On primary is whichever of white or Ink passes.
5. If neither white nor Ink reaches 4.5:1 on Primary (some mid-tone oranges and teals), the header bar keeps Primary and buttons use Primary strong with white text instead. The screen says so in one line.
6. Generate dark-mode values for the portal from the same ramp. Emails use the light values only.

**Customising after generation**

- With the colour system on, Settings → Branding shows each token as a swatch with its hex and a Generated or Custom tag, beside the live email preview.
- Clicking a swatch opens a picker; changing it marks the token Custom. Each token has Reset to generated, and there is Reset all.
- Changing the primary later regenerates only the Generated tokens. Custom tokens stay, with a note if they no longer suit the new primary.
- Every text-on-background pair is checked live. Pairs that customer emails depend on (body text on Surface, button text on button, links on Surface) must reach 4.5:1 to save; other pairs show a warning only.
- Stored per tenant (and per yard override): the primary entered, the generator version, the generated values and the overrides kept separately. A better generator in a later release never changes a tenant's colours until they click Regenerate.
- The same system styles customer emails, PDFs, the contractor portal and the rental booking page. Changes are logged in the audit log, including staff switching the colour system on or off. If it's switched off later, custom colours are kept but ignored and emails go back to the generated colours; switching it on again restores them.

## Sending and custom domains

Customer emails go out from a YardRunner address with the yard's name until the tenant connects its own domain. Connecting a domain is a white-label flow inside YardRunner: the tenant never sees, signs up for or hears about the email provider.

### Senders

| Mode                         | From                                              | Reply-to                                          | Who can choose                              |
| ---------------------------- | ------------------------------------------------- | ------------------------------------------------- | ------------------------------------------- |
| YardRunner address (default) | Stoneworks Supply <stoneworks@mail.yardrunner.ca> | Yard's reply-to address                           | Automatic for every tenant                  |
| Tenant's own domain          | Stoneworks Supply <orders@stoneworkssupply.ca>    | Yard's reply-to address (can be the same mailbox) | Owner or Admin, once the domain is verified |

- The from address on the YardRunner domain is the tenant's slug. The display name is always the business name (or the yard's override).
- Tenant users always receive YardRunner-branded email from notifications@mail.yardrunner.ca, even when the tenant has its own domain.
- If a verified domain starts failing (records removed or changed), customer email falls back to the YardRunner address automatically, so nothing stops sending. Owner and Admin get an email and an in-app banner.

**Mailboxes.** Once a domain is verified, every email to the tenant's customers sends from the tenant's own addresses. The tenant can set a mailbox per email group on that domain; each defaults to the main mailbox, and replies go to the same mailbox unless the yard sets another reply-to.

| Mailbox         | Emails                                                                             | Example                      |
| --------------- | ---------------------------------------------------------------------------------- | ---------------------------- |
| Main (required) | Portal invites, sign-in codes, and any group without its own mailbox               | hello@stoneworkssupply.ca    |
| Accounts        | Invoices, rental invoices, statements, payment reminders, credit notes and refunds | accounts@stoneworkssupply.ca |
| Orders          | Quotes, quote expiry, order confirmations, receipts                                | orders@stoneworkssupply.ca   |
| Delivery        | Delivery scheduled, out for delivery, delivered, COD receipts                      | delivery@stoneworkssupply.ca |
| Rentals         | Bookings, agreements, reminders, overdue notices, damage and late charges          | rentals@stoneworkssupply.ca  |

One-click DNS setup (Domain Connect) needs YardRunner registered as a service provider with a template approved by each DNS host, so the launch list of hosts depends on those approvals; every other host gets the manual steps.

### Connecting a domain (Settings → Email → Sending domain)

1. Owner or Admin enters the domain (stoneworkssupply.ca) and the mailbox name (orders). YardRunner checks it isn't a free mail domain (gmail.com, outlook.com and so on), which can't be verified.
2. YardRunner registers the domain with the provider behind the scenes and publishes the real records on its own DNS, under the tenant's own name on yardrunner.ca.
3. The tenant is shown three records to add, all CNAMEs pointing at yardrunner.ca, each with a copy button and a plain-English note ("proves YardRunner may send for you", "handles bounces"). See the table below.
4. YardRunner detects the DNS host (GoDaddy, Cloudflare, Namecheap, Squarespace, Wix and others). If the host supports automatic setup (Domain Connect), a "Connect automatically" button opens the host's sign-in and adds all three records in one click. Otherwise YardRunner shows short steps for that host. A "Send these to my IT person" button emails the records, YardRunner-branded, to any address.
5. YardRunner checks the records automatically every 10 minutes for 72 hours, and on demand with "Check now". Each record shows its own status.
6. Once all three pass, the domain is Verified. Owner or Admin turns on "Send customer emails from this domain" and sends a test email to themselves.

| Record                                      | Type  | Host the tenant adds | Points to                   |
| ------------------------------------------- | ----- | -------------------- | --------------------------- |
| Signing key (DKIM)                          | CNAME | resend.\_domainkey   | dkim-42.dns.yardrunner.ca   |
| Bounce handling (MX and SPF)                | CNAME | yr                   | bounce-42.dns.yardrunner.ca |
| DMARC (recommended, only if they have none) | TXT   | \_dmarc              | v=DMARC1; p=none;           |

### Domain statuses

| Status                   | Meaning                                      | Customer email sends from               |
| ------------------------ | -------------------------------------------- | --------------------------------------- |
| Not set up               | No domain entered                            | YardRunner address                      |
| Waiting for DNS          | Records shown, not found yet                 | YardRunner address                      |
| Partly verified          | Some records found                           | YardRunner address                      |
| Verified                 | All records pass, sending switched on        | Tenant's domain                         |
| Needs attention          | A record that passed has gone missing        | YardRunner address (automatic fallback) |
| Not found after 72 hours | Auto-checks stopped; "Check now" still works | YardRunner address                      |

### Keeping the provider hidden

- **In the app and help docs:** the screens, emails, help articles and error messages say "YardRunner" or "our email service" only. Provider error codes are mapped to plain messages before they reach the screen or the logs tenants can see.
- **Record values:** every value points at yardrunner.ca. YardRunner owns those names and points them at the provider's real hosts, so the provider can change without tenants touching their DNS.
- **Return path:** the bounce subdomain is set to yr (not the provider's default, send) when YardRunner registers the domain.
- **Limit: the DKIM record name can't be changed.** The provider's signing key always lives at resend.\_domainkey, and the same word appears in each email's signature header. A curious IT person could spot it. The screen calls it "Signing key" and shows the host as given, with no explanation of the name. Accepted (Oct 27): it's fine for the name to show.
- **Test before launch:** confirm the provider's verification accepts records delegated by CNAME to yardrunner.ca, for both the DKIM and the bounce (MX and SPF) records.

## Layout and components

Both brands share one layout and one set of components; only the theme changes. That way a fix or a new email type works in both brands at once.

### Layout, top to bottom

1. **Preheader:** hidden preview text shown in the inbox list (e.g. "Order #10482 · 3 items · Delivery Thu Oct 29").
2. **Header:** brand bar (4px), logo, and on tenant emails the yard name and phone on the right.
3. **Title:** one line saying what happened ("Your order is confirmed", "Invoice #2231 is due Nov 15").
4. **Intro:** one or two sentences. On customer emails, the tenant can replace the default intro per email type (plain text, 500 characters, with merge fields like {customer\_first\_name}, {order\_number}, {yard\_name}).
5. **Key facts box:** order number, date, PO number, job site, delivery window or pickup yard, amount due.
6. **Main button:** one per email (View order, Pay invoice, Sign agreement, Track delivery).
7. **Detail:** line items or the relevant summary (see below).
8. **Contact block:** yard address, phone, hours, and a reply line ("Reply to this email to reach Stoneworks Supply").
9. **Footer:** tenant footer note, social links, "Sent with YardRunner" if on, and an unsubscribe link only on emails that allow it (payment reminders and marketing-like reminders; never receipts, invoices or agreements).

### Components

| Component         | Shows                                                                                                  | Notes                                                                                                                                             |
| ----------------- | ------------------------------------------------------------------------------------------------------ | ------------------------------------------------------------------------------------------------------------------------------------------------- |
| Line items table  | Product, quantity with selling unit (3 tons, 2 pallets), unit price, line total                        | Lines show the unit sold, matching the order. Pallet deposit and pallet usage fee are their own lines, labelled "refundable" and "non-refundable" |
| Totals            | Subtotal, delivery fee, pallet deposits, pallet usage fees, HST or GST + QST, total, paid, balance due | Tax shown with the tenant's tax numbers                                                                                                           |
| Rental summary    | Unit, dates and times, rate basis, deposit held, damage waiver                                         | Rental emails only                                                                                                                                |
| Delivery card     | Window, truck, driver first name, tracking link, delivery notes                                        | No driver phone number                                                                                                                            |
| Proof of delivery | Photo thumbnail, signer name, time                                                                     | Full-size images in the PDF                                                                                                                       |
| Status banner     | Overdue, card declined, needs attention                                                                | Alert red, used sparingly                                                                                                                         |
| Attachment note   | "Invoice #2231 is attached as a PDF"                                                                   | Attachments over 5 MB become links                                                                                                                |

### Rendering rules

- **Width:** 600px card, single column, stacking cleanly at phone width. Tables built for Outlook (no flexbox or grid).
- **Dark mode:** colours chosen to survive Gmail and Apple Mail dark-mode inversion; logos sit on a white rounded tile so dark logos stay visible.
- **Plain text:** every email has a plain-text version built from the same content.
- **Accessibility:** real text (never text in images), alt text on logos, 4.5:1 contrast for body text, buttons at least 44px tall, language set to en-CA (fr-CA when French arrives).
- **Language:** customer emails follow the customer's language when set, else the tenant's; English at launch, French for Quebec next.
- **Money and dates:** CAD with $ and two decimals; dates like "Thu Oct 29, 2026" in the yard's time zone.
- **Privacy by role carries into email:** a delivery email never shows prices to the driver; alert emails to Yard crew never include prices or totals, matching the Sensitive data by role table.

## Permissions, previews and logging

Branding and sending domains are Settings, so Owner and Admin manage them; nobody else can change how the tenant appears to customers.

| Action                                                                      | Owner | Admin | Others                                                            |
| --------------------------------------------------------------------------- | ----- | ----- | ----------------------------------------------------------------- |
| Edit branding, per-yard overrides, intros and footer                        | Yes   | Yes   | No                                                                |
| Connect, change or remove a sending domain                                  | Yes   | Yes   | No                                                                |
| Turn off "Sent with YardRunner" (YardRunner staff only, in the back office) | No    | No    | No                                                                |
| Preview and send test emails                                                | Yes   | Yes   | No                                                                |
| See the customer email log for an order, invoice or rental                  | Yes   | Yes   | Sales, Rental desk and Accounts, for records they can already see |
| Resend an email to the customer                                             | Yes   | Yes   | Sales, Rental desk and Accounts, for records they can already see |

- **Live preview:** Settings → Branding shows the chosen email beside the form, with a picker for every customer email type, light and dark mode, and desktop and phone width. It uses sample data from the tenant's own catalogue when there is any.
- **Test send:** sends the previewed email to the signed-in user's address, from the sender it would really use.
- **Email log:** each order, invoice, quote and rental shows the emails sent, to whom, when, and whether they were delivered, bounced or marked as spam. Opens aren't tracked.
- **Bounces:** a hard bounce flags the customer's email address on their record and tells Sales at the yard, so the address gets fixed.
- **Audit log:** changes to branding, intros, footer, reply-to, sending domain and the "Sent with YardRunner" setting are logged with who and when, alongside the existing audit log entries.

## Build notes (Laravel)

One mail layout, a theme object chosen per recipient, and a sender resolver cover everything above.

- **Theme resolver:** `EmailTheme::for($mailable)` returns either the YardRunner theme or the tenant theme (merging tenant settings with the yard's overrides). Mailables declare their audience (`Audience::TenantUser` or `Audience::Customer`), so the brand can't be picked by mistake.
- **Layout:** one Blade layout with components (`x-mail.header`, `x-mail.facts`, `x-mail.button`, `x-mail.line-items`, `x-mail.totals`, `x-mail.footer`), compiled with inlined CSS (e.g. a CSS inliner in the mail pipeline) and a generated plain-text part. Replaces Laravel's default markdown mail theme.
- **Sender resolver:** sets From, Reply-To and the display name from the tenant's domain status. Only domains in Verified status with sending switched on are used; anything else falls back to the YardRunner address.
- **Domains:** a `SendingDomain` model (tenant\_id, domain, mailbox, provider\_domain\_id, status, per-record status, last\_checked\_at). A queued job registers the domain with the provider API (custom return path "yr"), writes the delegated records to the yardrunner.ca DNS zone through the DNS host's API, and a scheduled job polls verification.
- **Provider behind an interface:** `MailDomainProvider` wraps every provider call and maps its errors to YardRunner messages. Nothing provider-specific reaches Inertia props, logs shown to tenants or help docs.
- **Webhooks:** delivery, bounce and complaint events update the email log; handlers are safe to run twice, like the Stripe and QuickBooks Online webhooks.
- **Assets:** logos stored as processed 2x PNGs on a public, cache-friendly URL on yardrunner.ca; the original upload kept for PDFs and the portal.
- **PDFs:** quotes, invoices, statements and agreements use the same theme, so the email and its attachment match.
- **Tests:** snapshot each email type in both brands, plus a no-branding tenant; render checks in Gmail, Outlook (Windows and web) and Apple Mail before launch.

## Decisions and open questions

| Question                       | Decision                                                                                                                                                                                                                                                             |
| ------------------------------ | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Which brand an email uses      | Decided by the recipient: tenant users get YardRunner, the tenant's customers get the tenant's brand                                                                                                                                                                 |
| Sender for customer emails     | Tenant's choice: a YardRunner address with the yard's name by default, or the tenant's own verified domain. Once verified, every customer email (statements and payment reminders included) sends from the tenant's mailboxes                                        |
| Domain verification experience | White-labelled inside YardRunner. The email provider is never named in the app, emails or help docs, and record values point at yardrunner.ca. One-click setup for DNS hosts that support Domain Connect, at launch                                                  |
| How far tenants can customise  | Logo, a primary colour (the full editable colour system is off by default; YardRunner staff switch it on per tenant in the back office on request), header style, contact details, reply-to, footer note, social links and each email's intro. No custom HTML or CSS |
| Per-yard branding              | Name, logo, contact block and reply-to can be overridden per yard                                                                                                                                                                                                    |
| "Sent with YardRunner" footer  | On by default; only YardRunner staff can turn it off, per tenant, in the back office                                                                                                                                                                                 |

- [ ] DKIM record name: accept that the host reads resend.\_domainkey, or move customer sending to a provider that allows custom DKIM selectors? I'm fine with 'resend' being in the dkim record
- [ ] Confirm the provider verifies records delegated by CNAME to yardrunner.ca (DKIM, bounce MX and SPF) Not sure what the question is
- [ ] Should turning off "Sent with YardRunner" be tied to a plan or add-on? No, allow it to be toggled off from the back office only per tenant.
- [ ] Automatic DNS setup with one click for common DNS hosts (a Domain Connect style flow), at launch or later? Yup, sounds good.
- [ ] Should statements and payment reminders go from the Accounts mailbox when a yard sets one? Yes, any email to a tenant's customer should be sent from the tenants mailbox.

**Settled Oct 27.** The DKIM host can read resend.\_domainkey. "Sent with YardRunner" is switched off per tenant in the back office only, not tied to a plan. Domain Connect is wanted, and is listed as not built yet because each DNS host has to approve YardRunner first. Every customer email goes from the tenant's mailboxes once its domain is verified.

**About the CNAME question.** No decision is needed; it's a launch check. Normally a yard would paste the email service's records straight into its DNS. Here the yard instead adds CNAMEs that point at yardrunner.ca, and the real records sit on YardRunner's DNS, which is what keeps the service hidden. Email services are expected to follow those CNAMEs when they verify a domain, but this hasn't been tried against the real service. Before launch, connect one real test domain and confirm it reaches Verified. If it doesn't, the fallback is to show the yard the service's own records.

Sources: [Resend domains overview](https://resend.com/docs/dashboard/domains) · [Create domain API](https://www.resend.com/docs/api-reference/domains/create-domain.md) · [Custom return path](https://resend.com/changelog/custom-return-path.md) (internal reference only; not shown to tenants)
