# YardRunner ↔ QuickBooks Online sync map

Oct 26, 2026 · @Jon Moore

> Moved here from the claude.ai doc "Yardline ↔ QuickBooks Online sync map" on Nov 27, 2026. This file is now the only copy; the developer summary is [quickbooks.md](quickbooks.md).

## Summary

YardRunner pushes money documents to QuickBooks Online one way: invoices, payments, credit memos, refunds and rental invoices, plus the customers and items they need. QBO stays the book of record for the general ledger, bank, payroll and supplier bills; YardRunner stays in charge of stock, pricing, orders and rentals.

- **Direction:** YardRunner → QBO. Nothing in YardRunner is changed by QBO, except one optional pull (payments entered in QBO, decision 3).
- **When:** each record is queued the moment it becomes final (an order is confirmed, a payment succeeds, a rental is returned) and sent within a minute. A nightly check re-sends anything that failed or changed.
- **From when:** only records dated on or after a sync start date the yard picks when connecting. No backfill of history unless asked.
- **Scope per yard business:** one YardRunner business connects to one QBO company. All its yards post to that company, tagged with a QBO Location (decision 7).
- **Eight decisions** at the end need a yes, no or change before I build the sync. The connection itself (sign-in to QBO, account mapping screen, sync log) doesn't depend on them and is being built now.

## Record by record

Every YardRunner record that reaches QBO, the QBO record it becomes, and exactly what goes across. Records that don't sync are listed in the next-but-one section.

| YardRunner record                                                                                                | QBO record                                                                     | Sent when                                                                         | Fields sent                                                                                                                         |
| ---------------------------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------ | --------------------------------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------- |
| Organization (account customer)                                                                                  | Customer                                                                       | First synced document for it; again when a sent field changes                     | Name, account number (in Display name), email, phone, billing address, payment terms, tax-exempt flag and exemption number          |
| Customer with no organization (homeowner, walk-in with a name)                                                   | Shared “Cash sales” customer, or its own Customer (decision 1)                 | First synced document                                                             | If its own: name, email, phone, address                                                                                             |
| Sale product                                                                                                     | Item, type Non-inventory (decision 4)                                          | First time it appears on a synced document; again when name, SKU or price changes | Name, SKU, description, income account, taxable flag, base-unit price                                                               |
| Rental fleet model (e.g. plate compactor)                                                                        | Item, type Service                                                             | First rental invoice for it                                                       | Name, SKU, rental income account                                                                                                    |
| Fixed charges: delivery fee, damage waiver, fuel, cleaning, restocking fee, extra rental charges, pallet deposit | Item, type Service, one each                                                   | Created when the yard connects                                                    | Name, account from the mapping screen                                                                                               |
| Invoice (one per confirmed goods order)                                                                          | Invoice                                                                        | Order confirmed                                                                   | Number, issue and due dates, terms, customer, ship-to address, PO number, location, lines, tax per line, memo with the order number |
| Prepaid walk-in sale                                                                                             | Invoice + Payment, or Sales Receipt (decision 5)                               | Order confirmed and paid                                                          | Same as an invoice, plus the payment method                                                                                         |
| Order payment: cash, cheque, e-Transfer, card terminal, payment link                                             | Receive Payment, applied to the invoice                                        | Payment succeeds                                                                  | Amount, date, method, reference (cheque number etc.), deposit-to account by method                                                  |
| Payment by credit note                                                                                           | Credit memo applied to the invoice                                             | Credit used                                                                       | Which memo, which invoice, amount                                                                                                   |
| Cancelled order with a synced invoice                                                                            | Invoice voided                                                                 | Order cancelled                                                                   | Void only; nothing is deleted                                                                                                       |
| Credit note: return                                                                                              | Credit Memo                                                                    | Return processed                                                                  | Returned lines at the price charged, restocking fee as a negative line, tax                                                         |
| Credit note: pallets back                                                                                        | Credit Memo                                                                    | Pallets returned                                                                  | Pallet deposit line, quantity and amount                                                                                            |
| Credit note: adjustment                                                                                          | Credit Memo                                                                    | Issued                                                                            | Reason as the line description, amount, tax                                                                                         |
| Credit refunded to the customer                                                                                  | Journal entry (refund account → customer’s balance), linked to the credit memo | Refund recorded                                                                   | Amount, method, refund-from account                                                                                                 |
| Rental                                                                                                           | Invoice, separate from goods                                                   | Checked out; updated when returned (decision 6)                                   | Number, customer, dates out and back, rental line, waiver, delivery fee, extra charges added at return, tax                         |
| Rental payment (charge)                                                                                          | Receive Payment, applied to the rental invoice                                 | Payment succeeds                                                                  | Amount, date, method, reference                                                                                                     |
| Rental deposit captured (damage)                                                                                 | Receive Payment against the rental invoice; the damage is an invoice line      | Capture succeeds                                                                  | Amount, date                                                                                                                        |
| Rental refund                                                                                                    | Journal entry (refund account → customer’s balance)                            | Refund succeeds                                                                   | Amount, method                                                                                                                      |
| Yard                                                                                                             | Location (decision 7)                                                          | Created when the yard connects                                                    | Yard name                                                                                                                           |

## Line by line

Each line on a YardRunner document becomes one QBO line. Amounts and tax are sent exactly as YardRunner calculated them, so the QBO total always equals the YardRunner total to the cent.

| Document                | YardRunner line                      | QBO item                                                                                                               | Posts to (default account name)                | Tax                   |
| ----------------------- | ------------------------------------ | ---------------------------------------------------------------------------------------------------------------------- | ---------------------------------------------- | --------------------- |
| Goods invoice           | Product, in the unit sold            | The product’s item; quantity and price in the unit sold, base quantity in the description (“12 pallets = 1,380 sq ft”) | Sales, or a sales account per product category | As charged            |
| Goods invoice           | Delivery fee                         | Delivery                                                                                                               | Delivery income                                | As charged            |
| Goods invoice           | Pallet deposit                       | Pallet deposit                                                                                                         | Pallet deposits held (liability)               | As charged            |
| Credit memo, return     | Returned product                     | The product’s item                                                                                                     | Sales (reduces it)                             | As originally charged |
| Credit memo, return     | Restocking fee (negative line)       | Restocking fee                                                                                                         | Restocking fees income                         | As charged            |
| Credit memo, pallets    | Pallets returned                     | Pallet deposit                                                                                                         | Pallet deposits held                           | As originally charged |
| Credit memo, adjustment | Adjustment                           | Adjustment                                                                                                             | Sales discounts and allowances                 | As entered            |
| Rental invoice          | Rental time                          | The fleet model’s item; description has dates out and back and the unit serial                                         | Rental income                                  | As charged            |
| Rental invoice          | Damage waiver                        | Damage waiver                                                                                                          | Damage waiver income                           | As charged            |
| Rental invoice          | Delivery and pickup                  | Delivery                                                                                                               | Delivery income                                | As charged            |
| Rental invoice          | Fuel, cleaning                       | Fuel charge, Cleaning fee                                                                                              | Rental income                                  | As charged            |
| Rental invoice          | Damage or extra time added at return | Rental damage, or the fleet item                                                                                       | Rental income                                  | As charged            |

Payments land in a holding account by method, so the bank feed matches them later:

| Payment method                                   | Deposit to                                                                                  |
| ------------------------------------------------ | ------------------------------------------------------------------------------------------- |
| Cash, cheque, e-Transfer                         | Undeposited Funds (“Payments to deposit”); the bookkeeper groups them into the bank deposit |
| Card terminal, Stripe payment link, card on file | Stripe clearing account; each Stripe payout in the bank feed is a transfer out of it        |

Every account name above is a default. The mapping screen lets the yard pick any existing QBO account for each one.

## What stays in YardRunner

These never go to QBO. Each would either duplicate what another QBO record already carries or put operational detail into the books.

| YardRunner record                                                | Why it doesn’t sync                                                                               |
| ---------------------------------------------------------------- | ------------------------------------------------------------------------------------------------- |
| Quotes and draft orders                                          | Not a sale until confirmed; QBO Estimates would be a second copy to keep in step                  |
| Contacts (people at an organization)                             | QBO bills the organization; contacts, roles and charge rights live in YardRunner                  |
| Job sites                                                        | The site’s address goes on each invoice as the ship-to (decision 2 if you want QBO sub-customers) |
| Selling units, price lists, contract prices                      | Pricing is decided in YardRunner; QBO only sees the price charged on each line                    |
| Credit limits and credit holds                                   | Enforced at the YardRunner counter; QBO has no credit limit to keep in step                       |
| Stock levels, movements, lots, transfers, cycle counts           | YardRunner is the stock system; QBO items are non-inventory (decision 4)                          |
| Suppliers, purchase orders, receipts                             | Bills are entered in QBO from the supplier’s invoice (decision 8)                                 |
| Pallet movements                                                 | Already on the invoice (deposit charged) and credit memo (deposit returned)                       |
| Rental deposit holds and releases                                | A card authorization; no money moves until it’s captured                                          |
| Pick lists, shipments, trucks, dispatch stops, proof of delivery | Operations only                                                                                   |
| Stripe fees and payouts                                          | Come in through the bank feed and Stripe statement; posting them twice would double count         |
| Users, roles, audit log, settings                                | Not accounting data                                                                               |

## Set up in QBO before connecting

The mapping screen lists the yard’s existing QBO accounts and offers to create any that are missing.

| Account or code                            | Type                        | Used by                                                                                    |
| ------------------------------------------ | --------------------------- | ------------------------------------------------------------------------------------------ |
| Sales (or one per product category)        | Income                      | Product lines                                                                              |
| Delivery income                            | Income                      | Delivery fees on goods and rentals                                                         |
| Rental income                              | Income                      | Rental time, fuel, cleaning, damage                                                        |
| Damage waiver income                       | Income                      | Damage waiver                                                                              |
| Restocking fees                            | Income                      | Restocking fee on returns                                                                  |
| Sales discounts and allowances             | Income (contra)             | Adjustment credit memos                                                                    |
| Pallet deposits held                       | Other current liability     | Pallet deposits charged and returned                                                       |
| Stripe clearing                            | Bank or other current asset | Card and payment-link payments                                                             |
| Undeposited Funds                          | Built in                    | Cash, cheque, e-Transfer                                                                   |
| Tax codes: HST ON, GST/QST QC, GST, Exempt | Sales tax codes             | Picked per line from the province YardRunner taxed the sale in; exempt accounts use Exempt |

YardRunner sends the tax amount Stripe Tax calculated on each line and sets the document’s total tax to match, so QBO’s own tax engine doesn’t recalculate it. The tax still lands on the right line of the HST and QST returns through the tax code.

## Decisions to confirm

Mark each one; anything left blank I’ll build as recommended.

| #   | Question                                                                                      | Recommendation                                                   | Why                                                                                                                                              | Your call              |
| --- | --------------------------------------------------------------------------------------------- | ---------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------ | ---------------------- |
| 1   | Customers with no organization: one shared “Cash sales” customer, or a QBO customer each?     | Shared “Cash sales” customer; the person’s name goes in the memo | Keeps hundreds of one-time homeowners out of the QBO customer list                                                                               | Go with recommendation |
| 2   | Job sites: ship-to address only, or a QBO sub-customer per site?                              | Ship-to address only                                             | Sub-customers split the account’s balance and statements in QBO                                                                                  | Go with recommendation |
| 3   | Payments entered directly in QBO (a cheque mailed to the office): pull them into YardRunner?  | Yes, for invoices YardRunner created                             | Otherwise YardRunner shows the invoice unpaid and the credit limit stays used up                                                                 | Go with recommendation |
| 4   | Products as Non-inventory items, or QBO Inventory items?                                      | Non-inventory                                                    | QBO inventory (Plus or Advanced only) would track stock a second time and post its own cost of goods; a monthly inventory journal can come later | Go with recommendation |
| 5   | Prepaid counter sales: Invoice plus Payment, or a Sales Receipt?                              | Invoice plus Payment                                             | Matches YardRunner, where every confirmed order has an invoice; one path to build and reconcile                                                  | Go with recommendation |
| 6   | Rental invoice: created when the unit goes out and updated at return, or only once it’s back? | Created at check-out, updated at return                          | Rentals are prepaid, so the payment needs an invoice to apply to on day one                                                                      | Go with recommendation |
| 7   | Tag every document with a QBO Location for the yard it came from?                             | Yes, when the yard business has more than one yard               | Gives profit and loss by yard; needs Locations turned on in QBO (Plus or Advanced)                                                               | Go with recommendation |
| 8   | Suppliers and purchase orders: sync vendors and turn receipts into bills?                     | No, not in this first version                                    | The bookkeeper enters bills from the supplier’s own invoice, which has their final prices and freight; syncing receipts risks a double bill      | Discuss                |

**Status:** 1 to 7 are built as recommended. 8 is left out until we’ve discussed it, so suppliers, purchase orders and receipts stay in YardRunner and bills are entered in QBO. One change from the first draft: refunds post as a journal entry from the refund account to the customer’s balance, linked to the credit memo. That clears the credit in QBO; a Refund Receipt would have credited the customer a second time.

## Edits, voids and errors

- **One record, one twin.** Each synced YardRunner record stores its QBO ID, so a retry updates the same QBO record and never makes a duplicate.
- **Changes after sending.** A confirmed order’s lines can’t change in YardRunner, so its invoice is sent once. A rental invoice is updated once more at return. Customer and item details are updated when a sent field changes.
- **Edits made in QBO win until the next YardRunner change.** YardRunner only overwrites a QBO record when the YardRunner record itself changes; it never reverts a bookkeeper’s edit on its own.
- **Nothing is deleted in QBO.** A cancelled order voids its invoice. YardRunner has no way to void a payment yet; a payment entered in error is corrected in QBO by the bookkeeper.
- **Closed periods.** If a document falls in a period closed in QBO, it’s held and shown on the sync log for the bookkeeper instead of being forced through.
- **Failures.** Each send is retried with back-off for a day. After that it shows on Settings → QuickBooks with the error from QBO and a “Send again” button. The owner gets an email the first time something fails.
- **Nightly check.** Compares totals for the day’s invoices, payments and credit memos between YardRunner and QBO and lists any that differ.
- **Disconnecting** stops new sends; everything already in QBO stays.
