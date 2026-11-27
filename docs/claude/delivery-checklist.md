# Delivery checklist

The `/phased-delivery` skill adapted to YardRunner's commands. One phase per
branch and PR; every phase ships something whole.

## Build each phase in this order

1. **Data:** migrations with a working `down` (and a data conversion when
   existing data changes shape), models, a helper class for the access rules.
   New tenant tables get `tenant_id` and `BelongsToTenant`, and go in
   `ResetDemoYard::TABLES` (children before parents).
2. **Server:** controllers that check access on every action, routes
   (named, so `RouteAccess` can list them), menu items, shared `auth.can`
   flags if other pages link to it.
3. **Pages:** Inertia pages in `resources/js/pages`, built from the
   [design system](design-system.md). Hide what the person can't use and
   enforce the same rule on the server. Send only the props the page needs.
4. **Demo data** in `DemoYardSeeder` that shows the feature off, with
   realistic loose ends (something overdue, something awaiting review),
   dated relative to today and seeded idempotently.
5. **Tests:** access for every role, the main workflow, validation, edge
   cases found while building. New sensitive fields and roles go into the
   security checks ([security.md](security.md)).
6. **Docs for people:** a help article in `resources/help/{category}`,
   related articles updated (always `getting-started/staff-and-roles.md`
   when access changes), points on the marketing home page
   (`resources/js/pages/welcome.tsx`), screenshot entries.
7. **Docs for the project** (same PR):
    - a decision file for every choice: Jon's answers as `confirmed`, your own
      build-time choices as `proposed` (`Decided by: Claude, at build`), listed
      in the PR body;
    - the phase or feature doc: scope, access changes, security notes, UAT
      rows with feature IDs;
    - its row in [decisions-index.md](decisions-index.md);
    - `modules/<module>.md` when how the module is built changed (start the
      file and add its row to `CLAUDE.md` if the module had none), and the
      "Not built yet" table in [gap-list.md](gap-list.md);
    - [permissions.md](permissions.md) and [security.md](security.md) when
      they change, and a new `docs/claude/<service>.md` for any new outside
      service (listed in [README.md](README.md#outside-services)).
8. Format touched files only, type-check, build, run the full suite:

    ```bash
    vendor/bin/pint --dirty
    npm run check:fix    # format + lint fixes
    npm run types:check
    npm run build
    composer test        # Pint, PHPStan, PHPUnit
    npm run check        # format + lint, as CI runs it
    python3 scripts/docs_check.py
    ```

## Checks before merging (every phase)

| Check                                                          | How here                                                                             |
| -------------------------------------------------------------- | ------------------------------------------------------------------------------------ |
| Permission probe: every role against every new page and action | Feature tests per role, plus the demo personas in [demo-and-uat.md](demo-and-uat.md) |
| Security tests and a diff review                               | `/security-review` against [security.md](security.md)                                |
| Dependency audit                                               | `composer audit`, `npm audit`                                                        |
| Functional run as real demo people                             | Browser script through `/demo`, waiting for each save                                |
| Link audit with new pages added                                | Every link on the new pages, as each persona                                         |
| UI crawl: desktop and phone widths, light and dark, axe        | Then look at the screenshots                                                         |
| Junk-form sweep                                                | Blank and junk input to every new form: no 500s                                      |
| Query counts per page                                          | No N+1 queries on the new pages                                                      |
| MySQL                                                          | The `mysql` CI job (raw SQL and every demo page)                                     |
| Volume test for each bulk path at realistic size               | Imports, exports and reports at a real yard's size                                   |
| Docs check                                                     | `python3 scripts/docs_check.py` (also the `docs-check` CI job)                       |
| CI runner balance                                              | Compare job times; shard when the suite passes about 10 minutes                      |

If a check isn't available in the session (no browser, say), the PR says
so and the check becomes a UAT row.

## Merge

- PR body follows `.github/pull_request_template.md`: what was built, Jon's
  decisions and the proposed build-time choices (each with its file), each
  check and what it found and fixed, the test count.
- Wait for every CI job (`tests`, `mysql`, `docs-check`), squash-merge,
  start the next phase. Several sessions may work at once: rebase on main
  before merging and never drop another session's doc entries.

## After merging

- Tell Jon briefly what shipped, what the checks found, and anything to
  decide or test.
- The UAT rows (in the feature's own doc, with feature IDs) are only for
  what needs a person: real emails, PDFs, phones, wording and judgement.
  Leave the results for Jon.
- If anything in the living docs was missed in the PR, fix it in a small
  follow-up PR straight away.
