# File storage

Signed rental agreements, proof-of-delivery photos, inspection photos and
customer documents are stored on `YARDRUNNER_FILES_DISK` (`App\Support\Files`).
Locally that's the private `local` disk. In production, attach a bucket in
Laravel Cloud (S3-compatible, through `league/flysystem-aws-s3-v3`) so files
survive deploys.

Files are private: they're served only through `FileController`
(`/files/{path}`), which checks access, with `Cache-Control: private`. The
one public file is each yard's email logo at `/brand/{slug}/logo`, because
email apps don't sign in.

## Environment

```
YARDRUNNER_FILES_DISK=s3
AWS_ACCESS_KEY_ID=...        # set by Laravel Cloud when a bucket is attached
AWS_SECRET_ACCESS_KEY=...
AWS_DEFAULT_REGION=...
AWS_BUCKET=...
```
