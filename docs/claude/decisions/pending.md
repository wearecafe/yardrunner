# Pending confirmations (backlog before the decision log)

Each row is a set of older choices, made while building or noted for later, that Jon hasn't confirmed or overruled. They stand as built until then. New unconfirmed choices don't go here: they're decision files with `Status: proposed`.

**How this list shrinks:** Jon confirms in batches (`/decision-batch`), usually a whole doc's set at once. Each batch becomes one decision file, and its rows here are ticked with that file's name. Never delete a row; tick it.

Most of YardRunner v0 was built in Oct 2026 sessions where Claude settled the detail itself between Jon's decisions. Those details are described in the module notes; the rows below are each module's set, waiting for Jon. Everything already in a decision file is left out.

| Doc                              | What's waiting                                                                                                                                                       | Settled by |
| -------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ---------- |
| `modules/orders.md`              | Counter and order behaviour: quote expiry and revisions, order numbering, the counter screen's flow, rounding prompts, price-floor warnings                          |            |
| `modules/fulfillment.md`         | Pick queue claiming, scale weights and photos, dispatch board, truck capacities and per-day driver picks, delivery fee rules, ETA texts with a fixed two-hour window |            |
| `modules/rentals.md`             | Rental booking page, agreements and signing, deposits and card holds, inspections, maintenance lock-outs, rental rates                                               |            |
| `modules/inventory.md`           | Purchase orders, receiving, colour-run lots, transfers and cycle counts                                                                                              |            |
| `modules/receivables.md`         | Statements, aging buckets, account holds, returns and credit notes, restocking fees                                                                                  |            |
| `modules/profit-and-loss.md`     | P&L views, filters and drill-down as built                                                                                                                           |            |
| `modules/portal.md`              | Contractor portal sign-in by emailed link, reorder, material calculator, statements, pallet pickup requests                                                          |            |
| `modules/time-and-attendance.md` | Clock methods, PIN length, geofence flags, exceptions and the payroll export formats as built                                                                        |            |
| `modules/setup.md`               | Setup steps and checklist details beyond `2026-10-27-tenant-setup-minimum.md`                                                                                        |            |
| `modules/audit-log.md`           | What the audit log records and who can see it                                                                                                                        |            |
| `modules/users-and-roles.md`     | Role defaults for each permission, PIN switching, `RouteAccess` reach per role                                                                                       |            |
| `modules/app-and-alerts.md`      | Which staff alerts exist and who gets them; the installable app's start screens                                                                                      |            |
| `modules/emails.md`              | Email catalogue and wording as built                                                                                                                                 |            |
| `modules/back-office.md`         | Back-office screens, Needs attention rules, staff two-factor                                                                                                         |            |
| `modules/billing.md`             | Plan prices other than Time and attendance, trial length, what locks when billing lapses                                                                             |            |
| `modules/demo-yard.md`           | Demo yard contents and nightly reset                                                                                                                                 |            |
| `modules/help-center.md`         | Help center search and article layout                                                                                                                                |            |
