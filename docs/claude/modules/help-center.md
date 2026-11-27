# Help center

The public help center is at `/help`, for yard staff and their customers.
Articles are Markdown files in `resources/help/{category}/{slug}.md`, with
`title`, `summary`, `order`, and optional `popular` and `related` front
matter. Categories are listed in `App\Help\HelpCenter::CATEGORIES`. Link
between articles with `/help/{category}/{slug}`; `HelpCenterTest` fails on
a broken link. A blockquote starting with **Tip**, **Note** or **Heads up**
renders as a callout. When a screen or rule changes, update its article.
Set `YARDRUNNER_SUPPORT_EMAIL` to show contact links.

The help center and the home page are marketing pages: they set no
cookies and are served from the edge cache, so they look the same to
everyone (see [laravel-cloud.md](../laravel-cloud.md#edge-cache)).
