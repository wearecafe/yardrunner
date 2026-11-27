# Gap list

Features that stop short of working end to end, with what's there now and
what to build, prioritised **Now**, **Next** or **Later**. Moved here from
the "YardRunner Gap List" page on claude.ai on Nov 27, 2026 (audited against
main at f8b2a68, Oct 27). The "Not built yet" table at the end lists the same kind of thing per
module (it was in `features.md` before the module notes split); when an item
ships, remove it from both.

Left off on purpose: promo codes and a public storefront (open questions in
[decisions-index.md](decisions-index.md#open-questions)), and anything that
only runs on fake or log drivers locally but has a real driver in
production.

## Money and payments

- **Next · Card-present payments at the counter (Stripe Terminal).** "Card
  (terminal)" is a manual record. Register readers per yard and counter on
  the connected account and create payment intents from the counter and
  order pages; keep "external terminal" as a tender for yards on their own
  processor.
- **Next · Collect payment on delivery.** The permission exists; the flow
  doesn't. COD flag on stops set by dispatch, driver sees amount due only;
  truck reader (S710), external terminal reference, optional cash; payment
  before proof of delivery; "couldn't pay" back to dispatch; end-of-day
  driver report by tender. Spec in
  [commerce-data-model.md](commerce-data-model.md#payment-on-delivery).
- **Later · Forfeit unreturned pallet deposits.** Deposits sit in a
  liability with no way to forfeit them. Forfeit action per org (and an
  optional age rule), posting to income in QuickBooks.

## Roles and permissions

- **Next · Hide what the server already refuses.** Disable selling-price
  fields for Purchasing on the product editor; hide QuickBooks connect and
  mapping controls from Accounts; hide rental rates from Service techs on
  fleet screens.
- **Next · Count-only receiving for Yard crew.** A quantities-only receiving
  view for Yard crew using the same `ReceivePurchaseOrder` action.
- **Next · PIN switching in the driver app and fleet screens.** The switch
  is only on the pick queue.
- **Later · Deactivated users list.** A "Deactivated" filter on Settings →
  Users with Reactivate.

## Delivery, maps and texting

- **Now · Geocode addresses and price distance automatically.** No
  coordinates on addresses; distance pricing waits for staff to type the
  km. Pick a provider (Google Maps Platform, Mapbox or HERE), geocode with
  address autocomplete on job sites and delivery addresses, store lat/lng
  and fill `delivery_km` from the order's yard. Optionally drawn zones
  alongside postal-code zones. The provider gets its own file in
  `docs/claude/` when chosen.
- **Now · Directions in the driver app.** "Directions" deep links (Google
  Maps and Apple Maps) on each stop.
- **Now · Finish rental and pallet stops in the driver app.** These stops
  end with a bare "Done". Pallet pickup: count pallets, write to
  `PalletLedger`, credit deposits. Rental drop-off and pickup: photos, hour
  meter, signature feeding the inspections.
- **Next · Real tracking map and live ETA.** The portal uses an
  OpenStreetMap iframe with the truck only; ETA texts send a fixed two-hour
  window. Provider map in the portal and on the dispatch board; ETA from the
  truck's last position.
- **Next · Route order and drive time on the dispatch board.** Drive time
  between stops and "optimise order" per truck.
- **Next · "Couldn't deliver" outcome.** Failed-stop reasons with a photo,
  back to dispatch, stock back in if the load returns.
- **Next · Offline work in the yard and on the truck.** Queue picks, scale
  weights, photos and proof of delivery while offline, then sync.
- **Next · Texting that works per yard.** Sending number per tenant (or
  Messaging Service), inbound replies to the yard, delivery status, STOP
  opt-outs; text the org's main contact for pallet pickups. See
  [twilio.md](twilio.md).

## Rentals

- **Next · Extend or swap a rental that's out.** `SaveRental` refuses any
  change once out. Extend with an availability check and re-pricing, swap a
  unit, charge the difference to the card on file.
- **Next · Partial rental refunds.** Rentals refund only everything on
  cancel (`refundAll`). Refund a chosen amount against a rental payment,
  reusing `app/Payments/Refunds` and the approval flow.
- **Later · Cycle billing for long rentals.** Bill every 28 days to the
  card on file, one QuickBooks invoice per cycle.
- **Later · Remove the old order-based rental path.** `App\Orders\ReturnRental`
  and `orders.return` are still there with their TODO.

## Contractor portal and counter

- **Next · Order from the portal, not only reorder.** Catalogue with their
  prices, cart, job site, PO, pickup or delivery; open quotes accepted in
  the portal.
- **Next · Pay invoices from the portal.** One invoice or the balance, by
  card through the yard's connected account.
- **Later · Installable portal with push alerts.** Portal manifest; push for
  "delivery on the way" and "order ready".
- **Later · Buyer, approver and account admin in the portal.** Approval step
  for buyers without charge rights; account admins manage their contacts.
- **Later · Email counter receipts.** "Email receipt" at the counter using
  the branded mail.

## Emails

- **Next · Email log and bounces.** Log sends per order and rental with
  Resend delivery and bounce webhooks; flag a customer's address when it
  bounces.
- **Next · Line items in order and invoice emails.** Lines, pallet charges,
  tax and totals in the branded template.
- **Later · Easier DNS setup for sending domains.** Detect the DNS host and
  show steps for it; Domain Connect (needs registering with each host).
- **Later · Logo processing.** Trim margins, resize, flatten transparency.
- **Later · Per-yard branding.** Optional overrides per yard.
- **Later · Branding changes in the yard's audit log.**

## Yard, inventory and purchasing

- **Next · Resolve picker shortages and substitutions.** Short lines and
  free-text substitutes notify nobody. "Needs attention" list plus a staff
  alert; approve a re-priced substitute or backorder; text the customer.
- **Next · Supplier price lists and cost-change alerts.** Supplier edit page
  and CSV price-list import that updates costs; "costs changed" list with
  margin impact and bulk price update.
- **Later · Barcode scanning and scale integration.**

## Onboarding, reports and language

- **Now · Import a yard's existing data.** CSV import with preview and error
  report: products and units, price lists, orgs and contacts, job sites,
  opening stock per yard, opening AR; wired into setup's products step.
- **Next · Reports beyond profit and loss.** Sales by product, customer and
  channel; delivery performance; pick times; inventory value; rental use
  and maintenance cost.
- **Later · Alert preferences.** Alert kinds and quiet hours per person.
- **Later · French for Quebec yards.** Translation keys; start with the
  portal, booking page, texts, emails, invoices and receipts.

## Not built yet

Features the scope docs in `docs/claude` call for that aren't built, or are
only partly built. Add to this list when something is left out, and
remove an item when it ships.

| Feature                                      | Area            | What's missing                                                                                                                                                                                                                                 |
| -------------------------------------------- | --------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Payment on delivery (COD)                    | Users and roles | The Collect payment on delivery permission exists, but not the flow: COD stops, the truck's Stripe reader (S710), external terminal, cash, driver report                                                                                       |
| Count-only receiving for Yard crew           | Users and roles | Yard crew should confirm received quantities and pallet returns without costs. The purchasing screens show costs, so Yard crew don't reach them yet                                                                                            |
| PIN switching on drivers' and techs' devices | Users and roles | Yard crew, Drivers and Service techs can be switched to by PIN, but the switch is only on the pick queue, not the driver app or fleet screens                                                                                                  |
| Rental rates hidden from Service techs       | Users and roles | The fleet screens show rental rates, which Service techs shouldn't see                                                                                                                                                                         |
| QuickBooks page for Accounts                 | Users and roles | Accounts sees the connect and mapping controls. The server refuses them; the page should hide them                                                                                                                                             |
| Selling prices locked for Purchasing         | Users and roles | The product editor leaves price fields editable for Purchasing. The server ignores the changes; the fields should be disabled                                                                                                                  |
| Deactivated users list                       | Users and roles | Deactivated users drop out of Settings → Users. They come back only by bringing back their employee record; there's no list of them to reactivate from                                                                                         |
| Offline work in the yard and on the truck    | Installable app | Offline is a friendly page only. Queuing picks, scale weights, photos and proof of delivery while offline, then syncing, isn't built                                                                                                           |
| Alert preferences                            | Installable app | Alerts are on or off per device. Choosing which kinds of alert to get, and quiet hours, isn't built                                                                                                                                            |
| Installable contractor portal                | Installable app | The portal has no manifest of its own, and contractors get no push alerts (delivery on the way, order ready)                                                                                                                                   |
| One-click DNS setup                          | Emails          | Domain Connect, so yards on GoDaddy, Cloudflare and other supporting hosts add the records by signing in. Needs YardRunner registered as a service provider with each host; until then, yards copy the records or send them to their IT person |
| DNS host detection                           | Emails          | Spotting where a yard's DNS is hosted and showing steps for that host                                                                                                                                                                          |
| Logo processing                              | Emails          | Trimming empty margins, resizing and flattening transparent logos. Logos are shown as uploaded, at 40 px tall                                                                                                                                  |
| Per-yard branding                            | Emails          | A different name, logo or reply-to for each yard. The contact block already uses the yard an email is about                                                                                                                                    |
| Email log and bounces                        | Emails          | Which emails went to whom, delivered or bounced, on each order and rental; flagging a customer's address when it bounces                                                                                                                       |
| Line items in emails                         | Emails          | Order and invoice emails show key facts and a link, not the full line items, pallet charges and totals                                                                                                                                         |
| Branding changes in the yard's audit log     | Emails          | Branding and sending-domain changes are recorded against the tenant, which the yard's own audit page leaves out; staff see them in the back office                                                                                             |
| Catalogue import                             | Setup           | Loading products and price lists from a spreadsheet. Setup's products step offers to load them by email instead                                                                                                                                |
| French emails                                | Emails          | Every email is in English                                                                                                                                                                                                                      |
