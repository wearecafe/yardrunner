# The whole test suite runs on MySQL in CI

Status: confirmed
Date: 2026-11-27
Area: Stack
Decided by: Jon Moore

**Decision:** The `tests` workflow runs the whole suite on MySQL 8.4, and first rolls every migration back and migrates again. The separate `mysql` workflow (profit report and demo pages only) is removed. SQLite stays for local runs.

**Turned down:** Running both SQLite and MySQL in CI.

**Source:** `docs/claude/stack-rules.md` phase 1. Supersedes the CI part of `2026-11-27-mysql-production.md`.
