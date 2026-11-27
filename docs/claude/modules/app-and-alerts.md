# Installable app and alerts

YardRunner is a progressive web app: staff install it from the browser to
the home screen of a counter tablet, a yard tablet or a driver's phone, and
it opens full screen from its own icon. One install for everyone: it opens
at `/start` (`PwaController`), which sends each person to their home screen
for their roles.

| Piece                                                          | Code                                                           |
| -------------------------------------------------------------- | -------------------------------------------------------------- |
| Manifest, icons (with maskable), theme colours                 | `public/manifest.webmanifest`, `public/icons`, `app.blade.php` |
| Service worker: offline page, cached assets, push, tap to open | `public/sw.js`, `public/offline.html`                          |
| Registration, "new version" reload toast, install prompt       | `resources/js/lib/pwa.ts`                                      |
| No-connection strip                                            | `components/offline-banner.tsx`                                |
| Turning alerts on per device, keeping devices current          | `resources/js/lib/push.ts`, `components/alerts-prompt.tsx`     |
| Settings → Alerts: this device, install steps, all devices     | `Settings\AlertsController`, `pages/settings/alerts.tsx`       |
| Who hears about what                                           | `app/Alerts/StaffAlerts`, `StaffAlert`, `WebPushChannel`       |

**Offline.** Pages always come from the network, since they hold live
orders, stock and prices; nothing a person sees is cached on the device.
With no connection a page load shows a branded "No connection" page that
reloads itself when the signal returns, and open screens show a strip
saying changes won't save. Built assets are cached, so the app opens fast
on weak yard Wi-Fi. The service worker is only registered in production
builds. Bump `VERSION` in `sw.js` when it or the offline page changes.

**Alerts.** Web push, to the devices each person turned alerts on for.
Never prices (Yard crew and Drivers don't see them). They're worked out
after the change commits, so a counter sale handed over on the spot never
alerts the yard.

| Alert                         | Who                                                                   | When                                             |
| ----------------------------- | --------------------------------------------------------------------- | ------------------------------------------------ |
| New order to pick             | Yard crew at the order's yard                                         | An order is confirmed and waits in the queue     |
| Check needed at Picked/Loaded | Everyone who may sign it off at that yard (not the person who did it) | A load moves to Awaiting check                   |
| Check rejected                | The person whose work it was                                          | A checker rejects, with the reason               |
| You're on a truck             | The driver                                                            | Dispatch puts them on a truck for today or later |
| New stop on your run          | Today's driver of that truck                                          | A stop is scheduled onto it for today            |
| Rental overdue                | Rental desk at the rental's yard                                      | Past due and grace, once a day per rental        |

Each person sees what they get under Settings → Alerts, can send a test,
and can remove other devices. Staff whose roles get alerts are asked once,
in a dismissable card, whether to turn them on. A device belongs to whoever
last signed in on it, so a shared yard tablet follows the PIN switch. On
iPhone and iPad, web push only works from the installed app, so they're
shown how to add it first. Alerts are off in the demo yard.
