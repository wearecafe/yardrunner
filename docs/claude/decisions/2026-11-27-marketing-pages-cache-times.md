# Marketing pages: 5 minutes in browsers, 1 hour at the edge, purged on deploy

Status: confirmed
Date: 2026-11-27
Area: Marketing pages
Decided by: Jon Moore

**Decision:** `Cache-Control: public, max-age=300, s-maxage=3600` on successful pages; Laravel Cloud purges the edge on every deploy.

**Turned down:** 5 minutes with no purge; a day with purge.

**Source:** `docs/claude/marketing-pages.md`; PR #59; `docs/claude/laravel-cloud.md` → Edge cache.
