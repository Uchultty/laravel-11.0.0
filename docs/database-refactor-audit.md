# Database Refactor Audit

## Current Safe Baseline
- Test isolation is now separated from the main PostgreSQL database.
- `RoleAccessTest` passes against `pergudangan_test`.
- Main database `pergudangan` is not touched by test runs.

## Blocking Findings

### 1. Migration layer still contains PostgreSQL-specific raw SQL
These migrations use `DB::statement()` with direct `ALTER TABLE`, `DROP CONSTRAINT`, `USING`, `REGEXP_REPLACE`, and cast syntax that is not portable to SQLite:
- `database/migrations/2026_04_16_000002_change_primary_keys_on_users_suppliers_customers.php`
- `database/migrations/2026_04_16_000003_convert_barang_ids_to_brg_format.php`
- `database/migrations/2026_04_21_000006_rename_foreign_keys_to_match_primary_keys.php`

Implication:
- The project must keep a PostgreSQL test database for now.
- Any future schema work should avoid introducing more dialect-specific SQL unless it is intentionally PostgreSQL-only.

### 2. `BarangDalamProsesController` still mixes unrelated flows
The controller now contains the intended proses flow, but also has leftover `edit`, `update`, and `store` methods that belong to `BarangMasuk` behavior, not `BarangDalamProses` behavior.

Relevant lines:
- `app/Http/Controllers/BarangDalamProsesController.php:229`
- `app/Http/Controllers/BarangDalamProsesController.php:247`
- `app/Http/Controllers/BarangDalamProsesController.php:255`
- `app/Http/Controllers/BarangDalamProsesController.php:285`

Implication:
- These methods are dead or wrong for the current routing and should be removed or moved into the correct controller before modular refactor continues.
- Leaving them in place increases the chance of accidental route binding or future regressions.

### 3. `CustomerController` is still legacy-style and not modular-aware
`CustomerController` still uses a simple CRUD pattern and is not part of the modular split, which is fine for now, but it is not a refactor target until the inventory flows are stable.

Relevant line:
- `app/Http/Controllers/CustomerController.php:10`

Implication:
- No immediate blocker, but do not expand the modular refactor into customer management yet.

### 4. Modular models exist, but the controllers still do not use them
The new models are present for the modular split:
- `app/Models/Material.php`
- `app/Models/Product.php`
- `app/Models/MaterialOrder.php`
- `app/Models/ProductionItem.php`
- `app/Models/Shipment.php`

They already define the intended relations between the new tables, but the active controllers still read and write the legacy `barang`, `barang_masuk`, `barang_keluar`, and `barang_dalam_proses` tables.

Implication:
- The modular schema is scaffolded, not yet cut over.
- Backfill and parity checks still need to happen before any controller can safely switch to the new tables.
- The current live app is still anchored to the legacy schema.

### 5. Backfill tooling exists, but parity gates are still manual
The backfill command in `routes/console.php` already copies legacy rows into the modular tables with idempotent upserts. That is good for initial migration, but the current setup still lacks automated parity enforcement.

What is still missing:
- automated row-count parity checks after each table backfill
- automated orphan checks for modular foreign keys
- a fail-fast guard if source and target counts diverge
- a dedicated cutover switch that can freeze writes during final validation

Implication:
- The data movement path is prepared, but the safety gates are still mostly documented rather than enforced in code.
- The migration is not yet in a final operational state, because it still depends on manual validation discipline.

## Recommended Next Step
1. Remove or relocate the leftover `BarangDalamProsesController` methods that actually handle barang masuk behavior.
2. Keep PostgreSQL as the test database until all migrations are rewritten to be dialect-safe.
3. Backfill and parity-check the modular tables before wiring controllers to them.
4. Add automated parity checks around the backfill command before any controller switch.
5. Continue the modular refactor only after the controller surface is clean and one flow owns one controller.
