# Time and attendance

An add-on, turned on under Settings → Time (`TimeSettings`, kept in
`tenants.settings['time']`). Turning it on sets up an `Employee` for
everyone who signs in. Rules live in `app/Time`.

Every employee is a user (`employees.user_id` is required, one per
user); not every user is an employee. An employee's name is their
user's (`Employee::name`), and YardRunner keeps no job titles: roles say
what someone does. Team → Employees picks a user who isn't set up yet or
adds a new one (name, email, roles, yards) in the same form; people
without Manage users and roles can only add Time & attendance only
users there. The home yard has to be one of the user's yards. Yard hands
who only punch at the counter tablet hold the `time_only` role with no
email. "End employment" archives the employee and leaves the user;
deactivating the user archives the employee too.

| Feature                                                                   | Screens                    | Code                                 |
| ------------------------------------------------------------------------- | -------------------------- | ------------------------------------ |
| Punches from the counter tablet (PIN, optional photo), phones, driver app | Team → Time clock, My time | `TimeClock`, `KioskController`       |
| Phone punches checked against each yard; take-home trucks excused         | Settings → Time            | `Geofence`                           |
| Punches queued on the phone with no signal, sent with their time          | My time                    | `time-clock-card.tsx`, `device_at`   |
| Shifts, unpaid breaks, rounding, weekly overtime (ON 44 h, QC 40 h)       | Timesheets                 | `TimesheetCalculator`                |
| Exceptions: missed clock-out, long shift, no meal, short rest, away…      | Who's in, Timesheets       | `TimesheetCalculator::EXCEPTIONS`    |
| Corrections with a reason; originals kept; employee emailed, can dispute  | Timesheet, My time         | `PunchCorrections`, `TimeAdjustment` |
| Pay periods (weekly, biweekly, semi-monthly), approval, lock              | Timesheets                 | `PayPeriods`, `Timesheets`           |
| Payroll: hours summary CSV, ADP import, QuickBooks time activities        | Timesheets                 | `PayrollExporter`                    |

Punches (`time_punches`) are never edited. A correction writes a
`time_adjustments` row, marks the punch `superseded_at`, and for an add
or change writes a new punch with `source = manager`. Hours are worked
out from the effective punches every time; approving a timesheet
freezes its totals, and any later punch or correction in an open period
sends it back. A locked or exported period refuses changes; late fixes
are hours adjustments in the current period.

A shift belongs to the day it starts. A shift left open for 16 hours
(`TimeClock::STALE_HOURS`) is a missed clock-out: the next clock-in
starts a new shift and the old one counts nothing until a manager fixes
it. Overtime counts over the workweek, so a period that starts mid-week
counts the days before it.

Drivers reach their own time (`time.self.*`) inside the driver app;
`manage-time` (owners and admins) covers Who's in, Timesheets,
Employees and exports. Pay rates aren't stored.
