# Docs and workflow

Status: built. PR #58 (Nov 27, 2026) moved all project knowledge into `docs/claude/` and wrote the workflow into `CLAUDE.md`; the delivery-framework PR brought YardRunner onto the Worksite delivery framework.

## Scope

- `CLAUDE.md` holds the workflow, stack, commands and cross-cutting rules, and stays under 16 KB. How each module is built is in `modules/`.
- Decisions are files in `decisions/` with a status. Every earlier decision was rewritten into its own file; the module notes' build-time detail waits in `decisions/pending.md`.
- Each phase or feature has its own doc, listed in [decisions-index.md](decisions-index.md), with its UAT rows (IDs registered in [demo-and-uat.md](demo-and-uat.md#uat-row-ids)).
- `scripts/docs_check.py` runs in the `docs-check` workflow on every PR and on main.
- The `delivery` plugin's skills (`/feature-shaping`, `/phased-delivery`, `/security-review`, `/decision-batch`, `/project-setup`) are shared with the whole team by uploading the plugin under the claude.ai organization's Plugins & skills (an Owner's step; not done as of Nov 27). Since `stack-rules.md` phase 0, `.claude/settings.json` also offers the plugin in Claude Code from `wearecafe/framework`.
- The claude.ai Project keeps only a pointer doc to this repo.

## Decisions

- `decisions/2026-11-27-knowledge-in-docs-claude.md`
- `decisions/2026-11-27-one-file-per-service.md`
- `decisions/2026-11-27-delivery-framework.md`
- `decisions/2026-11-27-decisions-one-file-each.md`
- `decisions/2026-11-27-docs-check-in-ci.md`
- Proposed: `decisions/2026-11-27-uat-area-codes.md`, `decisions/2026-11-27-module-notes-split.md`, `decisions/2026-11-27-no-plugin-settings-yet.md`, `decisions/2026-11-27-ci-one-runner.md`, `decisions/2026-11-27-open-questions-in-index.md`

## Access changes

None.

## Security notes

None: docs, a docs check and CI configuration only.

## Found while adopting the framework

The starter kit's `.gitignore` listed `/CLAUDE.md`, so the `CLAUDE.md` written in PR #58 was never committed. The `docs-check` workflow caught it on its first run. The ignore line is removed and `CLAUDE.md` is now tracked.
