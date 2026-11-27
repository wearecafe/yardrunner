# YardRunner Owner P&L Report

Oct 26, 2026 · @Jon Moore

> Moved here from the claude.ai doc "Yardline Owner P&L Report" on Nov 27, 2026. Access now follows the job-based roles in [commerce-data-model.md](commerce-data-model.md#roles-and-permissions) (See gross-profit P&L permission); the Staff and Order picker rows below are the old roles. What's built is in [features.md](features.md#profit-and-loss).

## Overview

The owner P&L shows how much gross profit the yard made in a period, from goods sales and rentals, down to gross margin. It stops at gross profit: operating expenses (wages, rent, fuel, insurance) live in QuickBooks Online, and net profit stays there.

Only owners see it by default. It answers three questions an owner asks every month:

- How much did we sell, and how much of it did we keep after the cost of the product?
- Which categories, yards, customers and rental units make the money, and which don't?
- How much are discounts, returns and shrinkage costing us?

Every figure is built from records YardRunner already holds (order lines, invoices, credit notes, inventory movements, rental invoices, maintenance logs and Stripe payments), so every number can be opened to the lines behind it.

## Report structure

The report reads top to bottom: revenue, less direct costs, equals gross profit. Goods and rentals each get a subtotal so the owner can see both businesses side by side.

| Line                                    | Group                 | Comes from                                                             | Notes                                   |
| --------------------------------------- | --------------------- | ---------------------------------------------------------------------- | --------------------------------------- |
| Goods sales                             | Revenue: goods        | Invoiced order lines, net of price list, contract and manual discounts | Broken down by product category         |
| Delivery fees                           | Revenue: goods        | Delivery fee lines on goods invoices                                   |                                         |
| Pallet usage fees                       | Revenue: goods        | Usage fee lines                                                        | Income. Pallet deposits are not revenue |
| Restocking fees                         | Revenue: goods        | Restocking fee lines on credit notes                                   |                                         |
| Returns and credits                     | Revenue: goods        | Credit notes for returned product                                      | Shown as a negative line                |
| Rental charges                          | Revenue: rentals      | Rental invoices: time-based rates and minimum charges                  |                                         |
| Damage waiver                           | Revenue: rentals      | Damage waiver lines                                                    |                                         |
| Rental delivery and pickup              | Revenue: rentals      | Delivery and pickup fee lines on rental invoices                       |                                         |
| Damage, late, fuel and cleaning charges | Revenue: rentals      | Extra charges added at return                                          |                                         |
| **Net revenue**                         | Total                 | Sum of the above                                                       |                                         |
| Cost of goods sold                      | Direct costs: goods   | Unit cost stored on each invoiced line × base quantity                 |                                         |
| Returned to stock                       | Direct costs: goods   | Cost of returned product put back into inventory                       | Negative, reverses the original cost    |
| Inventory adjustments                   | Direct costs: goods   | Shrinkage, damage write-offs and cycle count variances                 | Can be positive or negative             |
| Rental maintenance and repairs          | Direct costs: rentals | Repair log and service costs on rental units                           |                                         |
| Card processing fees                    | Direct costs: both    | Stripe fees on payments, split by goods or rental invoice              |                                         |
| **Gross profit**                        | Total                 | Net revenue less direct costs                                          | Shown in dollars and as gross margin %  |

Two memo lines sit under the report. They aren't part of the arithmetic but explain it: discounts given (list price minus selling price, split into price list and contract discounts vs manual discounts), and pallet deposits held at period end (a liability, not income).

## Calculation rules

Revenue is counted on an accrual basis by invoice date, the same basis that syncs to QuickBooks Online, so revenue totals reconcile with the QBO income accounts.

**Revenue**

- Goods revenue counts when the invoice is issued. Open quotes and unbilled orders are left out.
- Rental revenue counts by rental invoice date. Long rentals on cycle billing land in the period of each cycle invoice.
- Credit notes count in the period they're issued, not the period of the original sale.
- Amounts exclude sales tax (HST, GST, QST). Tax is a liability, not revenue.
- Pallet deposits and rental security deposits are never revenue. A forfeited deposit (pallet not returned, damage kept from a deposit) moves to revenue on the day it's forfeited.

**Cost of goods sold**

- Each product keeps a moving weighted average cost per base unit, per yard. Receiving against a purchase order updates it.
- When a line is invoiced, YardRunner stores the unit cost at that moment on the line. Later cost changes never rewrite past margins.
- Returned product goes back at the cost stored on the original line, so a return reverses exactly what was charged.
- Inventory adjustments are valued at the average cost on the day of the adjustment.
- A line with no cost (product received without a PO or cost never entered) is still counted as revenue, but flagged. The report shows how many lines are missing a cost and their revenue, with a link to fix them.

**Left out on purpose**

- Operating expenses: wages, truck fuel and repairs, rent, insurance, utilities. These belong to QuickBooks Online.
- Purchases of rental equipment. These are assets, not costs of the period.
- Delivery costs. Driver time and fuel are operating expenses, so delivery fees show as revenue with no matching cost. The report notes this beside the line.

## Filters, views and drill-down

The report opens on the current month for all yards, compared with the previous month.

| Control       | Options                                                                                        |
| ------------- | ---------------------------------------------------------------------------------------------- |
| Period        | This month, last month, this quarter, last quarter, year to date, last 12 months, custom range |
| Compare to    | Previous period, same period last year, none                                                   |
| Yard          | All yards or one yard                                                                          |
| Business      | Goods and rentals, goods only, rentals only                                                    |
| Sales channel | All, counter, staff-entered, contractor portal (goods only)                                    |

**Views**

1. **Summary:** the P&L statement, with headline cards for net revenue, gross profit and gross margin, and the change against the comparison period.
2. **Trend:** net revenue and gross margin by month for the last 12 months.
3. **By category:** sales, cost, gross profit and margin per product category, sortable.
4. **By product:** the same columns per product, with a filter for products selling below a target margin.
5. **By customer:** sales, gross profit and margin per org, with each org's price tier. Shows which contractor accounts are profitable after their discounts.
6. **By rental unit:** revenue, maintenance cost and gross profit per unit, plus days rented in the period.

**Drill-down:** every amount opens a list of the invoice lines, credit notes, adjustments or maintenance entries behind it, each linking to its record.

**Export:** CSV of any view and a PDF of the summary. A monthly emailed PDF to owners is a later addition.

## Permissions and audit

Owners see the report; everyone else is blocked unless an owner grants it to admins.

| Role         | Access         | Can change it?                                                                     |
| ------------ | -------------- | ---------------------------------------------------------------------------------- |
| Owner        | Always         | No, owners always have it                                                          |
| Admin        | Off by default | Yes, a tenant setting "Admins can view profit reports" (owners only can change it) |
| Staff        | Never          | No                                                                                 |
| Order picker | Never          | No                                                                                 |

- The report and its menu item are hidden from roles without access, and the API refuses the request, not just the page.
- Cost and margin figures elsewhere in the app (the below-cost warning on order lines) follow their own rules and are not changed by this setting.
- Every export is logged in the audit log with the user, time, view and filters.

## Data model additions

The report needs cost recorded at the moment things happen; it can't be worked out reliably after the fact. These fields should go in before goods sales go live, even if the report ships later.

| Entity                          | New fields                                                                      | Why                                         |
| ------------------------------- | ------------------------------------------------------------------------------- | ------------------------------------------- |
| Product stock (per yard)        | Average cost per base unit, last cost, last cost date                           | Basis for cost of goods sold                |
| Purchase order receipt line     | Unit cost received, freight and landed cost share                               | Updates the average cost                    |
| Invoice line (goods)            | Unit cost snapshot, cost source (average, manual, missing), list price snapshot | Fixed margin per line; discounts given memo |
| Credit note line                | Link to original invoice line, cost reversed, returned to stock (yes/no)        | Returns reverse the right cost              |
| Inventory movement              | Cost per base unit, reason code (shrinkage, damage, count variance, transfer)   | Values inventory adjustments                |
| Maintenance entry (rental unit) | Date, parts cost, labour cost, vendor, notes                                    | Rental direct costs                         |
| Payment                         | Stripe fee amount, from the Stripe balance transaction                          | Card processing fees                        |
| Deposit                         | Status (held, refunded, forfeited), forfeited date                              | Moves forfeited deposits into revenue       |

Report totals are calculated from these records on request. If the yard's volume makes that slow, a nightly daily-summary table (per yard, category and day) can feed the summary and trend views, with drill-down still reading the live lines.

## Decisions and open questions

| Question                 | Decision                                                                       |
| ------------------------ | ------------------------------------------------------------------------------ |
| How far does the P&L go? | Gross profit only. Operating expenses and net profit stay in QuickBooks Online |
| Who sees it?             | Owners. Admins only if an owner turns it on                                    |
| Accounting basis         | Accrual, by invoice date, matching what syncs to QuickBooks Online             |
| Inventory costing        | Moving weighted average per product per yard, snapshotted on each invoice line |

- [ ] Should the report offer a cash-basis toggle (revenue when paid) for owners who think in cash?
- [ ] Should freight on supplier deliveries be added to product cost (landed cost), or left as an operating expense in QuickBooks?
- [ ] Should damage waiver claims paid out be a rental direct cost, once the damage waiver decision (built in or partner) is made?
- [ ] Do multi-yard owners need inter-yard transfers shown, or are they invisible at the all-yards level?
- [ ] Should owners set a target margin per category, so the report highlights categories and products below it?
