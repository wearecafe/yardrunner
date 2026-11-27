# Bunny Fonts

IBM Plex Sans (400–700) and IBM Plex Mono (500) come from Bunny Fonts through
`laravel-vite-plugin/fonts` (`vite.config.ts`). The plugin downloads the
font files at build time and emits them with the other built assets, and
`@fonts` in `app.blade.php` adds the preload links. Browsers never contact
Bunny, so there's no runtime request, cookie or account to manage.

The build machine needs to reach `fonts.bunny.net`. To change a family or
weight, edit `vite.config.ts` and the `--font-*` tokens in
`resources/css/app.css` together (see [design-system.md](design-system.md)).
