# YardRunner: phase and feature index

**Start here.** Each phase or feature keeps its scope, access changes, security notes and UAT rows in its own doc, listed below. Its decisions are files in `decisions/`.

**Rules for sessions:**

- A new phase or feature gets its own doc (a named doc like `billing.md`) with sections for scope, access (→ `permissions.md`), security and UAT rows (feature IDs: `demo-and-uat.md` → UAT row IDs), and links to its decision files.
- Add one row below when the doc is created, and update its status when it's built and merged. Edit only your own row (or add one) in a small change, rebase before merging, and keep every row.
- Where a newer doc and an older one disagree, the newer one wins, and it says what it reverses.

| Phase or feature                                  | Doc                                                                                                | Status                                              | Changes access?                       | UAT rows   |
| ------------------------------------------------- | -------------------------------------------------------------------------------------------------- | --------------------------------------------------- | ------------------------------------- | ---------- |
| Commerce, pricing, pallets, roles and permissions | [commerce-data-model.md](commerce-data-model.md)                                                   | Built (v0, Oct 2026); payment on delivery not built | Yes                                   |            |
| Setup for new businesses                          | [tenant-setup.md](tenant-setup.md)                                                                 | Built                                               | No                                    |            |
| Emails and branding                               | [email-branding.md](email-branding.md), full spec [email-branding-spec.md](email-branding-spec.md) | Built; gaps in `gap-list.md`                        | Yes (Owner and Admin manage branding) | EM-1, EM-2 |
| QuickBooks Online sync                            | [quickbooks-sync-map.md](quickbooks-sync-map.md), [quickbooks.md](quickbooks.md)                   | Built; supplier sync (decision 8) open              | Yes (Accounts sees sync errors)       | QB-1       |
| Owner P&L report                                  | [profit-report.md](profit-report.md)                                                               | Built                                               | Yes (See gross-profit P&L)            |            |
| Time and attendance                               | [time-and-attendance.md](time-and-attendance.md)                                                   | Phase 1 built                                       | Yes (Approve timesheets)              | TA-1       |
| Marketing pages: cookieless, edge-cached          | [marketing-pages.md](marketing-pages.md)                                                           | Built (PR #59)                                      | No                                    | MK-1       |
| Docs and workflow                                 | [docs-and-workflow.md](docs-and-workflow.md)                                                       | Built (PR #58, delivery-framework PR)               | No                                    |            |
| Laravel stack rules (framework gaps)              | [stack-rules.md](stack-rules.md)                                                                   | In progress: plugin settings built                  | No                                    | DW-1       |
| Backlog                                           | [gap-list.md](gap-list.md)                                                                         | Not built                                           |                                       |            |
| Market research                                   | [competitive-research.md](competitive-research.md)                                                 | Reference                                           |                                       |            |

## Reference docs

- `delivery-checklist.md`, `permissions.md`, `security.md`, `demo-and-uat.md` (foundation UAT rows PAY-1, TAX-1, AL-1, SMS-1), `design-system.md`, `modules/`, `decisions/`.
- Outside services: one file each, listed in [README.md](README.md#outside-services).

## Open questions

Questions nobody has answered yet (`decisions/2026-11-27-open-questions-in-index.md`). Ask them with `/decision-batch`; each answer becomes a decision file and leaves this table. Anything legal or compliance-related is
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
| Research   | Validate the rental module with 5 to 10 yard owners                                                                                | [competitive-research.md](competitive-research.md#open-questions-before-mockups) |
