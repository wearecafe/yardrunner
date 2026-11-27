# Price lists can discount a whole category

Status: confirmed
Date: 2026-10-26
Area: Pricing
Decided by: Jon Moore

**Decision:** A price list can give a $ or % discount on a category, one rule per category per list. $ amounts are per base unit, converted to each selling unit. Whether category rules beat product rules is a tenant default, overridable per price list. Stacking a category discount on a volume-break price is optional per price list (default off).

**Source:** `docs/claude/commerce-data-model.md` → Category rule details; Settings.
