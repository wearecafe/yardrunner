# Laravel stack rules

Status: in progress. Closing the gaps between YardRunner and the Laravel stack rules in `wearecafe/framework` (`stacks/laravel/CLAUDE-stack.md`), in phases. Decided in `decisions/2026-11-27-stack-rules-in-phases.md`.

## Scope

| Phase | Gap                            | Rule in the framework                                        | Status          |
| ----- | ------------------------------ | ------------------------------------------------------------ | --------------- |
| 0     | Delivery plugin in Claude Code | `.claude/settings.json` turns the plugin on                  | Built (this PR) |
| 1     | Dependency audits in CI        | `composer audit` and `npm audit` on every PR                 | Not built       |
| 1     | Migration rollback in CI       | Roll back and re-migrate on MySQL                            | Not built       |
| 1     | Full suite on MySQL            | CI runs MySQL; SQLite only locally                           | Not built       |
| 2     | Shared props guard             | A test guards what `HandleInertiaRequests` shares            | Not built       |
| 2     | Public pages: forms and links  | The public-pages test also checks for forms and broken links | Not built       |
| 3     | Frontend tests                 | `npm test`: Vitest and React Testing Library beside the code | Not built       |

YardRunner's marketing pages stay Inertia pages, not the stack's Blade views in `routes/site.php` (`decisions/2026-11-27-marketing-pages-stay-inertia.md`). CI stays on one runner while the suite runs in about two minutes (`decisions/2026-11-27-ci-one-runner.md`).

## Decisions

- `decisions/2026-11-27-framework-repo-is-the-source.md`
- `decisions/2026-11-27-stack-rules-in-phases.md`
- `decisions/2026-11-27-plugin-from-framework-repo.md`
- `decisions/2026-11-27-project-instructions-yardrunner-block.md`

## Access changes

None.

## Security notes

- `.claude/settings.json` holds no secrets: it names a public marketplace source and turns the plugin on. `.claude/settings.local.json` and the rest of `.claude/` stay ignored.

## UAT rows

Results are Jon's to fill in. IDs: `demo-and-uat.md` → UAT row IDs.

| #    | What to check                                                                                                                                                            | Who | Result |
| ---- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------ | --- | ------ |
| DW-1 | Open the repo in Claude Code on a team member's machine: it offers the `delivery` plugin from `worksite`, and `/delivery:feature-shaping` and the other four skills load | Jon |        |
