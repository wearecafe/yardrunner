# Links to marketing pages are full page loads

Status: confirmed
Date: 2026-11-27
Area: Marketing pages
Decided by: Jon Moore

**Decision:** Inertia visits and prefetches to `/` and `/help…` become full page loads; the server answers stray Inertia visits with an uncached 409.

**Turned down:** Keeping Inertia navigation with a separate data URL.

**Source:** `docs/claude/marketing-pages.md`; PR #59.
