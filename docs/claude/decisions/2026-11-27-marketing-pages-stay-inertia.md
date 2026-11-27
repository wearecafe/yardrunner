# Marketing pages stay in Inertia (an exception to the Laravel stack rule)

Status: confirmed
Date: 2026-11-27
Area: Marketing pages
Decided by: Jon Moore

**Decision:** The home page and help center stay Inertia pages in `routes/marketing.php`, guarded by full page loads, rather than Blade views in `routes/site.php`.

**Turned down:** Rebuilding them as Blade views.

**Source:** `docs/claude/docs-and-workflow.md`; `docs/claude/marketing-pages.md`.
