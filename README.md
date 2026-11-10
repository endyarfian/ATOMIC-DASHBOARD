# A.T.O.M. Dashboard

Laravel-based, read-only monitoring dashboard for the A.T.O.M. biometric access system.

## Modules

- **Overview** — device heartbeat state, operator totals, access decisions, session health, seven-day activity, and recent MQTT processing.
- **Devices** — device status derived from the latest heartbeat and device access history.
- **Operators** — permanent operator identity directory, including inactive identities retained for audit.
- **Access Logs** — immutable access-event snapshots and decision history.
- **Sessions** — session lifecycle with separate active, completed, interrupted, and orphaned classifications.

## Option B operator identity

The dashboard follows the permanent identity model used by the subscriber:

- `operator_db_id` is the permanent database identity and primary key.
- `device_operator_id` is the firmware/device identifier and may be reused after deletion.
- Historical access logs and sessions are always associated with `operator_db_id`.
- Access-log display prioritizes the `operator_name` snapshot stored on the event. It must not replace historical names with the current operator directory name.
- Inactive operator rows remain visible so their audit history can still be inspected.

## Orphaned sessions

A source session is shown as **orphaned** when its source status remains `active` but its permanent operator identity is inactive or missing. This is a derived dashboard classification only.

The dashboard does not close, delete, or rewrite source sessions. In particular, deleting an operator on the device cannot generate a genuine `SESSION_ENDED` event for a session that can no longer be ended by that operator. The original source row therefore remains unchanged and is clearly labelled as orphaned.

Heartbeat interruption detection is stored in the dashboard's own database (`session_interruptions`) and never writes to `atomic_access`.

## Database separation

Two database connections are used:

- `mysql` — dashboard's own application database (`atomic_dashboard`).
- `atomic_access` — source database, configured with a SELECT-only database account.

Do not run dashboard writes, migrations, seeders, or administrative actions against `atomic_access`. Dashboard-local migrations such as `session_interruptions` belong only to the `mysql` connection.

Example environment configuration:

```dotenv
DB_CONNECTION=mysql
DB_DATABASE=atomic_dashboard

ATOMIC_ACCESS_DB_HOST=127.0.0.1
ATOMIC_ACCESS_DB_PORT=3306
ATOMIC_ACCESS_DB_DATABASE=atomic_access
ATOMIC_ACCESS_DB_USERNAME=atomic_dashboard_reader
ATOMIC_ACCESS_DB_PASSWORD=change-this
APP_TIMEZONE=Asia/Jakarta
```

Use the actual credentials provisioned on the server; never commit `.env` or production secrets.

## Deployment

After reviewing and merging the dashboard changes:

```bash
cd /mnt/hdd-ext4/apps/atomic-dashboard
git status --short
git pull --ff-only origin main
php artisan optimize:clear
```

Only run migrations if a dashboard-local migration is explicitly added and reviewed. Never run `migrate:fresh`, `db:wipe`, or destructive commands against either production database.

## Access

All monitoring routes are protected by the `atom.access` middleware. The dashboard is intended for internal access and read-only operational monitoring.
