# YardRunner Time and Attendance

Oct 26, 2026 · @Jon Moore

> Moved here from the claude.ai doc "Yardline Time and Attendance" on Nov 27, 2026. Employees are users and billing is per tracked person (see [decisions-index.md](decisions-index.md)); what's built is in [features.md](features.md#time-and-attendance). The pay-rule defaults below are legal rules to verify before launch.

## Overview

YardRunner adds time and attendance so every employee clocks in inside the same app they already work in, and approved hours go to payroll without a paper timesheet. Because YardRunner already knows about deliveries, pick lists and rental maintenance, it can also show where the hours went, not just how many there were.

**Who it covers:** drivers, yard staff (pickers, loaders, equipment operators), counter staff, rental and maintenance staff, and office staff.

**Goals**

- Accurate, tamper-resistant punches from the counter, the yard and the truck
- Overtime, meal breaks and daily limits applied per province (Ontario and Quebec first)
- Manager review that surfaces only the exceptions, not every punch
- One-click export of approved hours to the payroll system
- Labour cost per delivery, per order and per rental unit, using hours YardRunner already captures

**Non-goals for v1**

- Running payroll, deductions, T4s or RL-1s (stays in the payroll provider)
- Auto-scheduling or demand forecasting
- Biometric (fingerprint or face match) clocking

**Pricing:** a paid add-on, priced per user.

## How people clock in

Every employee is a YardRunner user with a 4 to 6 digit PIN, and can punch from whichever device fits their job. All methods write the same punch record, tagged with its source.

| Method            | Who uses it                                    | How it works                                                                | Anti-cheat                                                                          |
| ----------------- | ---------------------------------------------- | --------------------------------------------------------------------------- | ----------------------------------------------------------------------------------- |
| Counter kiosk     | Counter and yard staff without a company phone | Shared tablet at the counter or yard shack; tap name, enter PIN             | Photo on punch, off by default; tenant turns it on for everyone or a selected group |
| YardRunner mobile | Pickers, loaders, rental staff, managers       | Clock button on the existing yard picker view                               | GPS checked against the yard geofence                                               |
| Driver app        | Drivers                                        | Clock in starts the shift, then pick a truck and do the pre-trip inspection | GPS at punch; delivery events add timestamps through the day                        |
| Manager entry     | Anyone who forgot or had no device             | Manager adds or fixes a punch                                               | Reason required; employee is notified and can dispute                               |

**Rules for punches**

- A punch outside the yard geofence is allowed but flagged for review, since drivers and site staff legitimately punch from job sites. Drivers set up with a take-home truck can start and end their shift away from the yard without a flag.
- Punches made offline are queued with the device time and synced later; a large gap between device and server time gets flagged.
- Location is captured only at punch time and, for drivers, at delivery events. No continuous tracking while clocked out.
- Ontario employers with 25 or more employees need a written electronic monitoring policy, so YardRunner should ship a template policy that describes what it collects.

## Data model

Punches are the raw, never-edited record; everything else (shifts, timesheets, overtime) is calculated from them, so a correction never overwrites what actually happened. Every table carries `tenant_id`, matching the rest of YardRunner.

| Entity           | Key fields                                                                                                                                                                    | Notes                                                                   |
| ---------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------------------------------------------------------------------- |
| Employee         | User, home yard, work province, role, pay type (hourly or salaried), tracks time (on or off, set per person), take-home truck, payroll employee ID, PIN, overtime-exempt flag | Links to the existing user; payroll ID maps hours to the payroll system |
| Punch            | Employee, type (in, out, break start, break end), timestamp, source (kiosk, mobile, driver app, manager), device, location and accuracy, photo, geofence result               | Immutable. Corrections are separate adjustment records                  |
| Punch adjustment | Original punch (or none for a missed punch), new time, reason, made by, employee acknowledged                                                                                 | Full audit trail                                                        |
| Time entry       | Employee, start, end, activity code, linked work object (order, delivery, rental unit, maintenance job), yard                                                                 | A shift is split into entries when the activity changes                 |
| Activity code    | Name (picking, loading, driving, delivery, counter, rental prep, maintenance, office), paid or unpaid, billable flag                                                          | Configurable per tenant                                                 |
| Schedule         | Employee, date, planned start and end, yard                                                                                                                                   | Optional; used for late and no-show alerts                              |
| Pay period       | Frequency (weekly, biweekly, semi-monthly), start date, status (open, locked, exported)                                                                                       | One per tenant, overridable per yard                                    |
| Timesheet        | Employee, pay period, regular hours, overtime hours, breaks, exceptions, status (draft, submitted, approved, exported)                                                        | Recalculated until approved, frozen once exported                       |
| Pay rule set     | One per tenant, applied to every employee: province defaults, weekly overtime threshold, daily threshold, meal break rules, rounding                                          | Seeded for Ontario and Quebec                                           |
| Time-off request | Employee, dates, type (vacation, sick, unpaid), status                                                                                                                        | Phase 2                                                                 |

Pay rates are not stored for now, which keeps sensitive pay data in the payroll system. Labour cost reports need them, so they can be added later as an optional field restricted to owners.

## Drivers

A driver's day is split into time entries automatically from dispatch events, so the driver only clocks in and out and the rest comes from work they already record in the driver app.

| Event in the driver app       | Time entry it starts                         |
| ----------------------------- | -------------------------------------------- |
| Clock in, truck selected      | Pre-trip inspection                          |
| Inspection submitted          | Loading (yard)                               |
| Load confirmed, departed yard | Driving, linked to the delivery route        |
| Arrived at site               | On-site delivery, linked to the order        |
| Proof of delivery signed      | Driving (next stop or return)                |
| Arrived back at yard          | Yard work, until the next route or clock out |

**What this enables**

- Actual time per delivery, to check whether zone, distance or truckload delivery fees cover the labour
- Late, long or skipped stops visible on the dispatch board
- A daily record of on-duty hours per driver and truck

**Regulatory notes (confirm with a transport compliance advisor)**

- Ontario commercial vehicles over 4,500 kg need a daily trip inspection; the pre-trip step should produce that record and block departure if a major defect is reported.
- Drivers who stay within a 160 km radius and return to the yard each day are generally exempt from the daily log, but the operator still has to keep records of their on-duty hours. YardRunner's time entries can be that record.
- Daily driving and on-duty limits should be configurable, with a warning as a driver nears them. Full electronic logging device (ELD) support is out of scope unless yards run long-haul or cross-border routes.

## Pay rules and exceptions

Each tenant uses one rule set for every employee, seeded from its province's defaults and recalculated whenever a punch changes, so the timesheet always shows current overtime. The defaults below are from general knowledge of the Ontario ESA and Quebec's Act respecting labour standards, not a fresh lookup; verify them before launch.

| Rule                             | Ontario default                                              | Quebec default                                 |
| -------------------------------- | ------------------------------------------------------------ | ---------------------------------------------- |
| Weekly overtime threshold        | 44 hours, at 1.5x                                            | 40 hours, at 1.5x                              |
| Meal break                       | 30 minutes, no more than 5 hours in a row without one        | 30 minutes after 5 hours in a row              |
| Daily rest                       | 11 hours off in a row each day                               | Tracked, not enforced                          |
| Minimum pay when sent home early | 3 hours (for regular employees who usually work more than 3) | 3 hours (when reporting to work and sent home) |
| Overtime averaging               | Supported when the tenant has an averaging agreement on file | Supported where permitted                      |

**Other settings per tenant**

- Rounding: off by default; if turned on, it must round both ways evenly (for example, to the nearest 5 minutes) so it never shortchanges staff
- Auto meal deduction: off by default; if on, the employee confirms on clock-out that they took it
- Salaried staff are set per person to clock in or not; overtime-exempt roles track attendance only

**Exceptions surfaced for review**

- Missed punch (clocked in yesterday with no clock out)
- Shift over 12 hours, or no meal break after 5 hours
- Punch outside the geofence, or with a large device-time gap
- Late arrival or no-show against the schedule
- Approaching weekly overtime (alert to the manager mid-week, not after the fact)
- Driver near the daily on-duty limit

## Approvals and payroll

Nothing reaches payroll until a manager approves it and the pay period is locked; after that, approved hours leave YardRunner once per period as a direct push or a file.

Punch to payroll: employees punch → YardRunner works out hours, overtime and exceptions → managers clear exceptions → employees submit → managers approve → the period is locked and exported. (The original doc showed this as a diagram.)

Managers clear exceptions daily, so the end-of-period review is a quick check rather than a rebuild of the week.

- **Edits:** fixing or adding a punch needs a reason, notifies the employee, and is kept in the audit log next to the original punch.
- **Export:** push hours to QuickBooks Online as time activities per employee, with activity codes mapped to service items (confirm QuickBooks Payroll in Canada picks these up), an ADP import file, or an hours-per-period summary for any other payroll provider or accountant.
- **After export:** an exported period is never edited; a late correction becomes an adjustment in the next open period.

| Role                   | Can do                                                                          |
| ---------------------- | ------------------------------------------------------------------------------- |
| Employee               | Punch, see and submit own timesheet, dispute edits                              |
| Yard manager           | See who's in, fix punches and approve timesheets for their yard                 |
| Owner or payroll admin | Everything above for all yards, lock periods, export, change rules and settings |

## Screens to mock up

Six screens, ordered by how often they're used, matching the first mockup pass for the yard and rental modules.

1. **Clock kiosk (counter tablet):** staff tiles with who's in, PIN pad, photo capture, break and clock-out buttons, today's hours on confirmation
2. **Mobile clock (yard and driver):** big clock button, current activity, switch activity, breaks; for drivers, truck selection and pre-trip inspection before the shift starts
3. **Who's in board (manager):** everyone on shift by yard with status (working, on break, driving, on site), late and missing people, live overtime warnings
4. **Timesheet review (manager):** pay period grid by employee, exceptions first, fix punches with a reason, approve one or all
5. **My timesheet (employee):** daily punches and hours, overtime so far this week, dispute a manager edit, submit for approval
6. **Settings and payroll export:** pay periods, province rules, geofences per yard, activity codes, payroll mapping, export history

## Phasing and decisions

Ship punches, timesheets and payroll export first; driver auto-tracking and labour costing come once dispatch and the driver app are live.

| Phase | Scope                                                                                                                                                                           |
| ----- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| 1     | Employees and PINs, kiosk and mobile clock, geofence flags, Ontario and Quebec rules, timesheet review and approval, QuickBooks Online and ADP export, hours-per-period summary |
| 2     | Driver auto-segmenting from dispatch events, pre-trip inspection, activity codes linked to orders and rental units, schedules, time-off requests                                |
| 3     | Labour hours per delivery, order and rental unit; on-duty limit warnings; direct API integrations with more payroll providers                                                   |

**Decisions**

| Question                     | Decision                                                                               |
| ---------------------------- | -------------------------------------------------------------------------------------- |
| Payroll systems              | Plan for QuickBooks Online and ADP, plus an hours-per-period summary for everyone else |
| Take-home trucks             | Sometimes; drivers with a take-home truck can clock in away from the yard              |
| Salaried staff               | Some clock in, so it's set per employee                                                |
| Kiosk photo                  | Off by default; a tenant can turn it on for everyone or a selected group               |
| Union or role-based overtime | None; one set of overtime rules across the whole tenant                                |
| Pay rates in YardRunner      | Not yet                                                                                |
| Pricing                      | Paid add-on, priced per user                                                           |
