# Design system

How YardRunner looks, and the rules that keep every part of it looking like
the same software. Read this before building or restyling a screen, and
update it when you add a token, a component or a pattern.

## Principles

- **One system.** Use the tokens and components below. A screen that needs
  something new adds it here first, so the next screen can reuse it.
- **Built for the jobs.** Every element serves a job someone does in the
  yard, at the counter, on a truck or in the office. Nothing is added for
  decoration.
- **Made with care.** Clear hierarchy, generous spacing, figures that line
  up, states that read at a glance. It should be pleasant to use all day.
- **Real conditions.** Yard tablets in sunlight, gloves, weak Wi-Fi and
  phones on a truck: large tap targets (44 px), high contrast, nothing that
  depends on colour alone, and pages that still make sense on a phone.

## Foundations

Tokens live in `resources/css/app.css` (`:root` and `.dark`) and are used
through Tailwind's theme names (`bg-card`, `text-muted-foreground`,
`border-border`…). Never hard-code a hex value in a component.

| Role                                               | Light                 | Dark                  | Use                                               |
| -------------------------------------------------- | --------------------- | --------------------- | ------------------------------------------------- |
| `background`                                       | `#f5f3ef`             | `#141210`             | Page, warm stone                                  |
| `card`                                             | `#ffffff`             | `#1c1917`             | Cards, tables, forms                              |
| `foreground`                                       | `#1c1917`             | `#f5f5f4`             | Text                                              |
| `muted-foreground`                                 | `#57534e`             | `#a8a29e`             | Secondary text, labels                            |
| `subtle`                                           | `#44403c`             | `#d6d3d1`             | Text that sits between foreground and muted       |
| `primary`, `ring`                                  | `#c2410c`             | `#ea580c`             | Burnt orange: the one main action per view, focus |
| `accent-soft`                                      | `#fff7ed`             | `#431407`             | Highlighted rows and selected states              |
| `border` / `input`                                 | `#e7e5e4` / `#d6d3d1` | `#292524` / `#44403c` | Dividers / field outlines                         |
| `success`, `info`, `warning`, `danger` (+ `-soft`) |                       |                       | Status chips and banners                          |
| `sidebar*`                                         | `#1c1917`             | `#0c0a09`             | Dark stone sidebar in both modes                  |
| `chart-1…5`                                        |                       |                       | Charts, in that order                             |

- **Type:** IBM Plex Sans for everything, IBM Plex Mono (500) for figures:
  money, quantities, codes and times (see [bunny-fonts.md](bunny-fonts.md)).
  Page titles are 28 px bold (`PageHeader`).
- **Radius:** `--radius` 0.625 rem, with `sm`/`md`/`lg`/`xl` derived from it.
- **Light and dark:** every screen works in both; the person's choice is the
  `appearance` cookie (light, dark or system).

## Components

- **shadcn primitives** in `resources/js/components/ui` (button, card,
  dialog, select, sheet, sidebar, tooltip, toggle-group…). Add new ones with
  the shadcn CLI (`components.json`) and restyle through tokens, not
  per-use classes.
- **YardRunner components** in `resources/js/components/yard`, built on the
  primitives. Reuse before writing new ones:

| Component                                                          | For                                              |
| ------------------------------------------------------------------ | ------------------------------------------------ |
| `PageHeader`                                                       | Every page: eyebrow, title, description, actions |
| `Chip`, `StatusBadge` (order, payment)                             | Statuses, one tone per meaning                   |
| `DataTable`, `Pagination`, `SearchBox`                             | Lists                                            |
| `Field`, `Combobox`, `ToggleButtons`, `Tabs`                       | Forms and switching views                        |
| `PeoplePickers`, `CustomerForm`, `OrganizationForm`, `ProductForm` | Shared record forms                              |
| `RefundApproval`, `RoundingPrompt`                                 | On-the-spot approvals and confirmations          |
| `SignaturePad`, `PrintBar`                                         | Signing and printable pages                      |
| `TimeClockCard`, `MyTime`                                          | Time and attendance                              |

- **Layouts** in `resources/js/layouts`: the app (sidebar), driver (phone),
  setup (full screen, no sidebar), auth and settings. The contractor portal
  uses `PortalLayout`; public pages (home, help, quote and booking pages)
  have their own headers but the same tokens.

## Patterns

- One primary (orange) action per view; everything else is secondary or a
  link.
- Status always has a word, not just a colour.
- Money and quantities right-aligned in tables, in Plex Mono, with units
  ("3 pallets", "$1,240.00").
- Hide what a person can't use, and say why when hiding would confuse
  (see [permissions.md](permissions.md)).
- Empty states say what the list is for and offer the action that fills it.
- Customer-facing emails, PDFs and pages use the yard's brand through the
  colour system in [email-branding.md](email-branding.md), not the app's
  orange.

## Visual references

Screen mockups are in [mockups](mockups). Visual explorations made on
claude.ai (the email designs, the logo, the P&L and back-office mockups)
are references only; anything they decided is written down here or in the
area's doc.
