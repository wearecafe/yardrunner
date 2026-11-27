# Inventory and purchasing

| Feature                                            | Screens           | Code                   |
| -------------------------------------------------- | ----------------- | ---------------------- |
| Stock ledger per yard; every change is a movement  | Inventory         | `InventoryLedger`      |
| Colour runs (lots); orders ship from one run       | Inventory, kiosk  | `StockLot`             |
| Purchase orders, receiving into runs, average cost | Purchasing        | `ReceivePurchaseOrder` |
| Transfers between yards                            | Inventory         | `TransferStock`        |
| Cycle counts                                       | Inventory → Count | `CycleCounting`        |
