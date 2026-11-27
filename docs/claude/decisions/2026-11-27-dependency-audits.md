# Any dependency advisory fails CI

Status: confirmed
Date: 2026-11-27
Area: Stack
Decided by: Jon Moore

**Decision:** The `Dependency audit` job in the `tests` workflow runs `composer audit --locked` and `npm audit` on every PR and on main. Any advisory, at any severity, fails the build; fix it in the same PR.

**Turned down:** Failing only on high and critical, with an allow-list for accepted advisories; reporting only.

**Source:** `docs/claude/stack-rules.md` phase 1.
