# Frontend tests run on Vite+'s Vitest, with jsdom

Status: proposed
Date: 2026-11-27
Area: Stack
Decided by: Claude, at build

**Decision:** `npm test` runs `vp test run`: the Vitest that ships with Vite+, configured in `vite.config.ts` (`test`), with jsdom and React Testing Library. Tests import from `vite-plus/test` and sit beside the code as `*.test.ts(x)`; `resources/js/test/setup.ts` adds the jest-dom matchers. Under Vitest the config loads only the React plugin and sets the `@` alias itself, so tests need no PHP, no font download and no CSS build. CI runs them in the `tests` workflow. The first tests cover the edge-cache path list and the order and payment badges.

**Turned down:** A separate `vitest` dependency and config file (Vite+ already ships Vitest); happy-dom.

**Source:** `docs/claude/stack-rules.md` phase 3; framework stack rule "Commands".
