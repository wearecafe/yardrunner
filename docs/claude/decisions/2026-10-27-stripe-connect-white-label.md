# Each yard gets a white-labelled Stripe connected account

Status: confirmed
Date: 2026-10-27
Area: Payments
Decided by: Jon Moore

**Decision:** YardRunner creates a Stripe Connect account per yard (no Stripe dashboard; Stripe carries negative-balance risk; the yard pays Stripe's fees), managed through embedded components in Settings → Payments. Direct charges, so the yard is merchant of record. YardRunner takes an application fee (default 0.5%). Yards can't link their own Stripe account.

**Turned down:** Yards linking their own Stripe account.

**Source:** `docs/claude/commerce-data-model.md` → Decisions (Stripe Connect); `docs/claude/stripe.md`.
