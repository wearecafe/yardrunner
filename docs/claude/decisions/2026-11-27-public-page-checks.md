# How the public-page test checks forms and links

Status: proposed
Date: 2026-11-27
Area: Marketing pages
Decided by: Claude, at build

**Decision:** Because the marketing pages are Inertia (React) pages, `MarketingPagesTest` checks their source files (`welcome.tsx`, `pages/help`, `components/help`) for anything that posts (`useForm`, `<Form`, `router.post|put|patch|delete`, `method="post"`), rather than the rendered HTML. Every help category and article must load cookieless and cacheable. Every internal link in help articles and in those sources must answer 200, or 302 for app pages that send guests to sign in. `/demo` is left to `DemoYardTest`, since it only exists where the demo is on.

**Source:** `docs/claude/stack-rules.md` phase 2; `decisions/2026-11-27-marketing-pages-stay-inertia.md`.
