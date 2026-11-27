# YardRunner product docs

Scope, decisions, access, security notes, specs and UAT rows. **This folder is the master copy.** Start with `CLAUDE.md` at the repo root, then `decisions-index.md`.

| Path                    | Holds                                                                                                  |
| ----------------------- | ------------------------------------------------------------------------------------------------------ |
| `decisions/`            | One file per decision, with a status. `pending.md` holds older choices still waiting for confirmation. |
| `decisions-index.md`    | Every phase or feature doc, its status, whether it changes access, and its UAT rows.                   |
| `modules/`              | How each module is built now: classes, tables, jobs, tests, demo data. Linked from `CLAUDE.md`.        |
| `permissions.md`        | Roles, access rules, sign-in rules, and the checklist for new features.                                |
| `security.md`           | How tenant isolation works, rules for new code, guest routes, automated checks, known gaps.            |
| `demo-and-uat.md`       | Demo personas, facts that affect testing, UAT row ID codes, foundation UAT rows.                       |
| `delivery-checklist.md` | Build order and pre-merge checks for this stack.                                                       |
| `design-system.md`      | Tokens, components and patterns.                                                                       |
| `gap-list.md`           | What's half-built or missing, prioritised, and the "Not built yet" table by module.                    |
| `<service>.md`          | One per outside service (below).                                                                       |
| `<phase or feature>.md` | One per phase or feature: scope, access changes, security notes, UAT rows, links to its decisions.     |

Each PR updates its own docs in the same PR (`CLAUDE.md` → Workflow).

## Outside services

Each outside service has its own file, with what it does, the code behind
it, its environment variables and how to set it up:

| Service                                   | File                                   |
| ----------------------------------------- | -------------------------------------- |
| Stripe: Connect, Stripe Tax and Billing   | [stripe.md](stripe.md)                 |
| QuickBooks Online                         | [quickbooks.md](quickbooks.md)         |
| Resend: email and yards' sending domains  | [resend.md](resend.md)                 |
| Cloudflare DNS for sending domains        | [cloudflare-dns.md](cloudflare-dns.md) |
| Twilio: text messages                     | [twilio.md](twilio.md)                 |
| Web push: staff alerts                    | [web-push.md](web-push.md)             |
| File storage (S3 bucket)                  | [file-storage.md](file-storage.md)     |
| Bunny Fonts (build time only)             | [bunny-fonts.md](bunny-fonts.md)       |
| Laravel Cloud: hosting and the edge cache | [laravel-cloud.md](laravel-cloud.md)   |
