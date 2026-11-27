# Profit and loss

Owners (and admins, if the owner ticks it under Settings → Users) see
**Reports → Profit and loss**: revenue from goods and rentals less their
direct costs, down to gross profit. Operating expenses and net profit stay
in QuickBooks. Gate `view-profit`, settings in `tenants.settings['reports']`
(`ReportSettings`).

| Feature                                                              | Screens                   | Code                                        |
| -------------------------------------------------------------------- | ------------------------- | ------------------------------------------- |
| Statement, comparison, trend, by category/product/customer/unit      | Reports → Profit and loss | `App\Reports\ProfitReport`, `ProfitFilters` |
| Drill-down to the records behind any amount; CSV and PDF             | Profit and loss           | `ProfitReportController`                    |
| Average cost per product per yard                                    | (Purchasing, transfers)   | `InventoryLedger`                           |
| Cost and list price fixed on each goods line at confirmation         | Order confirm             | `RecordLineCosts`                           |
| Card processor fees on payments (hourly, `yardrunner:payments:fees`) | (scheduled)               | `app/Payments/Fees`                         |

Rules:

- **Basis.** Accrual, on the dates that go to QuickBooks: goods by invoice
  date (`invoices.issued_on`), credit notes by theirs, rentals by
  `checked_out_at`. Amounts are before tax. Deposits are never revenue.
- **Cost of goods.** `stock_levels.average_cost_cents` is a moving
  weighted average in cents per base unit (four decimals). Receipts,
  transfers in and restocked returns blend in at their own cost
  (`InventoryLedger::COSTED_IN`); everything else moves at the average, and
  every `stock_movements` row records the cost it moved at. A receipt
  without a cost comes in at `products.cost_cents`.
- **Line costs.** Confirming a goods order writes `order_lines.unit_cost_cents`
  (the yard's average, else the product's cost), `cost_source` (`average`,
  `product`, `missing` or `none`) and `list_price_cents`. Lines can't change
  after that. Stocked products with no cost are `missing`: counted at no
  cost and flagged on the report.
- **Returns** reverse the line's cost when the goods go back on the shelf
  (`credit_note_lines.unit_cost_cents`); damaged returns keep their cost.
- **Inventory adjustments** are adjustment, count and damage movements,
  valued at the cost they moved at.
- **Card fees** come from the Stripe balance transaction, looked up hourly
  for 30 days; with the fake payments driver (local, the demo yard) they're
  estimated at 2.9% + 30¢.
- A **sales channel** filter shows goods only and leaves out inventory
  adjustments, which belong to no channel.
