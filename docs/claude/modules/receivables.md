# Receivables

| Feature                                       | Screens                       | Code                                      |
| --------------------------------------------- | ----------------------------- | ----------------------------------------- |
| Invoices issued at confirmation, due by terms | Receivables, invoice print    | `IssueInvoice`                            |
| Aging and statements (print, PDF, email)      | Receivables                   | `Aging`, `Statement`                      |
| Returns with restocking fees, credit notes    | Returns                       | `ProcessReturn`, `UseCredit`              |
| Pallet deposits out and back                  | Order page, organization page | `PalletLedger`                            |
| Pallet usage fees                             | Order page, invoice, receipt  | `RecalculateOrder`, `ProcessReturn`       |
| Card payments and payment links               | Order page                    | `app/Payments` (Stripe)                   |
| Refunds, and refund and cancel                | Order page, credit note       | `app/Payments/Refunds`, `RefundAndCancel` |
| Sales tax                                     | Order confirm                 | `app/Tax` (Stripe Tax or flat rate)       |

**Pallet deposits and usage fees.** Two separate per-pallet charges, both
under Settings → Delivery, Returns tab (`ReturnsSettings`): the tenant turns
each on and sets its amount, each yard turns each on or off
(`locations.pallet_deposits`, `locations.pallet_usage_fees`), and each
product opts in (`products.pallet_deposit`, `products.pallet_usage_fee`).
The deposit is refundable: it sits outside the subtotal, isn't taxed, posts
to a liability and is credited when the pallet comes back. The usage fee
isn't: it's in the subtotal (`orders.pallet_fee_cents`), taxed with the
product's tax code, posts to QuickBooks as a "Pallet usage fee" item on the
sales account, and is never credited for an empty pallet. It's only
credited, with its tax, when unused product comes back on full pallets
(`ProcessReturn`, `credit_notes.pallet_fee_cents`). A line keeps the rate
it was charged once its prices are fixed.

**Refunds.** A payment goes back the way it came (`RefundPayments`):
card payments to the card through Stripe on the yard's connected account
(`StripeGateway::requestOptions()`, so refunds work while charges are
paused). The customer gets the full amount back; Stripe keeps its fee,
which stays the yard's cost; YardRunner's application fee goes back to
the yard in proportion (`refund_application_fee`, also on rental
refunds), recorded as `fee_returned_cents` and netted off the profit
report's card fees,
account credit back onto its credit note, and cash, cheque, e-Transfer
or a terminal payment recorded as handed back the same way. Each is a
`refunds` row; refunds of the order's own payments come off
`orders.amount_paid_cents`, and `payments.refunded_cents` tracks what's
left on each card. A credit note can also go back to a card the account
paid with in the last year: the credit is spent and the card's order is
left as it is. The Stripe call runs inside the transaction that records
it, with the payment locked, so a refusal records nothing. Cancelling
needs the order's payments refunded first; **Refund and cancel** does
both, cards first, after one refund-limit check on the money going out
(credit put back on a credit note isn't counted). The `charge.refunded`
webhook settles refunds made here and records anything refunded in
Stripe itself (by YardRunner staff; yards' embedded payments component
has refunds off) as a refund off the order; `charge.refund.updated` undoes
a card refund that fails later. Order refunds post to QuickBooks as a
journal entry, like rental refunds.

**Tax reversals.** A sale recorded with Stripe Tax is reversed when it's
undone (`ReverseSalesTax`, queued as `ReverseTax`): in full when the order
is cancelled (`orders.tax_reversal_ref`), and line by line when a return
is credited (`credit_notes.tax_reversal_ref`), each line less its share of
the restocking fee and its tax, plus any pallet usage fee credited with it
(`credit_note_lines.pallet_fee_cents`). The references make each reversal
happen once. Flat-rate tax records no sales, so it has nothing to reverse.
Pallet and adjustment credits carry no tax from a sale and aren't
reversed.
