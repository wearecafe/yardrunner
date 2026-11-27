# Cloudflare DNS

YardRunner uses Cloudflare's API for one thing: publishing the records that
let yards send email from their own domain without seeing the email
service. For each yard, the real DKIM and bounce records from Resend are
written to YardRunner's zone under names like `dkim-42.dns.yardrunner.ca`
and `bounce-42.dns.yardrunner.ca`; the yard adds CNAMEs pointing at them.
See [resend.md](resend.md) and [email-branding.md](email-branding.md).

(Laravel Cloud's own edge network also runs on Cloudflare; that's covered in
[laravel-cloud.md](laravel-cloud.md) and needs no setup here.)

## Code

`yardrunner.email.dns_driver` picks the driver: `cloudflare` in production,
`local` for development and the demo yard. Tests answer DNS lookups with
`DnsLookup::fake()`.

## Environment

```
YARDRUNNER_EMAIL_DNS_DRIVER=cloudflare
YARDRUNNER_EMAIL_DNS_ZONE=dns.yardrunner.ca
YARDRUNNER_DNS_CLOUDFLARE_TOKEN=...   # DNS edit permission on the yardrunner.ca zone only
YARDRUNNER_DNS_CLOUDFLARE_ZONE=...    # zone ID for yardrunner.ca
```

Keep the token scoped to DNS edit on that one zone, and note its expiry
somewhere it will be seen.
