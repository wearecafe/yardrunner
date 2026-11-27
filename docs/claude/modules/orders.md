# Counter and orders

| Feature                                                                      | Screens                          | Code                                         |
| ---------------------------------------------------------------------------- | -------------------------------- | -------------------------------------------- |
| Orders: draft → confirmed → fulfilled, settled on account or prepaid         | Orders, order page               | `app/Orders`                                 |
| Credit checks, holds and owner overrides                                     | Order confirm, organization page | `app/Credit`                                 |
| Selling units (bag, pallet, tonne…) converting to a base unit                | Product editor                   | `ProductUnit`, `ProductUnitController`       |
| Units switched off for sale; rounding to the nearest mix of units for sale   | Product editor, order lines      | `QuantityFit`, `UnitMix`                     |
| Price resolution: contract → price list (per unit, dated, breaks) → retail   | Order lines                      | `app/Pricing/PriceResolver`                  |
| Category discounts ($ or %) on price lists; products exempt from price lists | Price lists, product editor      | `PriceListCategoryRule`, `PricingSettings`   |
| Quotes with expiry, accepted from a public link                              | Order page, `/q/{token}`         | `SendQuote`, `QuoteController`               |
| Job sites (with PO required), contacts with roles and charge rights          | Organization page                | `OrganizationLocation`, `CustomerMembership` |
| Counter screen for walk-ins                                                  | Counter                          | `CounterController`                          |

## Category discounts and exempt products

A price list can take $ or % off a whole category. A $ amount is off each
base unit, so $0.50 off a bag is $28 off a 56-bag pallet. Prices never go
below zero.

When a product has its own price on the list and its category has a
discount, one of them wins; they never add up. Price lists → Pricing rules
sets the business default, and each price list can follow it or choose.
A list can also stack its category discount on a volume-break price (an
entry for a quantity above 1). Contract prices still beat both.

A product marked "Exempt from price lists" always sells at its own price:
no contract, price list or category discount touches it. Pricing rules
also says which roles (owner, admin) may set an exempt product's price by
hand. They give a reason, logged on the order with the price it replaced.

## Units for sale and rounding

Each selling unit has a **For sale** switch. A unit switched off still
converts and can carry prices, but nothing sells in it on its own: order
entry, the counter and the portal refuse it ("Square foot isn’t sold on its
own"). Use it for a base unit that only
counts stock, like square feet of interlock sold by the layer or the
pallet. At least one unit has to stay for sale. **Sold in steps of** and
**Minimum** still apply to the units that are.

A product's **When a quantity doesn't fit** setting (`quantity_rounding`)
decides what happens to a quantity that isn't in whole steps of a unit for
sale, or is entered in one that isn't for sale:

- **Don’t round** (default): it's refused.
- **Round up** / **Round down**: `UnitMix` finds the nearest total at or
  above (or below) what was asked, made of whole steps of the units for
  sale. Ties go to the most in the bigger units, so full pallets sell at
  the pallet price. 300 sq ft of interlock at 116.82 sq ft a pallet and
  10.62 sq ft a layer is 2 pallets + 7 layers (307.98 sq ft) rounding up,
  2 pallets + 6 layers (297.36 sq ft) rounding down.

Staff accept a rounding before it goes on: the order page and the counter
ask `products.fit` (`QuantityFit`), show what was asked, the mix and its
total, and add one line per unit when accepted (`round` on
`orders.lines.store`). When a product rounds, its base unit stays in the
unit picker, marked "(rounds)", so staff can enter an area or weight.
Rounding down below the smallest sale (5 sq ft when a layer is 10.62) is
refused. Pallet deposits and usage fees count each line on its own, so
loose layers ship on (and pay for) one more pallet.

Portal reorders round the same way when a unit has been switched off since,
and the patio calculator prices pavers that round as the same mix
(`portal.calculator.pavers`).

In the demo yard, the 80 mm natural pavers (`PAV-80NT`) sell only by the
layer (9 sq ft) or the pallet (90 sq ft) and round up.
