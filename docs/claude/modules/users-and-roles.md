# Users and roles

Every user belongs to one tenant and holds one or more job-based roles
(`UserRole`): `owner` (one per business, created at sign-up), `admin`,
`sales`, `rental_desk`, `yard_crew`, `dispatcher`, `driver`,
`purchasing`, `accounts`, `service_tech` and `time_only`. `users.roles` lists them
(null means just `users.role`); `users.role` is the highest, kept for
sorting and owner lookups. Older values read through
`UserRole::fromValue`: `staff` is Sales and `picker` is Yard crew (the
`2026_10_28_000003` migration renames them in users and in tenant
settings). `User::setRoles()` keeps both in step.

**Screens.** `RouteAccess` maps each role to the route names it reaches:
`full` routes with any method, `view` routes only with GET. Owner and
Admin reach everything. `RestrictByRole` sends anyone else back to their
home screen (`RouteAccess::home`) on a GET outside their roles and
refuses anything else. Routes behind a `can:` gate (settings, the P&L,
timesheets, users, QuickBooks) are left to that gate, so a permission
given to one person works whatever their role. The sidebar hides what a
person can't reach (`auth.routes`, `reachableSections`).

**Yards.** Everyone but Owner and Admin works only their yards
(`location_user`, `User::yardIds()`/`worksAt()`). New people start with
every yard. Lists filter their queries with `YardScope`: the pick queue,
orders and the dashboard, the counter (its yard and yard picker), the
stock overview and its counts and movements, the dispatch board (stops
for their yards' orders and rentals or on their yards' trucks, trucks
based at their yards or nowhere, unassigned pallet pickups, ETA texts),
rentals, the calendar, the fleet, maintenance and rental payments. Forms
only offer and accept their yards (`YardScope::locationRule()`,
`existsIn()`); a stock transfer can go to any yard but only come out of
theirs. Single records are guarded by `RestrictByYard`: an order, rental,
stop, stock level, cycle count, rental unit, truck or maintenance record
(`HasYard`) from another yard is a 404. The driver app and shipment
hand-over keep their own checks.

**Permissions.** `Permission` lists the judgment calls: Set manual line
prices, Discount exempt products (needs manual prices), Override credit
limit, Refund above limit, Collect payment on delivery, Quality-check
sign-off, See costs and margins, See gross-profit P&L, Approve
timesheets and export payroll, Manage users and roles. Each has default
roles; the business changes them per role under Settings → Roles
(`AccessSettings`, `tenants.settings['access']`), and Owner or Admin
grant or remove one for a person under Settings → Users (`users.permissions`,
permission => true/false). `User::hasPermission()` checks the person,
then their roles; the owner holds everything. Only the owner gives See
gross-profit P&L. Gates: `manage-business` (Owner/Admin), `manage-users`,
`override-credit`, `set-prices`, `see-costs`, `manage-time`,
`view-profit`, `manage-credit` and `quickbooks-log` (Owner, Admin,
Accounts). Exempt-product pricing on the price lists page and the
"admins can see the profit report" switch write the same role
permissions.

**Prices and costs.** `User::seesPrices()` (not Yard crew, Dispatcher,
Driver or Service tech) decides whether the queue and pick lists show
money. Without See costs and margins, the product editor shows no cost
and ignores one sent; without `manage-business` it ignores selling
prices (Purchasing edits products, not what they sell for).

**Refund limits.** `AccessSettings` holds a limit per role in cents
(null for none): Sales $250 on goods, Rental desk $250 on rentals,
Owner/Admin/Accounts unlimited, others 0. A user gets their highest.
`RefundApproval::check` runs before a credit-note refund, an order
payment refund, Refund and cancel, and a rental cancellation that
refunds; above the limit someone with Refund above
limit approves with their password or time-clock PIN, and
`RefundApproval::record` logs both names.

**Time & attendance only.** `time_only` clocks in and out and nothing
else: it can't be held with another role, gives no permissions (even
ones granted on the person) and no refunds, isn't on Settings → Roles,
and reaches only the person's own time (`time.self.*`). It's the one role
that can go without an email (`users.email` is nullable); without one,
the person never signs in and punches at the counter tablet with their
PIN. Giving them another role needs an email, which sends the invite.

**Managing people.** Settings → Users (`UserAccountsController`, with
`UserAccounts` shared with Team → Employees) adds people with roles,
yards and permissions, changes them and removes them, each in the audit
log. Removing someone with an employee record deactivates them instead
(`users.deactivated_at`, `UserAccounts::deactivate`): they can't sign in,
a global scope (`User::ACTIVE`) drops them from every list and ends
their session, their employee record is archived, and their hours keep
their name. Bringing the employee back reactivates the user (anyone who
signs in needs Manage users and roles for that). Nobody manages the
owner, themselves, or people who only drive (Settings → Drivers lists
everyone holding Driver).

**Shared devices.** Yard crew, Drivers and Service techs switch in on a
shared tablet with their time-clock PIN (`picker.switch`,
`Employee::checkPin`, throttled), landing on their home screen; anyone
who also holds Owner or Admin can't be switched to that way.

## What every page gets

`HandleInertiaRequests` shares `auth.user` as a hand-picked array (`id`, `name`, `email`, `email_verified_at`), plus `auth.roles`, `auth.can` flags, `auth.routes` and `auth.home`. Roles, permissions, phone and tenant never go to pages through the user. `SharedPropsTest` lists every shared prop, `auth.can` flag and user field: add to it on purpose when a page needs more.
