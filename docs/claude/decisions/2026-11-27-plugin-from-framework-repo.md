# Claude Code turns on the delivery plugin from wearecafe/framework

Status: confirmed
Date: 2026-11-27
Area: Process
Decided by: Jon Moore

**Decision:** `.claude/settings.json` registers the `worksite` marketplace from the `wearecafe/framework` repo (`path: marketplace/.claude-plugin/marketplace.json`) and enables `delivery@worksite`, so everyone who opens the repo in Claude Code is offered the plugin. `.gitignore` keeps the rest of `.claude/` ignored.

**Turned down:** Waiting for a repo with the marketplace at its root.

**Source:** `docs/claude/stack-rules.md`. Supersedes `2026-11-27-no-plugin-settings-yet.md`. Checked: the subfolder layout validates and installs all five skills from a local copy; the GitHub fetch is UAT row DW-1.
