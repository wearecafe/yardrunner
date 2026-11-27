# YardRunner: demo data and UAT

**Editing this file:** several sessions may work at once. Read it immediately before writing, and keep every persona and row already here. New UAT rows go in their feature's own doc, not here.

## Demo personas

Escarpment Stone & Landscape Supply, built by `Database\Seeders\DemoYardSeeder`
with data for every module, dated relative to today and rebuilt nightly
(`yardrunner:demo:reset`). Sign in at `/demo`; the contractor portal is at
`/portal/escarpment-stone`. Two yards, a truck and rental fleet, contractor
and builder price lists, job sites and contract prices, purchase orders and
colour runs, today's pick queue and dispatch board, invoices across every
aging bucket, returns, pallet deposits, a portal reorder waiting to be
confirmed and an audit trail.

| Persona        | Name           | Roles                 | Use it to test                                                                                                                       |
| -------------- | -------------- | --------------------- | ------------------------------------------------------------------------------------------------------------------------------------ |
| Owner          | Gord Hessels   | Owner                 | Everything; pricing, credit limits and overrides, timesheets, the P&L                                                                |
| Office manager | Lisa Marchetti | Admin                 | Accounts, price lists, payments, settings, credit overrides; no Owner-only rights                                                    |
| Counter sales  | Ryan Tran      | Sales                 | Counter and orders without price or credit control; refund limits                                                                    |
| Yard lead      | Owen Bakker    | Yard crew, Purchasing | Picking and loading, receiving and counts, rental check-in; PIN 2222 at the counter tablet. Sees costs and prices through Purchasing |
| Driver         | Sam Rinaldi    | Driver                | The driver app on a phone-sized screen: his run only, proof of delivery, location                                                    |
| Contractor     | (portal demo)  | Portal contact        | Reorder, calculator, statement, pallet pickup requests                                                                               |

## Facts that affect testing

- The demo yard always uses flat-rate tax and fake card payments, never
  emails its customers, can't connect QuickBooks and sends no push alerts.
- Demo accounts can't change account or business settings.
- Roles with no persona (Rental desk, Dispatcher, Accounts, Service tech,
  Time & attendance only) are covered by feature tests; add a persona when a
  feature needs one walked through.
- There's one demo tenant. Cross-tenant checks use tenants made in the
  tests, not a second demo yard.

## UAT row IDs

Every UAT row has a feature ID, `<CODE>-<n>`: the area's code, then the next free number for that code. Each feature's rows live in its own doc. `scripts/docs_check.py` checks that IDs are unique, are never bare numbers, and use a code from this table.

To add rows, find the highest number for the code with `grep -ohE "\bCODE-[0-9]+" docs/claude/*.md | sort -t- -k2 -n | tail -1`, then number on from it. A new area gets a new code: add it to this table in the same PR.

| Code | Area                           | Docs                     |
| ---- | ------------------------------ | ------------------------ |
| EM   | Emails and sending domains     | `email-branding.md`      |
| PAY  | Card payments and payouts      | this doc                 |
| TAX  | Sales tax                      | this doc                 |
| QB   | QuickBooks Online              | `quickbooks-sync-map.md` |
| TA   | Time and attendance            | `time-and-attendance.md` |
| AL   | Staff alerts                   | this doc                 |
| SMS  | Text messages                  | this doc                 |
| MK   | Marketing pages and edge cache | `marketing-pages.md`     |

## UAT checklist (foundation)

Rows only for what needs a person: real emails, PDFs, phones, wording, judgement, and accountant or lawyer review. Results are Jon's to fill in.

| #             | Feature  | What to check                                                                                              | Who        | Result |
| ------------- | -------- | ---------------------------------------------------------------------------------------------------------- | ---------- | ------ |
| PAY-1 (was 3) | Payments | A live card payment on a connected account: application fee taken, payout arrives                          | Jon        |        |
| TAX-1 (was 4) | Tax      | Stripe Tax amounts on an Ontario delivery, a Quebec delivery and a pickup match what an accountant expects | Accountant |        |
| AL-1 (was 7)  | Alerts   | Push alerts arrive on an installed iPhone and an Android phone                                             | Jon        |        |
| SMS-1 (was 8) | Texts    | ETA and rental texts arrive from the production number                                                     | Jon        |        |
