# Close the six Laravel stack gaps in phases

Status: confirmed
Date: 2026-11-27
Area: Process
Decided by: Jon Moore

**Decision:** YardRunner closes the six gaps against the framework's Laravel stack rules, in small PRs: CI checks first (dependency audits, migration rollback, the full suite on MySQL), then the shared-props and public-page tests, then frontend tests with Vitest.

**Turned down:** CI checks only; logging them as gaps.

**Source:** `docs/claude/stack-rules.md`.
