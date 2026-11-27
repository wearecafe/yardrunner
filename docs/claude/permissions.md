# Access model

Who can see and do what in YardRunner, and how the code enforces it. The
product rules (roles, defaults, the access-by-screen table) are in
[commerce-data-model.md](commerce-data-model.md#roles-and-permissions); the
implementation detail is in [modules/users-and-roles.md](modules/users-and-roles.md).
Update this file whenever access changes.

## Who signs in where

| Person                    | Signs in at                     | How                                                                                  | Tenant                      |
| ------------------------- | ------------------------------- | ------------------------------------------------------------------------------------ | --------------------------- |
| Yard staff (tenant users) | `/login`                        | Email and password (Fortify), optional two-factor; PIN switch on shared yard devices | One, from `users.tenant_id` |
| Time & attendance only    | Counter tablet                  | Time-clock PIN only, never a full sign-in when they have no email                    | Their tenant                |
| Contractor contacts       | `/portal/{slug}`                | Emailed signed link (magic link), held in the session (`EnsurePortalCustomer`)       | The yard in the URL         |
| Rental customers          | `/sign/{token}`, `/book/{slug}` | Token link, or the public booking page                                               | From the token or slug      |
| Quote recipients          | `/q/{token}`                    | Token link                                                                           | From the token              |
| YardRunner staff          | `/admin`                        | Email and password plus required two-factor; no tenant                               | None (`is_platform_staff`)  |
| Demo visitors             | `/demo`                         | One click into a demo-yard account                                                   | The demo yard only          |

## Roles

`App\Enums\UserRole`: Owner, Admin, Sales, Rental desk, Yard crew,
Dispatcher, Driver, Purchasing & inventory, Accounts, Service tech, and
Time & attendance only. Users hold several; their access is the union.
`staff` reads as Sales and `picker` as Yard crew.

## Permissions (judgement calls)

`App\Enums\Permission`, defaults per role in `AccessSettings`, changeable
per role (Settings → Roles) and per person (Settings → Users).
`User::hasPermission()` checks the person, then their roles; the owner holds
everything; Time & attendance only holds nothing.

| Permission                            | Default holders                      | Gate                 |
| ------------------------------------- | ------------------------------------ | -------------------- |
| Set manual line prices                | Owner, Admin                         | `set-prices`         |
| Discount exempt products              | Owner                                | (checked in pricing) |
| Override credit limit                 | Owner, Admin, Accounts               | `override-credit`    |
| Refund above limit                    | Owner, Admin, Accounts               | (`RefundApproval`)   |
| Collect payment on delivery           | Driver                               | (flow not built)     |
| Quality-check sign-off                | Set per tenant and stage             | (fulfillment)        |
| See costs and margins                 | Owner, Admin, Purchasing & inventory | `see-costs`          |
| See gross-profit P&L                  | Owner                                | `view-profit`        |
| Approve timesheets and export payroll | Owner, Admin                         | `manage-time`        |
| Manage users and roles                | Owner, Admin                         | `manage-users`       |

Role gates: `manage-business` (Owner, Admin), `manage-credit` and
`quickbooks-log` (Owner, Admin, Accounts).

## How it's enforced

| Layer            | Helper                                                                | What it does                                                                                             |
| ---------------- | --------------------------------------------------------------------- | -------------------------------------------------------------------------------------------------------- |
| Tenant           | `BelongsToTenant`, `IdentifyTenant`, `TenantRule`                     | Every business query is scoped to the signed-in user's tenant; IDs from forms are validated within it    |
| Screens          | `RouteAccess`, `RestrictByRole`                                       | Maps each role to the route names it reaches (`full` or `view`); everything else goes home or is refused |
| Gates            | `can:` middleware                                                     | Settings, P&L, timesheets, users and QuickBooks follow permissions, not roles                            |
| Yards            | `YardScope`, `RestrictByYard`, `HasYard`                              | Everyone but Owner and Admin works only their yards; another yard's record is a 404                      |
| Money on screen  | `User::seesPrices()`, See costs and margins                           | Yard crew, Dispatcher, Driver and Service tech never get prices; costs need the permission               |
| Refunds          | `RefundApproval::check` / `record`                                    | Per-role limits; above the limit someone with Refund above limit approves on the spot                    |
| Add-ons, billing | `EnsureRentals`, `EnsureTimeTracking`, `EnsureBilling`, `EnsureSetUp` | Screens close when an add-on is off, billing is locked or setup isn't done                               |
| Back office      | `EnsurePlatformStaff`, `KeepStaffInBackOffice`                        | Tenant users get a 404 on `/admin`; staff stay out of yard screens                                       |
| Demo             | `ProtectDemoAccounts`                                                 | Demo accounts can't change account or business settings                                                  |
| Pages            | `HandleInertiaRequests` (`auth.can`, `auth.routes`)                   | The sidebar and buttons hide what the person can't use; the server still refuses it                      |

## Checklist for a new feature

- [ ] Which roles reach each new route? Add it to `RouteAccess` (or put it
      behind a `can:` gate when a permission decides it).
- [ ] Is it a judgement call that cuts across jobs? Make it a `Permission`
      with role defaults, logged on use, not a new role.
- [ ] Yard-scoped data: model uses `HasYard`, lists filter with
      `YardScope`, forms use `YardScope::locationRule()`.
- [ ] Prices or costs on the page? Respect `seesPrices()` and See costs and
      margins in the props, not just the UI.
- [ ] Hide controls the person can't use and enforce the same rule on the
      server (`auth.can` flags for other pages that link to it).
- [ ] Role test: every role against every new page and action
      (`RolesAndPermissionsTest`, `YardScopeTest`).
- [ ] Update this file, the access-by-screen table in
      [commerce-data-model.md](commerce-data-model.md#access-by-screen), the
      roles help article (`resources/help/getting-started/staff-and-roles.md`),
      and [security.md](security.md) for new guest routes
      or sensitive fields.

## Known gaps

Listed under Roles and permissions in [gap-list.md](gap-list.md): controls
shown to roles the server refuses (Purchasing prices, Accounts on the
QuickBooks page, rental rates for Service techs), count-only receiving for
Yard crew, PIN switching outside the pick queue, and a deactivated users
list.
