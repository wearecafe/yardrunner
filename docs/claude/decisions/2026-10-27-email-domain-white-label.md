# Sending-domain verification is white-labelled

Status: confirmed
Date: 2026-10-27
Area: Emails
Decided by: Jon Moore

**Decision:** Domain verification happens inside YardRunner and every record value points at yardrunner.ca. The DKIM host may read `resend._domainkey`.

**Turned down:** Moving to a provider that allows custom DKIM selectors.

**Source:** `docs/claude/email-branding.md` → Decisions; `docs/claude/resend.md`.
