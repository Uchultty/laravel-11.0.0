# Backup & Restore Plan for Inventory Database

## Goal
Mencegah kehilangan data saat refactor database dan memastikan ada jalur restore yang jelas jika test atau migrasi salah target database.

## Important Finding
Current `phpunit.xml` sets `APP_ENV=testing` but does **not** explicitly set a separate test database. The SQLite test connection is commented out. That means feature tests with `RefreshDatabase` can impact the default database if environment variables are not isolated.

## Priority Order
1. Isolate test database from production/dev database.
2. Create a full backup before any refactor migration.
3. Verify restore procedure on a non-production copy.
4. Only then continue refactor/backfill work.

## A. Backup Strategy

### 1) Full PostgreSQL backup
Use `pg_dump` before any schema or data migration.

```powershell
$env:PGPASSWORD='94526345'
pg_dump -h 127.0.0.1 -U postgres -d pergudangan -F c -f backups/pergudangan-$(Get-Date -Format 'yyyyMMdd-HHmmss').dump
```

Recommended backup types:
- `-F c` custom format for restore flexibility.
- Optional plain SQL backup for quick inspection.

```powershell
$env:PGPASSWORD='94526345'
pg_dump -h 127.0.0.1 -U postgres -d pergudangan -F p -f backups/pergudangan-$(Get-Date -Format 'yyyyMMdd-HHmmss').sql
```

### 2) Backup scope
Include:
- schema
- data
- sequences
- constraints
- indexes
- legacy tables and new modular tables

### 3) Verification after backup
Check backup file exists and is non-zero size.

```powershell
Get-ChildItem backups
```

## B. Restore Strategy

### 1) Restore to a fresh database first
Do not restore directly into the active database until verified.

```powershell
$env:PGPASSWORD='94526345'
createdb -h 127.0.0.1 -U postgres pergudangan_restore_test
pg_restore -h 127.0.0.1 -U postgres -d pergudangan_restore_test backups/pergudangan-YYYYMMDD-HHMMSS.dump
```

For plain SQL backup:

```powershell
$env:PGPASSWORD='94526345'
psql -h 127.0.0.1 -U postgres -d pergudangan_restore_test -f backups/pergudangan-YYYYMMDD-HHMMSS.sql
```

### 2) Restore validation checklist
After restore, verify:
- table count matches
- row count matches
- PK/FK intact
- sample records readable
- documents paths still present

Example checks:
```sql
select count(*) from barang;
select count(*) from barang_masuk;
select count(*) from barang_dalam_proses;
select count(*) from barang_keluar;
```

### 3) Promote restore only after validation
If restore passes on the test copy, then and only then consider production restore.

## C. Test Database Isolation

### Recommended fix
Create a separate test database or enable in-memory SQLite for tests.

Option 1: PostgreSQL test database
- `DB_CONNECTION=pgsql`
- `DB_DATABASE=pergudangan_testing`

Option 2: SQLite for tests
- set `DB_CONNECTION=sqlite`
- set `DB_DATABASE=:memory:` or `database/testing.sqlite`

### Why this matters
`RefreshDatabase` can rebuild the schema on the active connection. If the test environment points to the main DB, it can wipe the current data.

## D. Recovery Playbook If Data Is Lost

### Case 1: Schema dropped or truncated accidentally
1. Stop all write activity.
2. Restore latest backup to a fresh database.
3. Compare row counts.
4. Swap application config to restored database only after verification.

### Case 2: Test accidentally targeted the main database
1. Stop tests immediately.
2. Restore from latest backup.
3. Apply only the intended migrations to a dedicated test DB.
4. Add isolation config before rerunning tests.

## E. Operational Rules Before Refactor
- Never run `RefreshDatabase` tests against the active database.
- Never run destructive migrations without a backup.
- Keep legacy tables until modular schema is validated.
- Run backfill only after backup is confirmed.

## F. Minimum Safeguards to Implement Next
1. Add `.env.testing` or explicit test DB config.
2. Add a helper test command that prints active DB name before tests.
3. Automate pre-migration backup command.
4. Keep restore instructions in repository docs.

## G. Recommended Next Step
Implement test isolation first, then proceed with the refactor/backfill flow.