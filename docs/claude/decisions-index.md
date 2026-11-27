# Decisions index

Every decision Jon has made about YardRunner, where its detail lives, and
the questions still open. Read this before shaping or building anything:
never re-ask or undo a decision listed here. When a decision is made, add
it to the log (newest first) and to the area's doc; when an open question
is answered, move it into the log.

## Where things are

| Area                                 | Doc                                                                                      |
| ------------------------------------ | ---------------------------------------------------------------------------------------- |
| How we work                          | [CLAUDE.md](../../CLAUDE.md) → Workflow, [delivery-checklist.md](delivery-checklist.md)  |
| What's built and where the code is   | [features.md](features.md)                                                               |
| What's half-built or missing         | [gap-list.md](gap-list.md), [features.md](features.md#not-built-yet)                     |
| Commerce, pricing, pallets, roles    | [commerce-data-model.md](commerce-data-model.md)                                         |
| Access model                         | [permissions.md](permissions.md)                                                         |
| Security rules, guest routes, checks | [security.md](security.md)                                                               |
| Setup for new businesses             | [tenant-setup.md](tenant-setup.md)                                                       |
| Emails and branding                  | [email-branding.md](email-branding.md), [email-branding-spec.md](email-branding-spec.md) |
| QuickBooks Online                    | [quickbooks.md](quickbooks.md), [quickbooks-sync-map.md](quickbooks-sync-map.md)         |
| Profit and loss report               | [profit-report.md](profit-report.md)                                                     |
| Time and attendance                  | [time-and-attendance.md](time-and-attendance.md)                                         |
| Design system                        | [design-system.md](design-system.md), [mockups](mockups)                                 |
| Demo yard and UAT                    | [demo-and-uat.md](demo-and-uat.md)                                                       |
| Market and competitors               | [competitive-research.md](competitive-research.md)                                       |
| Outside services                     | One file each; listed in [features.md](features.md#integrations)                         |

## Decision log

Newest first. "Turned down" notes the alternatives Jon rejected where it
matters.

### Nov 27, 2026

- **Project knowledge lives only in `docs/claude/`.** Everything that was in
  the claude.ai Project's docs, the claude.ai docs (QuickBooks sync map,
  email branding spec, P&L report, time and attendance) and the gap list
  page now lives here; `docs/` holds nothing else. The workflow lives only in
  `CLAUDE.md`. Turned down: keeping `features.md`, the scope docs and the
  deploy docs outside `docs/claude/`.
- **Each outside service has its own file** in `docs/claude/`.
- **Marketing pages (home and help center) set no cookies and are cached at
  the edge.** Fixed in its own PR. Turned down: logging it as a gap.
- **A `/decision-batch` skill** is drafted for Jon to save, for batching
  open questions into multiple-choice rounds.
- **Production database is MySQL.**

### Oct 29, 2026

- **Product renamed from Yardline to YardRunner** (repo, config and env
  names; `YARDLINE_*` variables still read as a fallback).

### Oct 27, 2026

- **Every employee is a user**; Team → Employees adds or picks a user. No
  job titles. Deactivating a user ends their employment and keeps their
  hours. ([commerce-data-model.md](commerce-data-model.md#decisions))
- **Time and attendance is $5 CAD per tracked person a month**, billed per
  employee with Clocks in on whose employment hasn't ended.
- **Eleven job-based roles**, several per user, yard-scoped except Owner and
  Admin; cross-role judgement calls are permissions with role defaults;
  per-role refund limits with on-the-spot approval. Rental desk is its own
  role. Staff became Sales, Order picker became Yard crew.
  ([commerce-data-model.md](commerce-data-model.md#roles-and-permissions))
- **Payment on delivery** through a permission held by Drivers, with a
  Stripe LTE reader per truck, an external terminal or optional cash.
  (Spec only; not built.)
- **Stripe Connect, white-labelled:** a connected account per yard, no
  Stripe dashboard, direct charges, 0.5% application fee; yards can't link
  their own Stripe account. ([stripe.md](stripe.md))
- **Emails:** the recipient decides the brand; customer emails go from the
  yard's mailboxes once its domain is verified; domain verification is
  white-labelled and every record points at yardrunner.ca; the DKIM host may
  read `resend._domainkey`; "Sent with YardRunner" is on by default and
  only staff turn it off per tenant, not tied to a plan; the full colour
  system is off by default and staff turn it on per tenant; Domain Connect
  is wanted but waits for DNS hosts to approve YardRunner.
  ([email-branding.md](email-branding.md#decisions))
- **Setup for new businesses:** only the business and first yard are
  required; everything else is optional and stays on a checklist.
  ([tenant-setup.md](tenant-setup.md))
- **Pallet usage fees** are a second, non-refundable pallet charge, one
  amount per tenant or yard, taxed like the product, credited only when
  unused product comes back on full pallets.

### Oct 26, 2026

- **No Shopify.** YardRunner is the only sales channel and point of sale.
  Stripe for payments and Stripe Tax for tax; QuickBooks Online receives
  the accounting. ([commerce-data-model.md](commerce-data-model.md#decisions))
- **Pricing:** contract price, then price list (product rules or category
  rules, with a per-list override and optional stacking with volume
  breaks), then base price; products can be exempt from price lists; manual
  discounts on exempt products by permission, always logged.
- **Units:** selling units can be switched off for sale; each product
  refuses, rounds up or rounds down quantities that don't fit.
- **Credit:** orgs buy on account up to a limit; above it, a card unless
  someone with Override credit limit overrides.
- **Fulfillment stages and quality-check sign-off** are switched on per
  tenant; sign-off roles per stage; the signer must differ by default.
- **Rentals** are invoiced separately from goods and prepaid with a
  deposit.
- **Delivery pricing:** zone, distance and truckload are all supported; the
  tenant picks.
- **QuickBooks sync map decisions 1–7** as recommended: one shared "Cash
  sales" customer, job sites as ship-to only, payments entered in
  QuickBooks pulled back, products as non-inventory items, prepaid sales as
  invoice plus payment, rental invoice at check-out updated at return, a
  QuickBooks Location per yard when there's more than one. Refunds post as
  a journal entry linked to the credit memo.
  ([quickbooks-sync-map.md](quickbooks-sync-map.md#decisions-to-confirm))
- **P&L:** gross profit only (operating expenses stay in QuickBooks);
  owners see it, admins only if the owner allows; accrual by invoice date;
  moving weighted average cost per product per yard, fixed on each line.
  ([profit-report.md](profit-report.md))
- **Time and attendance:** QuickBooks Online and ADP exports plus an hours
  summary; take-home trucks can clock in away from the yard; salaried staff
  clock in per person; kiosk photo off by default; one overtime rule set per
  tenant; no pay rates stored; a paid add-on per user.
  ([time-and-attendance.md](time-and-attendance.md#phasing-and-decisions))

## Open questions

Batch these with `/decision-batch`. Anything legal or compliance-related is
described with its rule and left for Jon to choose.

| Area       | Question                                                                                                                           | Source                                                                           |
| ---------- | ---------------------------------------------------------------------------------------------------------------------------------- | -------------------------------------------------------------------------------- |
| QuickBooks | Sync suppliers, purchase orders and bills (decision 8), or keep bills entered in QuickBooks?                                       | [quickbooks-sync-map.md](quickbooks-sync-map.md#decisions-to-confirm)            |
| Payments   | Stripe readers: does YardRunner sell and ship them (at cost or marked up), or do yards order from Stripe?                          | [commerce-data-model.md](commerce-data-model.md#open-questions)                  |
| Sales      | Online ordering for goods: contractor portal only, or also a public storefront?                                                    | [commerce-data-model.md](commerce-data-model.md#open-questions)                  |
| Sales      | Promo codes or automatic discounts at launch? (They'd skip exempt products.)                                                       | [commerce-data-model.md](commerce-data-model.md#open-questions)                  |
| Rentals    | One account and statement for a customer who rents and buys?                                                                       | [competitive-research.md](competitive-research.md#open-questions-before-mockups) |
| Rentals    | Damage waiver: built in or through a partner? (Insurance rules may apply.)                                                         | [competitive-research.md](competitive-research.md#open-questions-before-mockups) |
| P&L        | Cash-basis toggle? Freight as landed cost? Waiver claims as a rental cost? Inter-yard transfers shown? Target margin per category? | [profit-report.md](profit-report.md#decisions-and-open-questions)                |
| Demo yard  | Owen Bakker's `/demo` card says he never sees prices, but his Purchasing role shows them. Change the card or his roles?            | [demo-and-uat.md](demo-and-uat.md)                                               |
| Research   | Validate the rental module with 5 to 10 yard owners                                                                                | [competitive-research.md](competitive-research.md#open-questions-before-mockups) |
