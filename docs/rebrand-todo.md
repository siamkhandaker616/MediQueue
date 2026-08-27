# Rebrand TODO (patient-side views)

Follow-up work for whenever the patient area gets its full design pass.
Do this AFTER the patient teammate's features stabilise — she is expected to
push more patient-side changes, so rebranding early would just get clobbered.

## Scope

Her three booking views currently use `layouts.app` + a teal/gray Tailwind
palette instead of the project design language:

- [ ] `resources/views/appointments/create.blade.php` — rebuild on the project
      layout components (`x-layouts.*`, brand color tokens, existing button /
      card / badge classes) and keep the wizard behaviour intact.
- [ ] `resources/views/appointments/show.blade.php` — same treatment; token
      card should use brand palette, not teal.
- [ ] Any future patient views she adds.

## Icon strategy

- The `departments.icon` column stores Font Awesome classes
  (`fa-solid fa-stethoscope`, ...) but Font Awesome is NOT installed in this
  project, so those icons render blank.
- As a stopgap, the booking wizard renders a generic inline plus-circle SVG
  per department card. During the rebrand, decide properly:
  - either add a maintained icon set and map `$dept->icon` values to it,
  - or replace the column with an SVG-name/icon-key convention used by the
    rest of the app.

## Known bug to hand back (not ours)

- `AppointmentController::index()` returns `view('appointments.index')`,
  but that file does not exist. Visiting `/appointments` manually throws a
  500. Nothing in the navbar links to it, so it is dormant — she needs to
  create the view (or we will during the rebrand if still missing).

## Feature follow-up: guest booking

- Booking currently requires a logged-in verified patient account
  (`auth` + `verified` + `role:patient` on the appointment routes).
- For convenience we may want **guests** to be able to book too — the real
  restriction should only be that doctors/admins can't use patient booking.
- If/when adding this:
  - capture name + email (+ phone?) in the wizard and create/find a User
    record at store-time (the whole notification stack — FR-17 reminders,
    FR-18 queue alerts — delivers via the User model, so a real record is
    still needed),
  - decide how a guest re-accesses their token card (magic link / login
    prompt on the show route),
  - keep doctors/admins blocked via `role` checks either way.

