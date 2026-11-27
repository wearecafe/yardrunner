# Contractor portal

Account contacts sign in at `/portal/{yard}` with an emailed, signed link
(30 minutes). Session key `portal` holds the customer and tenant.

| Feature                                             | Code                                     |
| --------------------------------------------------- | ---------------------------------------- |
| Recent orders, delivery status and ETA              | `Portal/PortalController::home`, `order` |
| Reorder a past order as a draft for the counter     | `reorder`                                |
| Patio calculator: area → pavers, base, sand         | `app/Portal/MaterialCalculator`          |
| Statement and PDF                                   | `statement`, `statementPdf`              |
| Pallet pickup requests (land on the dispatch board) | `palletPickup`                           |

Orders from the portal are drafts with `source = portal`; the counter
confirms them.
