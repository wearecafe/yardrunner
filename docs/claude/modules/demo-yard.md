# Demo yard

`Database\Seeders\DemoYardSeeder` builds Escarpment Stone & Landscape
Supply with data for every module, dated relative to today.
`yardrunner:demo:reset` rebuilds it nightly. When adding a tenant table, add
it to `ResetDemoYard::TABLES` (children before parents).
