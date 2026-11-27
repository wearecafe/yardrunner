# Setup for new businesses

What a business fills in after signing up (or after staff set it up in
the back office). Scope: [tenant-setup.md](../tenant-setup.md).
Code: `App\Onboarding\Setup`, `SetupController`, `EnsureSetUp`,
`pages/setup/*`, `layouts/setup-layout.tsx`.

| Step                  | Required | Saves                                                                    |
| --------------------- | -------- | ------------------------------------------------------------------------ |
| Your business         | Yes      | Name, time zone, reply-to email, tax numbers (`branding.tax_numbers`)    |
| Your yards            | Yes      | Each yard with a full address; sets `tenants.onboarded_at`               |
| How you work          | No       | Delivers / sells on account (`settings.onboarding.answers`), the add-ons |
| Your products         | No       | Nothing: links to the product editor, or emails support the price list   |
| Your team             | No       | Invites, through `UserAccounts::create` (not drivers or time-only)       |
| Logo and colour       | No       | Logo (the branding upload) and primary colour                            |
| Card payments and tax | No       | Nothing: shows the payments status and links to Settings → Payments      |

- **The gate.** `EnsureSetUp` sends owners and admins of a business with
  no `onboarded_at` to their step in Setup from every screen except their
  account, billing, help and sign-out. Nobody else is held back, and an
  unverified email is dealt with first. Existing businesses and the demo
  yard were marked set up by the migration; the factory makes tenants set
  up (`notSetUp()` for one that isn't).
- **Progress** is in `tenants.settings['onboarding']`: steps saved
  (`completed`), steps moved past (`visited`), the answers, the last step
  opened (where `/setup` resumes), `finished_at` and
  `checklist_hidden_at`. Whether products, team, logo and payments are
  done is worked out from the data each time.
- **Checklist.** Today shows owners and admins a "Finish setting up" card
  (`Setup::checklist`) until everything on it is done or they hide it:
  products, team, logo, card payments, sending domain, plus trucks,
  contractor accounts and the rental fleet when they apply, and payment
  details for YardRunner during the trial. Never in the demo yard.
- **Back office.** The tenant page says when the business was set up or
  which step it's on; Needs attention lists businesses that signed up two
  days ago or more and haven't finished the required steps.
