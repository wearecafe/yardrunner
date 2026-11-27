# Twilio

Text messages go through Twilio: delivery ETAs, rental signing links and
rental reminders. With `YARDRUNNER_SMS_DRIVER=log` (the default locally)
they're written to the log instead.

## Environment

```
YARDRUNNER_SMS_DRIVER=twilio  # or log
TWILIO_SID=...
TWILIO_TOKEN=...
TWILIO_FROM=+1...
```

## Known gaps

Every yard's texts go from the one `TWILIO_FROM` number, replies aren't
received, delivery status isn't logged and STOP opt-outs aren't handled.
Texts are also a channel with consent and opt-out rules (CASL in Canada);
how strictly to handle them is Jon's call when the per-yard texting work is
shaped. See "Texting that works per yard" in [gap-list.md](gap-list.md).
