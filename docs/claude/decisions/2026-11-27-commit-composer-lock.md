# composer.lock is committed

Status: confirmed
Date: 2026-11-27
Area: Stack
Decided by: Jon Moore

**Decision:** `composer.lock` is in the repo, so CI and Laravel Cloud install the same PHP package versions and `composer audit --locked` can run. Dependabot keeps it current.

**Turned down:** Keeping it out and auditing installed packages.

**Source:** `docs/claude/stack-rules.md` phase 1.
