# Yard and delivery

| Feature                                                                  | Screens                         | Code                                           |
| ------------------------------------------------------------------------ | ------------------------------- | ---------------------------------------------- |
| Pick queue, picking with 5% tolerance, bins                              | Orders → Queue, yard kiosk      | `StartPicking`, `RecordPick`                   |
| Loading shipments onto trucks, partial shipments                         | Kiosk                           | `LoadShipment`                                 |
| Stages on/off (Picking, Picked, Loaded, Out), sign-offs at Picked/Loaded | Settings → Order picking, Queue | `PickingSettings`, `PickingFlow`               |
| Yard crew role: their yards' queue only, no money, PIN switching         | Queue, Settings → Users         | `RouteAccess`, `PickerSwitchController`        |
| Dispatch board: trucks × hours, rental drops and pallet pickups too      | Dispatch                        | `Dispatch`, `DispatchController`               |
| Delivery pricing by zone, distance or truckload                          | Settings → Delivery             | `DeliveryPricer`, `DeliverySettings`           |
| ETA texts                                                                | Dispatch                        | `Dispatch::sendEtas`, `EtaMessage`             |
| Proof of delivery (signature, photo)                                     | Driver app, deliver screen      | `CompleteShipment`                             |
| Driver role: the day's truck run on their phone, nothing else            | `/driver`, Settings → Drivers   | `DriverRun`, `DriverController`, `RouteAccess` |
| Truck locations from drivers' phones (kept 30 days)                      | Driver app, Dispatch            | `DriverRun::locate`, `DriverPing`              |
| Contractor tracking: stops before yours, truck map when next             | Portal order page               | `DeliveryTracking`                             |

## Order picking stages and checks

`tenants.settings['picking']` (`PickingSettings`) says which of Picking,
Picked, Loaded and Out for delivery a yard uses (New and Delivered /
Picked up are always on) and whether a sign-off is required at Picked,
Loaded or both, by which roles, and whether it must be someone other
than whoever completed the stage. `PickingFlow` moves a shipment through
the stages that are on and records each check in `shipment_checks`; a
shipment waiting for one has `check_stage` set. `LoadShipment::release`
is where stock leaves and a delivery reaches the dispatch board
(`shipments.released_at`), so it only runs once every required check is
signed off. A rejection sends the shipment back a stage with the reason.
With Loaded off, a released delivery waits as `ready` ("Ready for
dispatch"); with Out off, `Dispatch::enRoute` leaves the shipment as it
is.

## Drivers

Users with the `driver` role are added under Settings → Drivers (an
emailed link to choose a password) with their phone number, and the
owner ticks every truck they can drive (`driver_truck`, changed any
time with "Edit"). Who drives a truck on a day is picked on the dispatch
board from the drivers ticked for it (`truck_assignments`,
`DriverRun::assign`); that driver sees the truck's run for that day, and
unticking a truck drops their days ahead on it. Trucks hold no driver
details: they carry material, rental equipment or both, with a list of
capacities in tonnes, cubic yards, cubic metres or pallets
(`trucks.capacities`). `RouteAccess` keeps people who only drive to
`driver.*`, the deliver screen and their own account settings. While `/driver` is open
on their phone, `useLocationSharing` posts the position about once a
minute (`driver.locate`); the latest is kept on the truck for the board
and every ping in `driver_pings`, pruned after 30 days. Browsers only
share location while the page is on screen. The portal shows
`DeliveryTracking` when the yard turns it on in Settings → Delivery; the
truck's position shows only once the contractor's stop is next and on
its way. The demo driver never shares a visitor's real position:
`yardrunner:demo:drive` moves Truck 2 along a fixed route every five
minutes instead.
