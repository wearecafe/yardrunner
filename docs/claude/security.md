# Security checklist

How tenant isolation works, the rules for new code, the automated checks and
the routes anyone can reach. The `/security-review` skill reads this file
first and it overrides the skill's defaults. Record what each review finds
under Known gaps until it's fixed.

## Tenant isolation

- One database; every business table has `tenant_id`. A tenant is one yard
  business.
- `App\Tenancy\BelongsToTenant` adds a global scope and stamps `tenant_id`
  on create. Creating a tenant model with no tenant set fails.
- The current tenant comes from the signed-in user (`IdentifyTenant`, which
  runs before route model binding) and lives in hidden `Context`, so queued
  jobs inherit it. With no tenant set, scoped queries return nothing (fails
  closed).
- Incoming IDs are validated with `TenantRule::exists()` (and
  `YardScope::existsIn()` for yard-scoped records) so one tenant can't
  reference another's rows.
- Users are tenant-scoped through `users.tenant_id`; pickers and recipient
  lists filter by tenant explicitly.
- Back-office reports read tables directly and group by `tenant_id`
  (`TenantMetrics`), because staff have no tenant.

## Rules for new code

1. Every new tenant table has `tenant_id`, an index on it and the
   `BelongsToTenant` scope; add it to `ResetDemoYard::TABLES`.
2. No `withoutGlobalScopes()`, query builder or raw SQL on tenant tables in
   web requests unless the tenant filter is added by hand with a comment
   saying why.
3. Scope lists, dropdown options and `exists` validation, not just show
   pages. Yard-scoped data also goes through `YardScope`.
4. Public and signed pages resolve the tenant from the slug, token or
   record before reading anything. Never create shared records from public
   free text; queue it for staff.
5. Jobs and commands take a tenant id and set it at the start.
6. Cache keys and file paths include the tenant. Files go to private
   storage and are served only through `FileController`, which checks
   access ([file-storage.md](file-storage.md)).
7. Uploads have an explicit size and type allow-list; no SVG, HTML or other
   browser-run formats.
8. Links typed by people and shown as `href` allow only `https?://` or a
   single-slash path.
9. Secrets (OAuth tokens, API keys) use an encrypted cast, are hidden from
   serialization and never reach the audit log.
10. Page props carry only the fields the page needs, never whole models.
    Prices and costs follow `seesPrices()` and See costs and margins.
11. OAuth connections (QuickBooks) finish only in the browser and session
    that started them (state check).
12. Guest posts and anything that sends email or texts are rate-limited.
    Guest routes answer the same whether or not a record exists.
13. New guest routes go in the list below and get a test.
14. Webhooks verify signatures and are safe to run twice.
15. Marketing pages (home and help) set no cookies and are cached at the
    edge, so they must render the same for everyone: no session, no CSRF
    token, nothing about the visitor.
16. A failing `composer audit` or `npm audit` is fixed in the same PR.

## Guest routes

Routes anyone can reach without signing in. Each needs a test.

| Route                                                    | What                                 | Protection                                                      |
| -------------------------------------------------------- | ------------------------------------ | --------------------------------------------------------------- |
| `GET /`                                                  | Marketing home page                  | `marketing` group: no session or cookies, edge-cached           |
| `GET /help…`, `GET /help/suggest`                        | Help center and search               | `marketing` group, edge-cached; suggest throttled 120/min       |
| `GET /start`                                             | Installed app start URL              | Redirects to sign-in when signed out                            |
| `GET, POST /demo`                                        | Demo yard sign-in                    | 404 unless `YARDRUNNER_DEMO`; demo accounts only; throttled     |
| `/login`, `/register`, password reset, two-factor        | Fortify                              | Fortify's rate limits                                           |
| `GET /portal/{slug}`, `POST /portal/{slug}`              | Portal sign-in, emailed link         | Throttled 6/min; same answer whether or not the email exists    |
| `GET /portal/{slug}/enter/{customer}`                    | Portal magic link                    | Signed URL                                                      |
| `POST /portal/{slug}/demo`                               | Demo portal sign-in                  | Demo yard only; throttled                                       |
| `GET /q/{token}`, `POST /q/{token}/accept`               | Public quote and acceptance          | Unguessable token; accept throttled 10/min                      |
| `GET, POST /sign/{token}`, `POST /sign/{token}/pay`      | Rental agreement signing and payment | Unguessable token; posts throttled 10/min                       |
| `GET /book/{slug}…`, `POST /book/{slug}`                 | Rental booking page                  | Rentals add-on on; reads throttled 60/min, booking 10/min       |
| `GET /brand/{slug}/logo`                                 | Yard logo for emails                 | Public on purpose; image only                                   |
| `POST /webhooks/stripe`, `POST /webhooks/stripe/billing` | Stripe webhooks                      | Signature check; CSRF exempt                                    |
| `GET /dev/rental-checkout/{session}`                     | Fake rental card payment             | Demo yard, or the fake driver outside production; otherwise 404 |

## Automated checks

| Check                                                                                                                                   | Where                                        | Status   |
| --------------------------------------------------------------------------------------------------------------------------------------- | -------------------------------------------- | -------- |
| Scoped queries, no-tenant fails closed, bound models hidden, foreign IDs rejected                                                       | `TenancyTest`                                | In place |
| Role reach and permissions                                                                                                              | `RolesAndPermissionsTest`                    | In place |
| Yard scope on lists and records                                                                                                         | `YardScopeTest`                              | In place |
| Every demo-yard page renders on MySQL                                                                                                   | `DemoYardTest` (whole suite on MySQL in CI)  | In place |
| Migrations roll back and re-run                                                                                                         | `Migrations roll back` step in `tests`       | In place |
| Static analysis, formatting, types                                                                                                      | PHPStan, Pint, ESLint, Prettier, `tsc` in CI | In place |
| Dependency updates                                                                                                                      | Dependabot                                   | In place |
| Cross-tenant probe: another tenant's admin on every route and method with this tenant's IDs (403/404)                                   | —                                            | Missing  |
| Sensitive data sweep: markers in prices, costs, balances, pay data and secrets never reach a role not allowed them, on any page or prop | —                                            | Missing  |
| Write probes: low-privilege roles can't change records outside their scope                                                              | Partly in the role tests                     | Partial  |
| Hardening: reset enumeration, rate limits, browser headers, credentials in logs, unsafe links                                           | —                                            | Missing  |
| Marketing pages set no cookies, show nothing about the visitor and send cache headers                                                   | `MarketingPagesTest`                         | In place |
| `composer audit` and `npm audit` in CI                                                                                                  | —                                            | Missing  |

## Known gaps

- The missing automated checks above. Adding them is a phase of its own;
  each new role gets a persona in the sweep and write probes, and each new
  sensitive field a marker with its allowed roles.
- Roles shown controls the server refuses (see [permissions.md](permissions.md#known-gaps)).
- Texts have no opt-out handling yet ([twilio.md](twilio.md)).
