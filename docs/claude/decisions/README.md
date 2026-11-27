# Decision log

Every decision about this project gets its own file here: one choice per file, so parallel sessions never edit the same file and nothing has to be renumbered.

## Where decisions are

- **New decisions:** a file in this folder (below).
- **Before the decision log (Oct 26 to Nov 27, 2026):** every decision Jon made was rewritten into its own file here when YardRunner adopted the log (`2026-11-27-decisions-one-file-each.md`). The area docs (`commerce-data-model.md`, `email-branding.md`, `quickbooks-sync-map.md` and the rest) keep the detail and the reasoning. Where a file here and an area doc disagree, the file here wins.
- **Waiting for the product owner:** older choices that were never confirmed are listed in `pending.md`. New ones are files here with status `proposed`. To find them all, run `grep -l "^Status: proposed" docs/claude/decisions/*.md` and read `pending.md`.

## A decision file

Name it `YYYY-MM-DD-short-slug.md`, using the date it was decided or proposed. Copy `TEMPLATE.md`. The header lines are checked by `scripts/docs_check.py`:

```
# Short title saying what was chosen

Status: proposed | confirmed | reversed | superseded
Date: YYYY-MM-DD
Area: Billing
Decided by: Jon Moore (or "Claude, at build" while proposed)
```

Then:

- **Decision:** what was chosen, written as the rule the code follows.
- **Turned down:** the options not taken. Leave this out only when there were none.
- **Why:** only when the reason isn't obvious.
- **Source:** the phase doc or PR it belongs to, and any decision it reverses or supersedes.

## Statuses

| Status       | Meaning                                                                                                                                               |
| ------------ | ----------------------------------------------------------------------------------------------------------------------------------------------------- |
| `proposed`   | Chosen by Claude while building, or a detail that follows from a decision without being asked. It's built, but the product owner hasn't confirmed it. |
| `confirmed`  | The product owner chose it, or confirmed a proposal. Never re-ask or undo it.                                                                         |
| `reversed`   | The product owner overruled it. The file says what replaces it and the follow-up work.                                                                |
| `superseded` | A later decision replaced it. Its `Source` names that file.                                                                                           |

Change a status by editing that one line and adding a dated note under **Source**. Never delete a decision file.

## Confirming in batches

The product owner confirms proposals and the backlog in `pending.md` in batches, through multiple-choice questions, usually one feature's set per question (`/decision-batch`). Each batch becomes a file here (`confirmed`, with any overruled items split into their own `reversed` files), and the rows in `pending.md` are ticked with that file's name.
