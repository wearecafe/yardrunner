# Production runs on MySQL

Status: confirmed
Date: 2026-11-27
Area: Stack
Decided by: Jon Moore

**Decision:** Production uses MySQL. The main suite may run on SQLite; raw SQL must also work on MySQL 8.4 (the `mysql` CI job).

**Turned down:** Postgres.

**Source:** Project instructions; PR #58. 2026-11-27: the CI part is superseded by `2026-11-27-full-suite-on-mysql.md` (the whole suite now runs on MySQL in CI); production on MySQL stands.
