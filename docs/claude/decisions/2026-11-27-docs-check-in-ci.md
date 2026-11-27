# The living docs are checked by docs_check.py in CI

Status: confirmed
Date: 2026-11-27
Area: Process
Decided by: Jon Moore

**Decision:** `scripts/docs_check.py` runs in its own `docs-check` workflow on every PR and on main, including the rule that code changes come with `docs/claude/` changes (the `no-docs-needed` label skips it).

**Turned down:** The PHPUnit docs tests, alone or as well.

**Source:** `docs/claude/docs-and-workflow.md`.
