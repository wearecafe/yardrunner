# Pages get four fields of the signed-in user

Status: proposed
Date: 2026-11-27
Area: Access
Decided by: Claude, at build

**Decision:** The shared `auth.user` prop is `id`, `name`, `email` and `email_verified_at`, picked by hand in `HandleInertiaRequests`. Roles, permissions, phone, tenant and the rest of the model stay on the server; pages that need them get their own props. `SharedPropsTest` fails when a shared prop, an `auth.can` flag or a user field is added without updating it.

**Turned down:** Sending the whole `User` model (minus `$hidden`), as before.

**Source:** `docs/claude/stack-rules.md` phase 2; framework stack rule "Props".
