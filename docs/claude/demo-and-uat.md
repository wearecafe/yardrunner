# Demo yard and UAT

## Demo yard

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

Facts that affect testing:

- The demo yard always uses flat-rate tax and fake card payments, never
  emails its customers, can't connect QuickBooks and sends no push alerts.
- Demo accounts can't change account or business settings.
- Roles with no persona (Rental desk, Dispatcher, Accounts, Service tech,
  Time & attendance only) are covered by feature tests; add a persona when a
  feature needs one walked through.

## UAT checklist

Things only a person can confirm: real emails and phones, money in live
services, wording and judgement. Each row is added when the work that needs
it merges. Results are Jon's to fill in.

| #   | Area       | Check                                                                                                                                                                                                                         | Result | Notes |
| --- | ---------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ------ | ----- |
| 1   | Emails     | Connect one real test domain and confirm it reaches Verified through the CNAMEs (DKIM and bounce MX/SPF)                                                                                                                      |        |       |
| 2   | Emails     | Customer and user emails render correctly in Gmail, Outlook (Windows and web) and Apple Mail, light and dark                                                                                                                  |        |       |
| 3   | Payments   | A live card payment on a connected account: application fee taken, payout arrives                                                                                                                                             |        |       |
| 4   | Tax        | Stripe Tax amounts on an Ontario delivery, a Quebec delivery and a pickup match what an accountant expects                                                                                                                    |        |       |
| 5   | QuickBooks | Invoices, payments, credit memos and refunds land on the right accounts and tax codes in a real company                                                                                                                       |        |       |
| 6   | Time       | Ontario and Quebec overtime and break defaults confirmed against current law before launch                                                                                                                                    |        |       |
| 7   | Alerts     | Push alerts arrive on an installed iPhone and an Android phone                                                                                                                                                                |        |       |
| 8   | Texts      | ETA and rental texts arrive from the production number                                                                                                                                                                        |        |       |
| 9   | Edge cache | After turning on "purge edge cache on deploy" and deploying, the home page and a help article answer with no `Set-Cookie` and `cf-cache-status: HIT` on a second load, and signed-in people still reach the app from "Log in" |        |       |
