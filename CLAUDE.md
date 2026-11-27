# YardRunner

Inventory, pricing, orders, rentals and time for masonry and landscape supply yards, sold as a multi-tenant SaaS by Café. Runs on Laravel Cloud.

Product scope, decisions and the access model live in `docs/claude/` in this repo, the only master copy. Don't keep copies in a claude.ai Project or elsewhere.

- **Decisions:** each decision is its own file in `docs/claude/decisions/` with a status (`docs/claude/decisions/README.md`). Never re-ask or undo a confirmed decision. Waiting ones are files with `Status: proposed` and the rows in `docs/claude/decisions/pending.md`. Unanswered questions are in `docs/claude/decisions-index.md` → Open questions.
- **Phases and features:** each keeps its scope, access changes, security notes and UAT rows in its own doc in `docs/claude/`, listed in `docs/claude/decisions-index.md`.

## Workflow

This section is the one written copy of the workflow. Other docs, and the claude.ai Project instructions, point here.

- Before working, read this file, then `docs/claude/decisions-index.md` and the docs and module notes for every area you'll touch.
- One phase per PR. Commit and push as you go. Squash-merge when CI is green, then start the next phase. Stop only for questions.
- Ask before any judgement call, as multiple choice (the question tool, never as text in a reply) with the recommended option first. Anything legal or compliance-related (tax, employment standards, privacy, consent, payments, insurance): describe the rule with its threshold and source, offer strictness levels (block, warn, track, leave out) and let Jon Moore choose. Never build a rule in on an assumption. Where a professional must confirm, add a UAT row.
- Never lose existing management functionality or fields. A rework keeps a parity table (`docs/claude/<rework>-parity.md`).
- Docs travel with the work. A PR adds a decision file for each choice made (Jon's answers as `confirmed`, your own build-time choices as `proposed`, listed in the PR body), and in the same PR updates its phase doc (access, security notes, UAT rows), its `decisions-index.md` row and the module notes. Several sessions may work at once, so keep doc edits small, rebase on main before merging, and never settle a conflict by dropping another session's entries.
- Every outside service or integration has its own doc, `docs/claude/<service>.md`, listed in `docs/claude/README.md` → Outside services.
- Every feature ships a help article (`resources/help/{category}`; always update `getting-started/staff-and-roles.md` when access changes), marketing-site points (`resources/js/pages/welcome.tsx`), and UAT rows with feature IDs (`docs/claude/demo-and-uat.md` → UAT row IDs).
- Run the pre-merge checks in `docs/claude/delivery-checklist.md` and report what they found in the PR (`.github/pull_request_template.md`).
- Use the `delivery` skills, shared with the whole team: `/feature-shaping` before building, `/phased-delivery` while building, `/security-review` before merging (it reads `docs/claude/security.md` first), and `/decision-batch` to confirm what's pending. In Claude Code with the plugin installed they're `/delivery:<skill>`.

## Keep this file short

This file is loaded into every session, so it holds only what every session needs: workflow, stack, commands and cross-cutting rules. How a module is built goes in `docs/claude/modules/<module>.md`, linked below. CI fails when this file passes 16 KB or links a `docs/claude/` file that doesn't exist (`scripts/docs_check.py`).

## Module notes (read before working on that area)

| Area | Notes |
| ---- | ----- |
| Counter, orders, pricing, units | `docs/claude/modules/orders.md` |
| Yard, picking, dispatch, drivers | `docs/claude/modules/fulfillment.md` |
| Rentals | `docs/claude/modules/rentals.md` |
| Inventory and purchasing | `docs/claude/modules/inventory.md` |
| Receivables, returns, credit | `docs/claude/modules/receivables.md` |
| Profit and loss | `docs/claude/modules/profit-and-loss.md` |
| Contractor portal | `docs/claude/modules/portal.md` |
| Time and attendance | `docs/claude/modules/time-and-attendance.md` |
| Setup for new businesses | `docs/claude/modules/setup.md` |
| Audit log | `docs/claude/modules/audit-log.md` |
| Users, roles and permissions | `docs/claude/modules/users-and-roles.md` |
| Installable app and alerts | `docs/claude/modules/app-and-alerts.md` |
| Emails and branding | `docs/claude/modules/emails.md` |
| Back office (YardRunner staff) | `docs/claude/modules/back-office.md` |
| Billing (subscriptions) | `docs/claude/modules/billing.md` |
| Demo yard | `docs/claude/modules/demo-yard.md` |
| Help center | `docs/claude/modules/help-center.md` |

When a module gets substantial work, start its notes file and add a row here.

## Stack

- Laravel 13 on Laravel Cloud (`docs/claude/laravel-cloud.md`). Fortify for sign-in (email and password, TOTP two-factor). Inertia 3, React 19, TypeScript, Tailwind 4 and shadcn/ui. Wayfinder for typed routes. Stay with shadcn: one documented design system across the app.
- MySQL in production. The main suite runs on SQLite; the `mysql` CI workflow runs raw SQL and every demo-yard page on MySQL 8.4. Every migration has a working `down`.
- Fonts are self-hosted (Bunny Fonts, downloaded at build). No third-party font or script requests from the app.
- Files go to `YARDRUNNER_FILES_DISK` (a private Laravel Cloud bucket in production) and are served through `FileController`, which checks access. Never `->url()` or public visibility.
- Queues run QuickBooks sync, alerts and billing updates. Jobs that touch tenant data take the tenant id and set it first.

## Commands

- `composer test`: Pint, PHPStan and PHPUnit. `npm run check` (format and lint) and `npm run types:check`. All must pass before pushing.
- `vendor/bin/pint --dirty` / `npm run check:fix`: format. Only format files you touched.
- `composer dev`: app, queue and Vite.
- `php artisan yardrunner:demo:reset`: rebuild the demo yard.
- `python3 scripts/docs_check.py`: the living-docs check (also the `docs-check` CI workflow).

## Tenancy (read before adding any table)

- A tenant is one yard business; every business table has `tenant_id` and the `BelongsToTenant` scope, and is listed in `ResetDemoYard::TABLES`.
- The current tenant comes from the signed-in user (`IdentifyTenant`) and lives in hidden `Context`, so queued jobs inherit it. With no tenant set, scoped queries return nothing.
- IDs from forms are validated with `TenantRule::exists()` (and `YardScope::existsIn()` for yard-scoped records). Users are filtered by tenant explicitly.
- Rules for new code, guest routes and known gaps: `docs/claude/security.md`.

## Access

- Eleven job roles (`App\Enums\UserRole`), several per user; judgement calls are permissions (`App\Enums\Permission`) with role defaults. Every role but Owner and Admin is scoped to its yards (`YardScope`).
- Screens by role: `RouteAccess`. Prices and costs on pages follow `User::seesPrices()` and See costs and margins. Every action checks access on the server, not just in the UI.
- When access changes, update `docs/claude/permissions.md`, the access-by-screen table in `docs/claude/commerce-data-model.md`, the roles help article and the role tests.

## Design

- Design within the documented system (`docs/claude/design-system.md`). Don't make different parts of the app look like different software.
- Design like a professional UI designer who cares about the product: make it enjoyable to use, but nothing is added for decoration. The design serves the jobs people do.

## Public pages (cookieless, edge-cached)

- The home page and help center are in `routes/marketing.php`, in the `marketing` middleware group only: no session, CSRF, cookies or signed-in user. `ServeFromEdgeCache` sets `Cache-Control: public, s-maxage` and strips any cookie.
- Unlike the Laravel stack's default (Blade views in `routes/site.php`), they stay Inertia pages, and the browser loads them whole (`resources/js/lib/edge-cached-pages.ts`; keep its paths in step with the routes). Nothing on them may depend on the visitor.
- `tests/Feature/MarketingPagesTest.php` checks every marketing route for cookies and caching headers. Details: `docs/claude/marketing-pages.md`.
