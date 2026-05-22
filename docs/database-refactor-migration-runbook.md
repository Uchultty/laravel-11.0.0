# Database Refactor Migration Runbook (Non-Destructive)

## Objective
Menjalankan refactor schema secara aman dari struktur legacy ke struktur modular tanpa kehilangan data dan tanpa downtime panjang.

## Golden Rules
- Jangan drop tabel legacy di fase awal.
- Jangan rename destruktif tabel yang dipakai aplikasi aktif.
- Semua migrasi harus reversible atau punya rollback plan yang jelas.
- Validasi parity data di setiap tahap.

## Phase 0 - Preparation
1. Backup full database (schema + data).
2. Freeze branch migration.
3. Catat baseline counts:
   - barang
   - barang_masuk
   - barang_dalam_proses
   - barang_keluar
   - customers
   - suppliers
   - users
4. Aktifkan log SQL/migration di environment staging.

Suggested commands:
```powershell
php artisan migrate:status
php artisan config:clear
php artisan cache:clear
```

## Phase 1 - Create New Modular Tables
Create migrations only (non-destructive):
- create_materials_table
- create_products_table
- create_material_orders_table
- create_production_items_table
- create_shipments_table

Mandatory columns for traceability:
- legacy_*_id unique nullable columns
- timestamps
- foreign keys ke master terkait

Do not touch:
- barang
- barang_masuk
- barang_dalam_proses
- barang_keluar

## Phase 2 - Backfill Data
Implement idempotent data migration command (Artisan command recommended):
- Upsert based on legacy ID to avoid duplicate on rerun.

Backfill order:
1. materials/products from barang
2. material_orders from barang_masuk
3. production_items from barang_dalam_proses
4. shipments from barang_keluar

Checks after each table:
- row count parity
- sample random record parity
- null critical fields check
- orphan FK check

## Phase 3 - Reconciliation & Data Quality Gates
Create SQL validation set:

1. Count parity
```sql
-- contoh
select count(*) from barang where status='material';
select count(*) from materials;
```

2. Qty parity
```sql
select sum(quantity) from barang where status='material';
select sum(stok_saat_ini) from materials;
```

3. Missing document parity
```sql
select count(*) from barang_keluar where invoice_path is not null;
select count(*) from shipments where invoice_path is not null;
```

4. Orphan check
```sql
select s.id_pengiriman
from shipments s
left join production_items p on p.id_barang_proses = s.id_barang_proses
where p.id_barang_proses is null;
```

Gate policy:
- Selisih count harus 0.
- Selisih sum qty harus 0 (atau dijelaskan jika memang rule berbeda).
- Orphan critical relation harus 0.

## Phase 4 - Dual Read Mode (No Controller Rewrite Big-Bang)
Refactor bertahap per modul:
1. Read path reports/dashboard via repository/service layer.
2. Jalankan dual-read compare:
   - baca dari legacy
   - baca dari tabel baru
   - log mismatch
3. Setelah mismatch = 0 selama window observasi, ubah read default ke tabel baru.

## Phase 5 - Write Path Transition
Per modul, lakukan:
1. Write to new table.
2. Optional mirror write ke legacy sementara (safety window).
3. Reconciliation harian otomatis.

Prioritas modul:
1. Master data material/produk
2. Barang masuk/material order
3. Produksi
4. Pengiriman
5. Dashboard + laporan

## Phase 6 - Role Access Hardening
Target route policy:
- Admin: full access.
- Supervisor:
  - allow: dashboard, laporan, barang-dalam-proses (read only), pengiriman (read only)
  - deny: data supplier, data customer, data material, data produk, users, seluruh create/update/delete

Technical steps:
- Buat route group read-only supervisor.
- Pisahkan endpoint write ke middleware admin only.
- Tambah integration test 403 untuk akses URL langsung supervisor.

## Phase 7 - Laravel Refactor Checklist
1. Models:
   - Material, Product, MaterialOrder, ProductionItem, Shipment
   - explicit custom key relations
2. Controllers:
   - ganti query dari barang* ke entitas modular
3. Requests:
   - pindahkan inline validate ke FormRequest bertahap
4. Views:
   - sesuaikan field naming
5. Reports:
   - cek metric parity dashboard/laporan
6. Middleware/policy:
   - lock supervisor read-only

## Phase 8 - Testing Checklist
Functional:
- CRUD admin all modules
- produksi flow (reserve/cancel/finish)
- shipment flow + dokumen
- dashboard metrics
- laporan material/pengiriman

Security:
- supervisor forbidden endpoints (403)
- supervisor cannot POST/PUT/PATCH/DELETE

Data:
- parity queries pass
- no orphan FK
- no missing critical docs path due migration

## Phase 9 - Decommission Plan (Optional, Last)
Only after stable period + sign-off:
1. freeze writes to legacy
2. final reconciliation
3. archive snapshot
4. mark legacy tables read-only
5. drop only with explicit approval

## Rollback Strategy
- If mismatch found:
  - switch read back to legacy path
  - pause new writes
  - rerun backfill idempotent after fix
- Keep legacy intact until full cutover validation complete.

## Deliverables Expected Before Coding Cutover
- Approved final data dictionary
- Approved migration SQL/Artisan scripts
- Approved access-control matrix
- Approved UAT checklist